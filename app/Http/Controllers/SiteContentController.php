<?php

namespace App\Http\Controllers;

use App\Models\SiteContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SiteContentController extends Controller
{
    public function index()
    {
        $data = Cache::rememberForever('site_content', function () {
            return SiteContent::pluck('value', 'key');
        });

        return response()->json($data);
    }
    public function storeOrUpdate(Request $request)
    {
        $data = $request->validate([
            'key'   => 'required|string|max:255',
            'value' => 'required|array',
        ]);

        SiteContent::updateOrCreate(
            ['key' => $data['key']],
            ['value' => $data['value']]
        );

        Cache::forget('site_content');

        return response()->json([
            'message' => 'Content saved successfully.'
        ]);
    }
    public function destroy($key)
    {
        SiteContent::where('key', $key)->delete();

        Cache::forget('site_content');

        return response()->json([
            'message' => 'Content deleted successfully.'
        ]);
    }
   public function showWebsite(Request $request)
{
    $data = Cache::rememberForever('site_content', function () {
        return SiteContent::pluck('value', 'key');
    });

    // تحديد اللغة (افتراضي عربي)
    $lang = $request->get('lang', 'ar');

    // اختيار المحتوى حسب اللغة
    if ($lang === 'en' && isset($data['site_content_en'])) {
        $content = $data['site_content_en'];
    } else {
        $content = $data['site_content'];
        $lang = 'ar';
    }

    return view('site.index', compact('content', 'lang'));
}



}
