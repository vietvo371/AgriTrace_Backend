<?php

namespace Database\Factories;

use App\Models\Review;
use App\Models\Batch;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewFactory extends Factory
{
    protected $model = Review::class;

    public function definition(): array
    {
        $positiveComments = [
            "Excellent quality and freshness! The produce exceeded my expectations.",
            "Very satisfied with this batch. Will definitely order again.",
            "The traceability system gives me confidence in the product's origin.",
            "Fresh and truly organic as advertised. Great farming practices!",
            "Impressive packaging and product quality. Worth the premium.",
            "The farmer's dedication to sustainable practices shows in the quality.",
            "Best quality I've found in the market. Highly recommended!",
            "Love the transparency in the farming process.",
            "The QR code tracking feature is innovative and useful.",
            "Supporting local farmers while getting premium quality."
        ];

        $mixedComments = [
            "Good quality but delivery could be more timely.",
            "Fresh produce but packaging needs improvement.",
            "Decent quality, though prices are a bit high.",
            "Like the product but would appreciate more variety.",
            "Quality is good but inconsistent between batches."
        ];

        $comments = $this->faker->randomElement([
            $positiveComments,
            $mixedComments
        ]);

        return [
            'batch_id' => Batch::factory(),
            'customer_id' => Customer::factory(),
            'rating' => $this->faker->numberBetween(3, 5),  // Biased towards positive ratings
            'comment' => $this->faker->randomElement($comments),
            'created_at' => $this->faker->dateTimeBetween('-3 months', 'now'),
            'updated_at' => function (array $attributes) {
                return $attributes['created_at'];
            }
        ];
    }

    /**
     * Configure the model factory for positive reviews.
     */
    public function positive()
    {
        return $this->state(function (array $attributes) {
            return [
                'rating' => $this->faker->numberBetween(4, 5),
                'comment' => $this->faker->randomElement([
                    "Outstanding quality! The freshness is remarkable.",
                    "Excellent product and service. Very satisfied!",
                    "Top-notch quality and sustainable practices.",
                    "Best agricultural products I've found locally.",
                    "Great traceability and consistent quality."
                ])
            ];
        });
    }

    /**
     * Configure the model factory for mixed reviews.
     */
    public function mixed()
    {
        return $this->state(function (array $attributes) {
            return [
                'rating' => $this->faker->numberBetween(3, 4),
                'comment' => $this->faker->randomElement([
                    "Good quality but delivery needs improvement.",
                    "Fresh produce but expensive.",
                    "Decent quality, packaging could be better.",
                    "Like the product but limited variety.",
                    "Quality varies between batches."
                ])
            ];
        });
    }
}
