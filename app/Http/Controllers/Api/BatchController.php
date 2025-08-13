<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBatchRequest;
use App\Http\Requests\UpdateBatchRequest;
use App\Http\Resources\BatchResource;
use App\Http\Resources\QrAccessLogResource;
use App\Models\Batch;
use App\Models\Product;
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
    public function store(Request $request): BatchResource
    {
        $batch = Batch::create($request->all() + [
            'customer_id' => $request->user()->id,
            'batch_code' => 'BATCH-' . random_int(100000, 999999),
        ]);

        // Generate QR code
        $this->qrCodeService->generateQrCode($batch);

        return new BatchResource($batch->load(['customer', 'product']));
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
}
