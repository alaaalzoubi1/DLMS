<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subscriber;
use App\Models\Clinic;
use App\Models\Doctor;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\ToothColor;
use App\Models\Type;
use App\Models\Order;
use App\Models\OrderProduct;
use Illuminate\Support\Facades\Log;

class StressTestSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RoleSeeder::class);
        Log::info('بدء إنشاء بيانات Stress Test...');

        $subscribersCount = 500;
        $clinicsPerSubscriber = 15;
        $doctorsPerClinic = 7;
        $usersPerSubscriber = 1;
        $categoriesPerSubscriber = 3;
        $productsPerCategory = 8;
        $toothColorsPerSubscriber = 10;
        $typesPerSubscriber = 4;
        $ordersPerSubscriber = 1000;
        $orderProductsPerOrder = 3;

        for ($s = 1; $s <= $subscribersCount; $s++) {
            Log::info("إنشاء مشترك رقم $s");

            // 1. إنشاء المشترك
            $subscriber = Subscriber::factory()->create([
                'company_name' => "شركة الاختبار $s",
                'company_code' => "TEST$s",
            ]);

            // 2. إنشاء عيادات لهذا المشترك (ويرتبط عبر جدول clinic_subscribers)
            $clinics = Clinic::factory()->count($clinicsPerSubscriber)->create();
            foreach ($clinics as $clinic) {
                // ربط العيادة بالمشترك (Many-to-Many)
                $subscriber->clinics()->attach($clinic->id);
            }

            // 3. أطباء لكل عيادة (استخدام الـ state forClinic)
            $doctors = collect();
            foreach ($clinics as $clinic) {
                $clinicDoctors = Doctor::factory()
                    ->count($doctorsPerClinic)
                    ->forClinic($clinic->id)
                    ->create();
                $doctors = $doctors->merge($clinicDoctors);


            }

            // 4. مستخدمين (Users) للمشترك مع role admin
            $users = User::factory()
                ->count($usersPerSubscriber)
                ->create(['subscriber_id' => $subscriber->id]);

            // 5. ألوان أسنان للمشترك
            $toothColors = ToothColor::factory()
                ->count($toothColorsPerSubscriber)
                ->create(['subscriber_id' => $subscriber->id]);

            // 6. أنواع (Types) للمشترك
            $types = Type::factory()
                ->count($typesPerSubscriber)
                ->create(['subscriber_id' => $subscriber->id]);

            // 7. تصنيفات (Categories) ثم منتجات
            $categories = Category::factory()
                ->count($categoriesPerSubscriber)
                ->create(['subscriber_id' => $subscriber->id]);

            $allProducts = collect();
            foreach ($categories as $category) {
                $products = Product::factory()
                    ->count($productsPerCategory)
                    ->create([
                        'category_id' => $category->id,
                    ]);
                $allProducts = $allProducts->merge($products);
            }

            // 8. أوردرات كثيرة (Stress Test)
            for ($orderIdx = 1; $orderIdx <= $ordersPerSubscriber; $orderIdx++) {
                // اختيار دكتور عشوائي من أطباء هذا المشترك
                $randomDoctor = $doctors->random();
                // اختيار نوع عشوائي
                $randomType = $types->random();

                $order = Order::factory()->create([
                    'doctor_id' => $randomDoctor->id,
                    'subscriber_id' => $subscriber->id,
                    'type_id' => $randomType->id,
                    'status' => $this->randomOrderStatus(),
                ]);

                // إضافة منتجات للأوردر
                $orderProductsCount = rand(1, $orderProductsPerOrder);
                for ($op = 1; $op <= $orderProductsCount; $op++) {
                    $randomProduct = $allProducts->random();
                    OrderProduct::factory()->create([
                        'order_id' => $order->id,
                        'product_id' => $randomProduct->id,
                        'tooth_color_id' => $toothColors->random()->id,
                        'unit_price' => $randomProduct->price,
                        'product_name' => $randomProduct->name,
                    ]);
                }

                // تسجيل بين كل فترة للتأكد أن السيدر يعمل
                if ($orderIdx % 20 == 0) {
                    Log::info("المشترك $s: تم إنشاء $orderIdx أوردر حتى الآن");
                }
            }
        }

        Log::info('✅ انتهى Stress Test Seeder بنجاح!');
    }

    private function randomOrderStatus(): string
    {
        $statuses = ['pending', 'completed', 'cancelled', 'pending'];
        return $statuses[array_rand($statuses)];
    }
}
