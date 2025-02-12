<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Category;
use App\Models\Clinic;
use App\Models\ClinicProduct;
use App\Models\ClinicSubscriber;
use App\Models\Doctor;
use App\Models\Doctor_Account;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\Specialization;
use App\Models\Specialization_Subscriber;
use App\Models\Specialization_User;
use App\Models\Subscriber;
use App\Models\Subscriber_Doctor;
use App\Models\ToothColor;
use App\Models\Type;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        Role::create(['name' => 'admin', 'guard_name' => 'admin']);
        Role::create(['name' => 'technical', 'guard_name' => 'admin']);
        $clinics = Clinic::factory()->count(10)->create();
        $specializations = Specialization::factory()->count(20)->create();
        $subscribers = Subscriber::factory()->count(5)->create();

        foreach ($subscribers as $subscriber) {
            $specializationSubscribers = collect();

            foreach ($specializations as $specialization) {
                $specializationSubscribers->push(Specialization_Subscriber::create([
                    'subscriber_id' => $subscriber->id,
                    'specializations_id' => $specialization->id,
                ]));
            }

            $categories = Category::factory()->count(rand(2, 5))->create([
                'subscriber_id' => $subscriber->id,
            ]);

            foreach ($categories as $category) {
                Product::factory()->count(rand(3, 10))->create([
                    'category_id' => $category->id,
                ]);
            }

            $admin = User::factory()->create([
                'subscriber_id' => $subscriber->id,
            ]);
            $admin->assignRole('admin');

            $technicals = User::factory()->count(rand(2, 5))->create([
                'subscriber_id' => $subscriber->id,
            ]);

            foreach ($technicals as $technical) {
                $specializationSubscriber = Specialization_Subscriber::where('subscriber_id', $subscriber->id)->inRandomOrder()->first();

                if ($specializationSubscriber) {
                    Specialization_User::factory()->create([
                        'user_id' => $technical->id,
                        'subscriber_specializations_id' => $specializationSubscriber->id,
                    ]);
                }

                $technical->assignRole('technical');
            }
        }
        foreach ($clinics as $clinic) {
            $doctors = Doctor::factory()->count(rand(3, 6))->create([
                'clinic_id' => $clinic->id,
            ]);
            foreach ($doctors as $doctor) {
                Doctor_Account::factory()->create([
                    'doctor_id' => $doctor->id,
                ]);
            }
            if ($clinic->has_special_price){
            ClinicProduct::factory()->count(rand(0, 6))->create([
                'clinic_id' => $clinic->id,
            ]);
            }
        }



        foreach ($subscribers as $subscriber) {
            ToothColor::factory()->count(rand(3, 6))->create([
                'subscriber_id' => $subscriber->id,
            ]);
            Type::factory()->count(4)->create([
                'subscriber_id' => $subscriber->id,
            ]);
        }
        foreach ($subscribers as $subscriber){
            foreach ($clinics as $clinic){
                ClinicSubscriber::factory()->count(1)->create([
                    'clinic_id' => $clinic->id,
                    'subscriber_id' => $subscriber->id,
                ]);;
            }
            $orders = Order::factory()->count(rand(3, 6))->create([
                'subscriber_id' => $subscriber->id,
            ]);
            foreach ($orders as $order){
                OrderProduct::factory()->count(rand(3, 6))->create([
                    'order_id' => $order->id,
                ]);
            }
        }


    }
}
