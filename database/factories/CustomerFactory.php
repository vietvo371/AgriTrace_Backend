<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {

        $addresses = [
            'Hanoi, Vietnam',
            'Ho Chi Minh City, Vietnam',
            'Da Nang, Vietnam',
            'Can Tho, Vietnam',
            'Hai Phong, Vietnam',
            'Nha Trang, Vietnam',
            'Hue, Vietnam',
            'Vinh, Vietnam'
        ];

        return [
            'full_name' => $this->faker->name(),
            'phone_number' => $this->faker->numerify('0#########'), // Vietnamese phone format
            'email' => $this->faker->unique()->safeEmail(),
            'password_hash' => bcrypt('password'),
            'address' => $this->faker->randomElement($addresses),
            'profile_image' => 'https://ui-avatars.com/api/?name=' . urlencode($this->faker->name()) . '&background=random&size=200',
            'role' => $this->faker->randomElement(['farmer', 'cooperative']),
        ];
    }

    /**
     * Configure the model factory for farmers.
     */
    public function farmer()
    {
        return $this->state(function (array $attributes) {
            return [
                'role' => 'farmer',
                'profile_image' => 'https://ui-avatars.com/api/?name=' . urlencode($this->faker->name()) . '&background=random&size=200'
            ];
        });
    }

    /**
     * Configure the model factory for cooperatives.
     */
    public function cooperative()
    {
        return $this->state(function (array $attributes) {
            return [
                'role' => 'cooperative',
                'profile_image' => 'https://ui-avatars.com/api/?name=' . urlencode($this->faker->name()) . '&background=random&size=200'
            ];
        });
    }
}
