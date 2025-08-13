<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BatchResource;
use App\Models\Batch;
use App\Models\Product;
use App\Models\QrAccessLog;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DashboardController extends Controller
{
    public function stats(): JsonResponse
    {
        $now = Carbon::now();
        $lastMonth = $now->copy()->subMonth();

        // Batches stats
        $totalBatches = Batch::count();
        $lastMonthBatches = Batch::where('created_at', '<=', $lastMonth)->count();
        $batchesTrend = $this->calculateTrend($totalBatches, $lastMonthBatches);

        // QR scans stats
        $totalScans = QrAccessLog::count();
        $lastMonthScans = QrAccessLog::where('created_at', '<=', $lastMonth)->count();
        $scansTrend = $this->calculateTrend($totalScans, $lastMonthScans);

        // Products stats
        $totalProducts = Product::count();
        $lastMonthProducts = Product::where('created_at', '<=', $lastMonth)->count();
        $productsTrend = $this->calculateTrend($totalProducts, $lastMonthProducts);

        return response()->json([
            'batches' => [
                'total' => $totalBatches,
                'trend' => $batchesTrend
            ],
            'qr_scans' => [
                'total' => $totalScans,
                'trend' => $scansTrend
            ],
            'products' => [
                'total' => $totalProducts,
                'trend' => $productsTrend
            ]
        ]);
    }

    private function calculateTrend(int $current, int $previous): array
    {
        if ($previous === 0) {
            return [
                'value' => 100,
                'isPositive' => true
            ];
        }

        $change = (($current - $previous) / $previous) * 100;
        return [
            'value' => abs(round($change, 1)),
            'isPositive' => $change >= 0
        ];
    }

    public function recentBatches(): JsonResponse
    {
        $batches = Batch::with(['product.category'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($batch) {
                return [
                    'id' => $batch->id,
                    'product_name' => $batch->product->name,
                    'category' => $batch->product->category->name,
                    'weight' => $batch->weight,
                    'harvest_date' => $batch->harvest_date,
                    'cultivation_method' => $batch->cultivation_method,
                    'status' => $batch->status,
                    'image' => $batch->images->first()?->url ?? null,
                ];
            });

        return response()->json([
            'batches' => $batches
        ]);
    }
    public function dashboardBatches(): AnonymousResourceCollection
    {
        $batches = Batch::with(['customer', 'product', 'reviews', 'images'])
            ->latest()
            ->take(5)
            ->get();

        return BatchResource::collection($batches);
    }
}
