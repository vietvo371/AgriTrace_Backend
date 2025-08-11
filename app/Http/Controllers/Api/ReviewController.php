<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReviewRequest;
use App\Http\Requests\UpdateReviewRequest;
use App\Http\Resources\ReviewResource;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Display a listing of the reviews.
     */
    public function index(): AnonymousResourceCollection
    {
        $reviews = Review::with(['batch', 'customer'])
            ->latest()
            ->paginate();

        return ReviewResource::collection($reviews);
    }

    /**
     * Store a newly created review in storage.
     */
    public function store(StoreReviewRequest $request): ReviewResource
    {
        $review = Review::create($request->validated() + [
            'customer_id' => Auth::id(),
        ]);

        return new ReviewResource($review->load(['batch', 'customer']));
    }

    /**
     * Display the specified review.
     */
    public function show(Review $review): ReviewResource
    {
        return new ReviewResource($review->load(['batch', 'customer']));
    }

    /**
     * Update the specified review in storage.
     */
    public function update(UpdateReviewRequest $request, Review $review): ReviewResource
    {
        $this->authorize('update', $review);

        $review->update($request->validated());

        return new ReviewResource($review->load(['batch', 'customer']));
    }

    /**
     * Remove the specified review from storage.
     */
    public function destroy(Review $review): JsonResponse
    {
        $this->authorize('delete', $review);

        $review->delete();

        return response()->json([
            'message' => 'Review deleted successfully',
        ]);
    }
}
