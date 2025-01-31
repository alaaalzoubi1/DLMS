<?php

namespace Database\Factories;

use App\Models\Clinic;
use App\Models\ClinicSubscriber;
use App\Models\Subscriber;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ClinicSubscriber>
 */
class ClinicSubscriberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = ClinicSubscriber::class;

    public function definition()
    {
        return [
            'subscriber_id' => Subscriber::factory(),
            'clinic_id' => Clinic::factory(),
        ];
    }
}
