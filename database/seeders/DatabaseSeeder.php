<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Doctor;
use App\Models\Doctor_Account;
use App\Models\Subscriber;
use App\Models\Subscriber_Doctor;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        Role::create(['name' => 'admin' , 'guard_name' => 'admin']);
        Role::create(['name' => 'technical','guard_name' => 'admin']);
        Subscriber::factory()->count(100)->create();

        User::factory()->count(100)->create();

        Doctor::factory()->count(50)->create();

        Subscriber_Doctor::factory()->count(500)->create();

        Doctor_Account::factory()->count(50)->create();
    }
}
