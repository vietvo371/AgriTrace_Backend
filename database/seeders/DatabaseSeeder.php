<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use App\Models\Category;
use App\Models\Product;
use App\Models\Batch;
use App\Models\Review;
use App\Models\BatchImage;
use App\Models\Customer;
use App\Models\QrAccessLog;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create farmers and cooperatives
        // Create one main farmer account
        Customer::factory()->create([
            'full_name' => 'Main Farmer',
            'email' => 'farmer@example.com',
            'password_hash' => bcrypt('password'),
            'role' => 'farmer',
            'phone_number' => '0123456789',
            'address' => 'Hanoi, Vietnam'
        ]);

        // Create additional farmers and cooperatives
        Customer::factory(5)->farmer()->create();
        Customer::factory(3)->cooperative()->create();

        // Create categories
        Category::factory(6)->create();

        // Create products (3-5 products per category)
        Category::all()->each(function ($category) {
            Product::factory(rand(3, 5))->create([
                'category_id' => $category->id
            ]);
        });

        // Possible statuses
        $statuses = ['active', 'completed', 'cancelled'];

        // Possible pesticide usages
        $pesticideUsages = ['None', 'Low', 'Medium', 'Controlled'];

        // Create batches with related data
        Product::all()->each(function ($product) use ($statuses, $pesticideUsages) {
            // Get random farmers
            $farmers = Customer::where('role', 'farmer')->inRandomOrder()->take(3)->get();

            // Create 2-4 batches per product
            foreach ($farmers as $farmer) {
                Batch::factory(rand(1, 2))->create([
                    'customer_id' => $farmer->id,
                    'product_id' => $product->id,
                    'status' => Arr::random($statuses),
                    'certification_number' => 'CERT-' . random_int(10000, 99999),
                    'certification_expiry' => now()->addMonths(rand(6, 24)),
                    'water_usage' => rand(100, 1000) . ' liters/kg',
                    'carbon_footprint' => rand(1, 10) . ' kg CO2/kg',
                    'pesticide_usage' => Arr::random($pesticideUsages)
                ])->each(function ($batch) {
                    // Create required images for each batch
                    BatchImage::factory()->farm()->create(['batch_id' => $batch->id]);
                    BatchImage::factory()->product()->create(['batch_id' => $batch->id]);

                    // 70% chance to have farmer image
                    if (rand(1, 100) <= 70) {
                        BatchImage::factory()->farmer()->create(['batch_id' => $batch->id]);
                    }

                    // Create reviews with a mix of ratings
                    $reviewCount = rand(5, 15);
                    // 70% positive reviews, 30% mixed
                    $positiveCount = ceil($reviewCount * 0.7);
                    $mixedCount = $reviewCount - $positiveCount;

                    Review::factory()->positive()->count($positiveCount)->create([
                        'batch_id' => $batch->id,
                        'created_at' => now()->subDays(rand(1, 90))
                    ]);

                    Review::factory()->mixed()->count($mixedCount)->create([
                        'batch_id' => $batch->id,
                        'created_at' => now()->subDays(rand(1, 90))
                    ]);

                    // Create QR access logs with realistic patterns
                    $totalDays = 90; // Last 3 months
                    for ($day = 0; $day < $totalDays; $day++) {
                        $date = now()->subDays($day);
                        $scanCount = rand(0, 5); // 0-5 scans per day

                        for ($i = 0; $i < $scanCount; $i++) {
                            $accessTime = $date->copy()->addHours(rand(8, 20));
                            QrAccessLog::factory()->create([
                                'batch_id' => $batch->id,
                                'access_time' => $accessTime,
                                'created_at' => $accessTime,
                                'updated_at' => $accessTime
                            ]);
                        }
                    }
                });
            }
        });
    }
}
