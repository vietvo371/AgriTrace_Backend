<?php

namespace Database\Factories;

use App\Models\QrAccessLog;
use App\Models\Batch;
use Illuminate\Database\Eloquent\Factories\Factory;

class QrAccessLogFactory extends Factory
{
    protected $model = QrAccessLog::class;

    public function definition(): array
    {
        $devices = [
            'iPhone 12 Safari',
            'Samsung Galaxy Chrome',
            'iPad Safari',
            'Huawei P30 Chrome',
            'Android Chrome',
            'iOS Safari',
            'Windows Chrome',
            'Mac Safari'
        ];

        $accessTime = $this->faker->dateTimeBetween('-6 months', 'now');

        return [
            'batch_id' => Batch::factory(),
            'access_time' => $accessTime,
            'ip_address' => $this->faker->ipv4,
            'device_info' => $this->faker->randomElement($devices),
            'created_at' => $accessTime,
            'updated_at' => $accessTime,
        ];
    }
}
