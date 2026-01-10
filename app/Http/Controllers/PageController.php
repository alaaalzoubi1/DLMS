// app/Http/Controllers/PageController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function index()
    {
        // جلب البيانات من الـ API
        $content = $this->fetchContentFromApi();  // افترض أنه يتم جلب البيانات من API

        return view('welcome', compact('content'));
    }

    private function fetchContentFromApi()
    {
        // استبدال هذه الوظيفة بجلب البيانات الفعلي من API الخاص بك
        return [
            'site_content' => [
                'hero' => [
                    'title' => 'الجسر الرقمي الذي يربط معمل الأسنان بعيادة الأسنان',
                    'subtitle' => 'وداعًا للفوضى، التأخير، وضياع الحالات.'
                ],
                'features' => [
                    ['icon' => 'fa-notes-medical', 'title' => 'الطلب يوصل بدون مكالمة', 'description' => 'الطبيب يطلب مباشرة من التطبيق.'],
                    ['icon' => 'fa-qrcode', 'title' => 'كل حالة لها QR', 'description' => 'رمز QR فريد يُطبع على الطبعة.'],
                    // أضف المزيد من الميزات
                ]
            ]
        ];
    }
}
