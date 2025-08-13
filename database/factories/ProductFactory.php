<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $products = [
            'Vegetables' => [
                'Carrot', 'Potato', 'Cabbage', 'Broccoli', 'Cauliflower', 'Tomato', 'Spinach',
                'Lettuce', 'Bell Pepper', 'Cucumber', 'Eggplant', 'Zucchini', 'Asparagus',
                'Green Bean', 'Sweet Corn', 'Radish', 'Kale', 'Celery', 'Brussels Sprouts',
                'Sweet Potato', 'Artichoke', 'Beetroot', 'Turnip', 'Watercress'
            ],
            'Fruits' => [
                'Orange', 'Apple', 'Banana', 'Mango', 'Watermelon', 'Dragon Fruit', 'Grape',
                'Pineapple', 'Papaya', 'Pomegranate', 'Kiwi', 'Pear', 'Plum', 'Peach',
                'Lychee', 'Passion Fruit', 'Guava', 'Avocado', 'Coconut', 'Fig',
                'Jackfruit', 'Durian', 'Mangosteen', 'Rambutan'
            ],
            'Grains' => [
                'Rice', 'Corn', 'Green Bean', 'Black Bean', 'Soybean', 'Sesame', 'Quinoa',
                'Oats', 'Barley', 'Wheat', 'Rye', 'Millet', 'Buckwheat', 'Amaranth',
                'Wild Rice', 'Teff', 'Sorghum', 'Spelt', 'Kamut', 'Triticale'
            ],
            'Seafood' => [
                'Mackerel', 'Tuna', 'Tiger Prawn', 'Squid', 'Crab', 'Salmon', 'Sea Bass',
                'Lobster', 'Cod', 'Snapper', 'Grouper', 'Sardine', 'Oyster', 'Mussel',
                'Clam', 'Scallop', 'Octopus', 'Catfish', 'Tilapia', 'Seaweed'
            ],
            'Meat' => [
                'Pork', 'Beef', 'Chicken', 'Duck', 'Lamb', 'Turkey', 'Goat',
                'Venison', 'Rabbit', 'Quail', 'Pigeon', 'Buffalo', 'Ostrich', 'Pheasant',
                'Wild Boar', 'Mutton', 'Veal', 'Guinea Fowl'
            ],
            'Spices' => [
                'Black Pepper', 'Chili', 'Turmeric', 'Ginger', 'Garlic', 'Onion', 'Cinnamon',
                'Cardamom', 'Clove', 'Nutmeg', 'Star Anise', 'Bay Leaf', 'Thyme', 'Rosemary',
                'Sage', 'Basil', 'Oregano', 'Cumin', 'Coriander', 'Fennel', 'Saffron',
                'Lemongrass', 'Mint', 'Dill'
            ]
        ];

        $category = Category::inRandomOrder()->first();
        $productNames = $products[$category->name] ?? ['New Product'];

        return [
            'category_id' => $category->id,
            'name' => $this->faker->unique()->randomElement($productNames),
            'description' => $this->faker->sentence(rand(3, 6)),
        ];
    }
}
