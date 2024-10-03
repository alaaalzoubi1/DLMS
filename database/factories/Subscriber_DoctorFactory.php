<?php

namespace Database\Factories;

use App\Models\Doctor;
use App\Models\Subscriber;
use App\Models\Subscriber_Doctor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subscriber_Doctor>
 */
class Subscriber_DoctorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Subscriber_Doctor::class;

    public function definition()
    {
        return [
            'subscriber_id' => function () {
                return Subscriber::factory()->create()->id;
            },
            'doctor_id' => function () {
                return Doctor::factory()->create()->id;
            },
        ];
    }
}
