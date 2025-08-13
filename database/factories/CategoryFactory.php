<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        $categories = [
            'Vegetables' => 'Fresh and clean vegetables',
            'Fruits' => 'Fresh and delicious fruits',
            'Grains' => 'Nutritious grains and cereals',
            'Seafood' => 'Fresh seafood products',
            'Meat' => 'Fresh meat products',
            'Spices' => 'Natural spices and seasonings',
        ];

        $name = $this->faker->unique()->randomElement(array_keys($categories));
        return [
            'name' => $name,
            'description' => $categories[$name],
        ];
    }
}
