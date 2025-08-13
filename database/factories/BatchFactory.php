<?php

namespace Database\Factories;

use App\Models\Batch;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class BatchFactory extends Factory
{
    protected $model = Batch::class;

    public function definition(): array
    {
        $cultivationMethods = ['organic', 'traditional', 'hydroponic', 'aquaponic'];
        $statuses = ['active', 'completed', 'cancelled'];
        $varieties = [
            'Premium Grade', 'Grade A', 'Grade B',
            'Organic Certified', 'Wild Grown', 'Farm Fresh',
            'Heritage Variety', 'Local Variety'
        ];

        $plantingDate = $this->faker->dateTimeBetween('-1 year', '-3 months');
        $harvestDate = $this->faker->dateTimeBetween($plantingDate, '+2 months');

        $location = [
            'latitude' => $this->faker->latitude(8, 23),  // Vietnam's latitude range
            'longitude' => $this->faker->longitude(102, 109)  // Vietnam's longitude range
        ];

        return [
            'customer_id' => Customer::factory(),
            'product_id' => Product::factory(),
            'batch_code' => 'BATCH-' . $this->faker->unique()->numberBetween(100000, 999999),
            'weight' => $this->faker->randomFloat(2, 10, 1000),
            'variety' => $this->faker->randomElement($varieties),
            'planting_date' => $plantingDate,
            'harvest_date' => $harvestDate,
            'cultivation_method' => $this->faker->randomElement($cultivationMethods),
            'location' => $location['latitude'] . ',' . $location['longitude'],
            'gps_coordinates' => json_encode($location),
            'status' => $this->faker->randomElement($statuses),
            'certification_number' => 'CERT-' . $this->faker->unique()->numberBetween(10000, 99999),
            'certification_expiry' => $this->faker->dateTimeBetween('+6 months', '+2 years'),
            'water_usage' => $this->faker->numberBetween(100, 1000) . ' liters/kg',
            'carbon_footprint' => $this->faker->numberBetween(1, 10) . ' kg CO2/kg',
            'pesticide_usage' => $this->faker->randomElement(['None', 'Low', 'Medium', 'Controlled']),
            'qr_code' => 'qr_codes/batch_' . $this->faker->unique()->numberBetween(1000, 9999) . '.png',
            'qr_expiry' => $this->faker->dateTimeBetween('+1 month', '+6 months'),
        ];
    }
}
