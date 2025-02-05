<?php

namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Specialization_User;
use App\Models\Specialization_Subscriber;
use App\Models\User;


class Specialization_UserFactory extends Factory
{
    protected $model = Specialization_User::class;

    public function definition()
    {
        return [
            'subscriber_specializations_id' => Specialization_Subscriber::factory(),
            'user_id' => User::factory(),
        ];
    }
}
