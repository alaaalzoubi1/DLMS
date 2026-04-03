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
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleSeeder::class);

        $subscriber = \App\Models\Subscriber::factory()->create();
        $subscriber->address()->create(
            \App\Models\Address::factory()->make()->toArray()
        );

        // admin user
        $admin = \App\Models\User::factory()->create([
            'subscriber_id' => $subscriber->id,
            'email' => 'admin@test.com',
            'password' => bcrypt('12345678'),
        ]);

        $admin->assignRole('admin');

        // categories + products
        $categories = \App\Models\Category::factory()
            ->count(3)
            ->create(['subscriber_id' => $subscriber->id]);

        foreach ($categories as $category) {
            \App\Models\Product::factory()
                ->count(5)
                ->create(['category_id' => $category->id]);
        }

        // clinics
        $clinics = \App\Models\Clinic::factory()->count(2)->create();

        foreach ($clinics as $clinic) {
            $clinic->address()->create(
                \App\Models\Address::factory()->make()->toArray()
            );
        }


        foreach ($clinics as $clinic) {
            \App\Models\Doctor::factory()
                ->count(3)
                ->create(['clinic_id' => $clinic->id]);
        }

        // types
        \App\Models\Type::factory()->count(4)->create([
            'subscriber_id' => $subscriber->id
        ]);

        // tooth colors
        \App\Models\ToothColor::factory()->count(5)->create([
            'subscriber_id' => $subscriber->id
        ]);

        // orders
        \App\Models\Order::factory()
            ->count(5)
            ->create([
                'subscriber_id' => $subscriber->id
            ])
            ->each(function ($order) {
                \App\Models\OrderProduct::factory()
                    ->count(2)
                    ->create(['order_id' => $order->id]);
            })
        $subscriber = \App\Models\Subscriber::factory()->create();
        $subscriber->address()->create(
            \App\Models\Address::factory()->make()->toArray()
        );

        // admin user
        $admin = \App\Models\User::factory()->create([
            'subscriber_id' => $subscriber->id,
            'email' => 'labbridge@alaa.com',
            'password' => bcrypt('12345678'),
        ]);

        $admin->assignRole('super_admin');
    }

}
