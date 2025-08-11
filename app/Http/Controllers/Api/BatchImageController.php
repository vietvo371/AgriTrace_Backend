<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBatchImageRequest;
use App\Http\Requests\UpdateBatchImageRequest;
use App\Http\Resources\BatchImageResource;
use App\Models\BatchImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BatchImageController extends Controller
{
    /**
     * Display a listing of the batch images.
     */
    public function index(): AnonymousResourceCollection
    {
        $images = BatchImage::with('batch')
            ->latest()
            ->paginate();

        return BatchImageResource::collection($images);
    }

    /**
     * Store a newly created batch image in storage.
     */
    public function store(StoreBatchImageRequest $request): BatchImageResource
    {
        $image = BatchImage::create($request->validated());

        return new BatchImageResource($image->load('batch'));
    }

    /**
     * Display the specified batch image.
     */
    public function show(BatchImage $batchImage): BatchImageResource
    {
        return new BatchImageResource($batchImage->load('batch'));
    }

    /**
     * Update the specified batch image in storage.
     */
    public function update(UpdateBatchImageRequest $request, BatchImage $batchImage): BatchImageResource
    {
        $batchImage->update($request->validated());

        return new BatchImageResource($batchImage->load('batch'));
    }

    /**
     * Remove the specified batch image from storage.
     */
    public function destroy(BatchImage $batchImage): JsonResponse
    {
        $batchImage->delete();

        return response()->json([
            'message' => 'Batch image deleted successfully',
        ]);
    }
}
