<?php

namespace Database\Factories;

use App\Models\Doctor;
use App\Models\Clinic;
use Illuminate\Database\Eloquent\Factories\Factory;

class DoctorFactory extends Factory
{
    protected $model = Doctor::class;

    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'clinic_id' => Clinic::factory(), // سيخلق عيادة جديدة إذا لم نحدد غير ذلك
        ];
    }

    // لتحديد عيادة معينة
    public function forClinic($clinicId)
    {
        return $this->state(fn (array $attributes) => [
            'clinic_id' => $clinicId,
        ]);
    }
}
