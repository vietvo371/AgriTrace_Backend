<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBatchRequest;
use App\Http\Requests\UpdateBatchRequest;
use App\Http\Resources\BatchResource;
use App\Models\Batch;
use App\Services\QrCodeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Jenssegers\Agent\Agent;
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
        $batches = Batch::with(['user', 'product', 'reviews', 'images'])
            ->latest()
            ->paginate();

        return BatchResource::collection($batches);
    }

    /**
     * Store a newly created batch in storage.
     */
    public function store(StoreBatchRequest $request): BatchResource
    {
        $batch = Batch::create($request->validated() + [
            'user_id' => Auth::id(),
        ]);

        // Generate QR code
        $this->qrCodeService->generateQrCode($batch);

        return new BatchResource($batch->load(['user', 'product']));
    }

    /**
     * Display the specified batch.
     */
    public function show(Request $request, Batch $batch): JsonResponse
    {
        // Check if this is a QR code scan
        if ($request->has('scan')) {
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
        }

        return response()->json(
            new BatchResource($batch->load([
                'user',
                'product',
                'reviews.customer',
                'images',
                'accessLogs'
            ]))
        );
    }

    /**
     * Update the specified batch in storage.
     */
    public function update(UpdateBatchRequest $request, Batch $batch): BatchResource
    {
        $batch->update($request->validated());

        return new BatchResource($batch->load(['user', 'product']));
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
}
