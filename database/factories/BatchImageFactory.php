<?php

namespace Database\Factories;

use App\Models\BatchImage;
use App\Models\Batch;
use Illuminate\Database\Eloquent\Factories\Factory;

class BatchImageFactory extends Factory
{
    protected $model = BatchImage::class;

    public function definition(): array
    {
        $types = ['farm', 'product', 'farmer'];
        $type = $this->faker->randomElement($types);

        // Sample images for each type
        $images = [
            'farm' => [
                'batch-images/farm_field.jpg',
                'batch-images/farm_greenhouse.jpg',
                'batch-images/farm_aerial.jpg',
                'batch-images/farm_equipment.jpg'
            ],
            'product' => [
                'batch-images/product_fresh.jpg',
                'batch-images/product_packaged.jpg',
                'batch-images/product_display.jpg',
                'batch-images/product_closeup.jpg'
            ],
            'farmer' => [
                'batch-images/farmer_working.jpg',
                'batch-images/farmer_portrait.jpg',
                'batch-images/farmer_harvesting.jpg',
                'batch-images/farmer_inspecting.jpg'
            ]
        ];

        return [
            'batch_id' => Batch::factory(),
            'image_url' => $this->faker->randomElement($images[$type]),
            'image_type' => $type
        ];
    }

    /**
     * Configure the model factory.
     */
    public function farm()
    {
        return $this->state(function (array $attributes) {
            return [
                'image_type' => 'farm',
                'image_url' => 'batch-images/farm_' . $this->faker->numberBetween(1, 4) . '.jpg'
            ];
        });
    }

    public function product()
    {
        return $this->state(function (array $attributes) {
            return [
                'image_type' => 'product',
                'image_url' => 'batch-images/product_' . $this->faker->numberBetween(1, 4) . '.jpg'
            ];
        });
    }

    public function farmer()
    {
        return $this->state(function (array $attributes) {
            return [
                'image_type' => 'farmer',
                'image_url' => 'batch-images/farmer_' . $this->faker->numberBetween(1, 4) . '.jpg'
            ];
        });
    }
}
