<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBatchRequest;
use App\Http\Requests\UpdateBatchRequest;
use App\Http\Resources\BatchResource;
use App\Http\Resources\QrAccessLogResource;
use App\Models\Batch;
use App\Models\Product;
use App\Models\Category;
use App\Services\QrCodeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class BatchController extends Controller
{
    protected QrCodeService $qrCodeService;

    public function __construct(QrCodeService $qrCodeService)
    {
        $this->qrCodeService = $qrCodeService;
    }

    /**
     * Display a listing of the batches.
     */
    public function index(): AnonymousResourceCollection
    {
        $batches = Batch::with(['customer', 'product', 'reviews', 'images'])
            ->latest()
            ->paginate();

        return BatchResource::collection($batches);
    }

    /**
     * Store a newly created batch in storage.
     */
    public function store(StoreBatchRequest $request): BatchResource
    {
        // Find or create product based on category and product name
        $product = Product::firstOrCreate(
            ['name' => $request->product_name, 'category_id' => $request->category_id],
            ['description' => '']
        );

        // Create batch
        $batch = Batch::create([
            'customer_id' => $request->user()->id,
            'product_id' => $product->id,
            'batch_code' => 'BATCH-' . random_int(100000, 999999),
            'weight' => $request->weight,
            'variety' => $request->variety,
            'planting_date' => $request->planting_date,
            'harvest_date' => $request->harvest_date,
            'cultivation_method' => $request->cultivation_method,
            'location' => $request->location['latitude'] . ',' . $request->location['longitude'],
            'gps_coordinates' => json_encode($request->location),
        //     'certification_number' => $request->certification_number,
        //     'certification_expiry' => $request->certification_expiry,
        //     'water_usage' => $request->water_usage,
        //     'carbon_footprint' => $request->carbon_footprint,
        //     'pesticide_usage' => $request->pesticide_usage,
        ]);

        // Handle image uploads
        $images = [];

        if ($request->hasFile('farm_image')) {
            $farmImagePath = $request->file('farm_image')->store('batch-images', 'public');
            $images[] = ['image_url' => $farmImagePath, 'image_type' => 'farm'];
        }

        if ($request->hasFile('product_image')) {
            $productImagePath = $request->file('product_image')->store('batch-images', 'public');
            $images[] = ['image_url' => $productImagePath, 'image_type' => 'product'];
        }

        if ($request->hasFile('farmer_image')) {
            $farmerImagePath = $request->file('farmer_image')->store('batch-images', 'public');
            $images[] = ['image_url' => $farmerImagePath, 'image_type' => 'farmer'];
        }

        // Create batch images
        if (!empty($images)) {
            $batch->images()->createMany($images);
        }

        // Generate QR code
        $this->qrCodeService->generateQrCode($batch);

        return new BatchResource($batch->load(['customer', 'product', 'images']));
    }

    /**
     * Display the specified batch.
     */
    public function show(Batch $batch): BatchResource
    {
        return new BatchResource($batch->load([
            'customer',
            'product',
            'reviews.customer',
            'images',
            'accessLogs'
        ]));
    }

    /**
     * Update the specified batch in storage.
     */
    public function update(UpdateBatchRequest $request, Batch $batch): BatchResource
    {
        $batch->update($request->validated());

        return new BatchResource($batch->load(['customer', 'product']));
    }

    /**
     * Remove the specified batch from storage.
     */
    public function destroy(Batch $batch): JsonResponse
    {
        $batch->delete();

        return response()->json([
            'message' => 'Batch deleted successfully',
        ]);
    }

    /**
     * Regenerate QR code for the specified batch.
     */
    public function regenerateQr(Batch $batch): JsonResponse
    {
        $qrPath = $this->qrCodeService->generateQrCode($batch);

        return response()->json([
            'message' => 'QR code regenerated successfully',
            'qr_code' => $qrPath,
            'qr_expiry' => $batch->qr_expiry,
        ]);
    }

    /**
     * Display QR code information for public access.
     */
    public function showQr(Request $request, Batch $batch): JsonResponse
    {
        // Check if QR code is valid
        if (!$this->qrCodeService->isQrCodeValid($batch)) {
            return response()->json([
                'message' => 'QR code has expired',
            ], 410); // Gone
        }

        // Log access
        $agent = new Agent();
        $this->qrCodeService->logQrAccess(
            $batch,
            $request->ip(),
            $agent->platform() . ' ' . $agent->browser()
        );

        // Return batch information
        return response()->json([
            'data' => new BatchResource($batch->load([
                'customer' => fn($query) => $query->select('id', 'full_name', 'role'),
                'product.category',
                'images' => fn($query) => $query->where('image_type', 'product'),
            ])),
            'qr_expiry' => $batch->qr_expiry,
        ]);
    }

    /**
     * Display access logs for the specified batch.
     */
    public function accessLogs(Batch $batch): AnonymousResourceCollection
    {
        // Ensure user can access logs
        $this->authorize('viewAccessLogs', $batch);

        return QrAccessLogResource::collection(
            $batch->accessLogs()
                ->latest()
                ->paginate()
        );
    }

        public function details(Batch $batch): JsonResponse
    {
        $batch->load([
            'product.category',
            'images',
            'customer',
            'reviews.customer',
            'accessLogs'
        ]);

        // Parse GPS coordinates from stored format
        $gpsCoordinates = json_decode($batch->gps_coordinates, true);

        // Group images by type
        $images = [
            'farm' => null,
            'product' => null,
            'farmer' => null
        ];
        foreach ($batch->images as $image) {
            $images[$image->image_type] = asset('storage/' . $image->image_url);
        }

        // Calculate stats
        $totalScans = $batch->accessLogs()->count();
        $uniqueCustomers = $batch->accessLogs()->distinct('ip_address')->count();
        $averageRating = $batch->reviews()->avg('rating') ?? 0;

        // Format reviews
        $reviews = $batch->reviews->take(5)->map(function ($review) {
            return [
                'id' => (string)$review->id,
                'reviewer' => [
                    'name' => $review->customer->full_name ?? 'Anonymous',
                    'avatar' => $review->customer->profile_image ?? null
                ],
                'rating' => (float)$review->rating,
                'comment' => $review->comment,
                'date' => $review->created_at->format('Y-m-d H:i:s')
            ];
        });

        // Format the response according to the required structure
        return response()->json([
            'data' => [
                'id' => (string)$batch->id,
                'product_name' => $batch->product->name,
                'category' => $batch->product->category->name,
                'weight' => (float)$batch->weight,
                'variety' => $batch->variety,
                'planting_date' => $batch->planting_date,
                'harvest_date' => $batch->harvest_date,
                'cultivation_method' => $batch->cultivation_method,
                'status' => $batch->status ?? 'active',
                'location' => [
                    'latitude' => $gpsCoordinates['latitude'] ?? 0,
                    'longitude' => $gpsCoordinates['longitude'] ?? 0,
                    'address' => $batch->customer->address
                ],
                'images' => $images,
                'traceability' => [
                    'batch_code' => $batch->batch_code,
                    'packaging_date' => $batch->created_at->format('Y-m-d'),
                    'best_before' => $batch->qr_expiry
                ],
                'stats' => [
                    'total_scans' => $totalScans,
                    'unique_customers' => $uniqueCustomers,
                    'average_rating' => round($averageRating, 1)
                ],
                'farmer' => [
                    'name' => $batch->customer->full_name ?? 'N/A',
                    'phone' => $batch->customer->phone_number ?? 'N/A',
                    'email' => $batch->customer->email ?? 'N/A'
                ],
                'certification' => [
                    'number' => $batch->certification_number ?? 'N/A',
                    'validUntil' => $batch->certification_expiry ?? 'N/A'
                ],
                'sustainability' => [
                    'water_usage' => $batch->water_usage ?? 'N/A',
                    'carbon_footprint' => $batch->carbon_footprint ?? 'N/A',
                    'pesticide_usage' => $batch->pesticide_usage ?? 'N/A'
                ],
                'reviews' => $reviews
            ],
            'message' => 'Batch details retrieved successfully'
        ]);
    }

    public function allFarmerBatches(Request $request): JsonResponse
    {
        $query = Batch::where('customer_id', auth()->user()->id)
            ->with(['product.category', 'reviews', 'images', 'accessLogs']);

        // Apply search filter
        if ($request->has('search')) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        // Apply status filter
        if ($request->has('status') && in_array($request->status, ['active', 'completed', 'cancelled'])) {
            $query->where('status', $request->status);
        }

        // Apply sorting
        if ($request->sort === 'oldest') {
            $query->oldest();
        } else {
            $query->latest(); // default: newest
        }

        // Pagination
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 10);
        $batches = $query->paginate($limit, ['*'], 'page', $page);

        // Format the response
        $items = $batches->map(function ($batch) {
            $productImage = $batch->images->where('image_type', 'product')->first();

            return [
                'id' => (string)$batch->id,
                'product_name' => $batch->product->name,
                'category' => $batch->product->category->name,
                'weight' => (float)$batch->weight,
                'harvest_date' => $batch->harvest_date,
                'cultivation_method' => $batch->cultivation_method,
                'status' => $batch->status ?? 'active',
                'stats' => [
                    'total_scans' => $batch->accessLogs->count(),
                    'unique_customers' => $batch->accessLogs->unique('ip_address')->count(),
                    'average_rating' => round($batch->reviews->avg('rating') ?? 0, 1)
                ],
                'images' => [
                    'product' => $productImage ? asset('storage/' . $productImage->image_url) : null
                ]
            ];
        });

        return response()->json([
            'data' => [
                'items' => $items,
                'pagination' => [
                    'total' => $batches->total(),
                    'page' => $batches->currentPage(),
                    'limit' => $limit,
                    'total_pages' => $batches->lastPage()
                ]
            ],
            'message' => 'Farmer batches retrieved successfully'
        ]);
    }
}
