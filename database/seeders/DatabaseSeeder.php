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
            'password_hash' => bcrypt('123456'),
            'role' => 'farmer',
            'phone_number' => '0123456789',
            'address' => 'Hanoi, Vietnam',
            'profile_image' => 'https://ui-avatars.com/api/?name=Main+Farmer&background=random&size=200'
        ]);

        // Create additional farmers and cooperatives
        // Create additional farmers with UI Avatars
        $farmerNames = [
            'John Smith',
            'Maria Garcia',
            'David Lee',
            'Sarah Johnson',
            'Michael Brown'
        ];

        foreach ($farmerNames as $name) {
            Customer::factory()->create([
                'full_name' => $name,
                'role' => 'farmer',
                'profile_image' => 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&background=random&size=200'
            ]);
        }
        // Create cooperatives with UI Avatars
        $cooperativeNames = [
            'Green Farm Co-op',
            'Fresh Valley Alliance',
            'Eco Harvest Group'
        ];

        foreach ($cooperativeNames as $name) {
            Customer::factory()->create([
                'full_name' => $name,
                'role' => 'cooperative',
                'profile_image' => 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&background=random&size=200'
            ]);
        }

        // Create fruit categories
        $fruitCategories = [
            ['name' => 'Citrus Fruits', 'description' => 'Fruits with high citric acid content'],
            ['name' => 'Berries', 'description' => 'Small, juicy, and often edible fruits'],
            ['name' => 'Tropical Fruits', 'description' => 'Fruits that grow in tropical regions'],
            ['name' => 'Stone Fruits', 'description' => 'Fruits with pits'],
            ['name' => 'Pome Fruits', 'description' => 'Fruits with core containing seeds']
        ];

        foreach ($fruitCategories as $category) {
            Category::create($category);
        }

        // Create products with real fruit data
        $fruits = [
            'Citrus Fruits' => [
                ['name' => 'Orange', 'description' => 'Sweet and juicy citrus fruit rich in vitamin C'],
                ['name' => 'Lemon', 'description' => 'Tart citrus fruit used for flavoring and cooking'],
                ['name' => 'Lime', 'description' => 'Small green citrus fruit with sharp, sour taste'],
                ['name' => 'Grapefruit', 'description' => 'Large citrus fruit with a sweet-tart flavor']
            ],
            'Berries' => [
                ['name' => 'Strawberry', 'description' => 'Sweet red berry with seeds on the outside'],
                ['name' => 'Blueberry', 'description' => 'Small, sweet blue-purple berry rich in antioxidants'],
                ['name' => 'Raspberry', 'description' => 'Sweet-tart red berry with a hollow core'],
                ['name' => 'Blackberry', 'description' => 'Deep purple-black berry with rich, sweet flavor']
            ],
            'Tropical Fruits' => [
                ['name' => 'Banana', 'description' => 'Sweet, curved yellow fruit with soft flesh'],
                ['name' => 'Mango', 'description' => 'Sweet, juicy tropical fruit with orange flesh'],
                ['name' => 'Pineapple', 'description' => 'Sweet-tart tropical fruit with yellow flesh'],
                ['name' => 'Papaya', 'description' => 'Sweet tropical fruit with orange flesh and black seeds']
            ],
            'Stone Fruits' => [
                ['name' => 'Cherry', 'description' => 'Small, round red fruit with a single pit'],
                ['name' => 'Peach', 'description' => 'Sweet, fuzzy fruit with juicy yellow flesh'],
                ['name' => 'Plum', 'description' => 'Sweet-tart purple fruit with yellow flesh'],
                ['name' => 'Apricot', 'description' => 'Small orange fruit with sweet-tart flavor']
            ],
            'Pome Fruits' => [
                ['name' => 'Apple', 'description' => 'Crisp, sweet fruit with white flesh'],
                ['name' => 'Pear', 'description' => 'Sweet, juicy fruit with soft flesh'],
                ['name' => 'Quince', 'description' => 'Tart fruit often used in preserves and cooking']
            ]
        ];

        foreach ($fruits as $categoryName => $products) {
            $category = Category::where('name', $categoryName)->first();
            foreach ($products as $product) {
                $newProduct = Product::create([
                    'category_id' => $category->id,
                    'name' => $product['name'],
                    'description' => $product['description']
                ]);

                // Create a batch for this product with more detailed information
                $plantingDate = now()->subMonths(rand(3, 6));
                $harvestDate = $plantingDate->copy()->addMonths(rand(2, 4));

                $batch = Batch::create([
                    'customer_id' => Customer::where('role', 'farmer')->first()->id,
                    'product_id' => $newProduct->id,
                    'batch_code' => 'BATCH-' . strtoupper(substr($product['name'], 0, 3)) . '-' . random_int(1000, 9999),
                    'status' => 'active',
                    'weight' => rand(100, 1000),
                    'variety' => $product['name'] . ' ' . ['Premium', 'Standard', 'Special', 'Organic'][rand(0, 3)],
                    'planting_date' => $plantingDate,
                    'harvest_date' => $harvestDate,
                    'cultivation_method' => ['Organic', 'Conventional', 'Hydroponic', 'Greenhouse'][rand(0, 3)],
                    'location' => $location = array_rand([
                        'Hanoi' => '21.028511,105.804817', // Thanh Xuân, Hà Nội
                        'Ho Chi Minh City' => '10.762622,106.660172', // Quận 1, TP.HCM
                        'Da Nang' => '16.054407,108.202164', // Hải Châu, Đà Nẵng
                        'Can Tho' => '10.045162,105.746857', // Ninh Kiều, Cần Thơ
                        'Hai Phong' => '20.844912,106.688084', // Hồng Bàng, Hải Phòng
                        'Lam Dong' => '11.946403,108.442383', // Đà Lạt, Lâm Đồng
                        'Son La' => '21.327711,103.91929', // Mộc Châu, Sơn La
                        'Bac Giang' => '21.275714,106.195555', // Lục Ngạn, Bắc Giang
                    ]),
                    'gps_coordinates' => [
                        'Hanoi' => '21.028511,105.804817',
                        'Ho Chi Minh City' => '10.762622,106.660172',
                        'Da Nang' => '16.054407,108.202164',
                        'Can Tho' => '10.045162,105.746857',
                        'Hai Phong' => '20.844912,106.688084',
                        'Lam Dong' => '11.946403,108.442383',
                        'Son La' => '21.327711,103.91929',
                        'Bac Giang' => '21.275714,106.195555'
                    ][$location],
                    'certification_number' => 'CERT-' . random_int(10000, 99999),
                    'certification_expiry' => now()->addMonths(rand(6, 24)),
                    'water_usage' => rand(100, 1000) . ' liters/kg',
                    'carbon_footprint' => rand(1, 10) . ' kg CO2/kg',
                    'pesticide_usage' => 'None',
                    'qr_code' => 'QR-' . strtoupper(substr($product['name'], 0, 3)) . '-' . random_int(1000, 9999),
                    'qr_expiry' => now()->addYears(1)
                ]);
                $fruitImages = [
                    'Orange' => 'https://images.unsplash.com/photo-1557800636-894a64c1696f?w=800',
                    'Lemon' => 'https://images.unsplash.com/photo-1582087463261-ddea03f80e5d?w=800',
                    'Lime' => 'https://upload.wikimedia.org/wikipedia/commons/e/e7/Lime_-_whole_and_halved.jpg',
                    'Grapefruit' => 'https://images.unsplash.com/photo-1577234286642-fc512a5f8f11?w=800',
                    'Strawberry' => 'https://images.unsplash.com/photo-1518635017498-87f514b751ba?w=800',
                    'Blueberry' => 'https://images.unsplash.com/photo-1498557850523-fd3d118b962e?w=800',
                    'Raspberry' => 'https://images.unsplash.com/photo-1577069861033-55d04cec4ef5?w=800',
                    'Blackberry' => 'https://images.unsplash.com/photo-1615218370629-da07db3571a4?w=800',
                    'Banana' => 'https://images.unsplash.com/photo-1571771894821-ce9b6c11b08e?w=800',
                    'Mango' => 'https://images.unsplash.com/photo-1553279768-865429fa0078?w=800',
                    'Pineapple' => 'https://images.unsplash.com/photo-1550258987-190a2d41a8ba?w=800',
                    'Papaya' => 'https://images.unsplash.com/photo-1617112848923-cc2234396a8d?w=800',
                    'Cherry' => 'https://images.unsplash.com/photo-1559181567-c3190ca9959b?w=800',
                    'Peach' => 'https://images.unsplash.com/photo-1595145610550-6d696e3714e3?w=800',
                    'Plum' => 'https://images.unsplash.com/photo-1603408209093-cd3c9af497d6?w=800',
                    'Apricot' => 'https://images.unsplash.com/photo-1600592068687-98de4c851c76?w=800',
                    'Apple' => 'https://images.unsplash.com/photo-1560806887-1e4cd0b6cbd6?w=800',
                    'Pear' => 'https://images.unsplash.com/photo-1601876813368-7962dc48cfab?w=800',
                    'Quince' => 'https://images.unsplash.com/photo-1597060330077-69b8f71346fc?w=800'
                ];
                // Create product image for the batch
                BatchImage::create([
                    'batch_id' => $batch->id,
                    'image_url' => $fruitImages[$product['name']] ?? 'https://images.unsplash.com/photo-1610832958506-aa56368176cf?w=800',
                    'image_type' => 'product'
                ]);
            }
        }

        // Possible statuses
        $statuses = ['active', 'completed', 'cancelled'];

        // Possible pesticide usages
        $pesticideUsages = ['None', 'Low', 'Medium', 'Controlled'];

        // Create batches with related data
        // Danh sách vị trí canh tác thực tế ở Việt Nam
        $locations = [
            'Hanoi' => '21.028511,105.804817', // Thanh Xuân, Hà Nội
            'Ho Chi Minh City' => '10.762622,106.660172', // Quận 1, TP.HCM
            'Da Nang' => '16.054407,108.202164', // Hải Châu, Đà Nẵng
            'Can Tho' => '10.045162,105.746857', // Ninh Kiều, Cần Thơ
            'Hai Phong' => '20.844912,106.688084', // Hồng Bàng, Hải Phòng
            'Lam Dong' => '11.946403,108.442383', // Đà Lạt, Lâm Đồng
            'Son La' => '21.327711,103.91929', // Mộc Châu, Sơn La
            'Bac Giang' => '21.275714,106.195555', // Lục Ngạn, Bắc Giang
        ];

        Product::all()->each(function ($product) use ($statuses, $pesticideUsages, $locations) {
            // Get random farmers
            $farmers = Customer::where('role', 'farmer')->inRandomOrder()->take(3)->get();

            // Create 1-2 batches per farmer for each product
            foreach ($farmers as $farmer) {
                for ($i = 0; $i < rand(1, 2); $i++) {
                    $plantingDate = now()->subMonths(rand(3, 6));
                    $harvestDate = $plantingDate->copy()->addMonths(rand(2, 4));
                    $location = array_rand($locations);

                    $batch = Batch::create([
                        'customer_id' => $farmer->id,
                        'product_id' => $product->id,
                        'batch_code' => 'BATCH-' . strtoupper(substr($product->name, 0, 3)) . '-' . random_int(1000, 9999),
                        'status' => Arr::random($statuses),
                        'weight' => rand(100, 1000),
                        'variety' => $product->name . ' ' . ['Premium', 'Standard', 'Special', 'Organic'][rand(0, 3)],
                        'planting_date' => $plantingDate,
                        'harvest_date' => $harvestDate,
                        'cultivation_method' => ['Organic', 'Conventional', 'Hydroponic', 'Greenhouse'][rand(0, 3)],
                        'location' => $location,
                        'gps_coordinates' => $locations[$location],
                        'certification_number' => 'CERT-' . random_int(10000, 99999),
                        'certification_expiry' => now()->addMonths(rand(6, 24)),
                        'water_usage' => rand(100, 1000) . ' liters/kg',
                        'carbon_footprint' => rand(1, 10) . ' kg CO2/kg',
                        'pesticide_usage' => Arr::random($pesticideUsages),
                        'qr_code' => 'QR-' . strtoupper(substr($product->name, 0, 3)) . '-' . random_int(1000, 9999),
                        'qr_expiry' => now()->addYears(1)
                    ]);

                    // Create images for each batch
                    // Farm image
                    BatchImage::factory()->farm()->create(['batch_id' => $batch->id]);

                    // Product image from real fruit photos
                    $fruitImages = [
                        'Orange' => 'https://images.unsplash.com/photo-1557800636-894a64c1696f?w=800',
                        'Lemon' => 'https://images.unsplash.com/photo-1582087463261-ddea03f80e5d?w=800',
                        'Lime' => 'https://images.unsplash.com/photo-1596840742963-13c7a7b7c603?w=800',
                        'Grapefruit' => 'https://images.unsplash.com/photo-1577234286642-fc512a5f8f11?w=800',
                        'Strawberry' => 'https://images.unsplash.com/photo-1518635017498-87f514b751ba?w=800',
                        'Blueberry' => 'https://images.unsplash.com/photo-1498557850523-fd3d118b962e?w=800',
                        'Raspberry' => 'https://images.unsplash.com/photo-1577069861033-55d04cec4ef5?w=800',
                        'Blackberry' => 'https://images.unsplash.com/photo-1615218370629-da07db3571a4?w=800',
                        'Banana' => 'https://images.unsplash.com/photo-1571771894821-ce9b6c11b08e?w=800',
                        'Mango' => 'https://images.unsplash.com/photo-1553279768-865429fa0078?w=800',
                        'Pineapple' => 'https://images.unsplash.com/photo-1550258987-190a2d41a8ba?w=800',
                        'Papaya' => 'https://images.unsplash.com/photo-1617112848923-cc2234396a8d?w=800',
                        'Cherry' => 'https://images.unsplash.com/photo-1559181567-c3190ca9959b?w=800',
                        'Peach' => 'https://images.unsplash.com/photo-1595145610550-6d696e3714e3?w=800',
                        'Plum' => 'https://images.unsplash.com/photo-1603408209093-cd3c9af497d6?w=800',
                        'Apricot' => 'https://images.unsplash.com/photo-1600592068687-98de4c851c76?w=800',
                        'Apple' => 'https://images.unsplash.com/photo-1560806887-1e4cd0b6cbd6?w=800',
                        'Pear' => 'https://images.unsplash.com/photo-1601876813368-7962dc48cfab?w=800',
                        'Quince' => 'https://images.unsplash.com/photo-1597060330077-69b8f71346fc?w=800'
                    ];

                    BatchImage::create([
                        'batch_id' => $batch->id,
                        'image_type' => 'product',
                        'image_url' => $fruitImages[$product->name] ?? 'https://images.unsplash.com/photo-1610832958506-aa56368176cf?w=800' // default fruit image
                    ]);

                    // Farmer image using UI Avatars
                    BatchImage::create([
                        'batch_id' => $batch->id,
                        'image_type' => 'farmer',
                        'image_url' => 'https://ui-avatars.com/api/?name=' . urlencode($farmer->full_name) . '&background=random&size=200'
                    ]);

                    // Create reviews with a mix of ratings
                    $reviewCount = rand(5, 15);
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
                    $totalDays = 60; // Last 3 months
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
                }
            }
        });
    }
}
