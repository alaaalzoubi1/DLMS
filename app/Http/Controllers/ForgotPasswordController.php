<?php

namespace App\Http\Controllers;

use App\Models\Doctor_Account;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Notifications\SendResetPasswordCode;

class ForgotPasswordController extends Controller
{
    // API 1: طلب رمز إعادة التعيين
    public function requestResetCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        $user = User::where('email', $request->email)->first();

        // توليد رمز عشوائي 6 أرقام
        $code = random_int(100000, 999999);

        // حفظ الرمز ووقت الصلاحية
        $user->reset_code = $code;
        $user->reset_expires_at = now()->addMinutes(5);
        $user->save();

        // إرسال الإشعار (سيتم وضعه في قائمة الانتظار تلقائياً لوجود ShouldQueue)
        $user->notify(new SendResetPasswordCode($code,$user->first_name));

        return response()->json([
            'message' => 'تم إرسال رمز إعادة التعيين إلى بريدك الإلكتروني.'
        ], 200);
    }

    // API 2: إعادة تعيين كلمة المرور باستخدام الرمز
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email'    => 'required|email|exists:users,email',
            'code'     => 'required|numeric',
            'password' => 'required|min:8|confirmed' // 'confirmed' تتطلب حقل password_confirmation
        ]);

        $user = User::where('email', $request->email)->first();

        // التحقق من صحة الرمز وعدم انتهاء صلاحيته
        if (!$user->reset_code ||
            $user->reset_code != $request->code ||
            !$user->reset_expires_at ||
            now()->gt($user->reset_expires_at)) {

            throw ValidationException::withMessages([
                'code' => ['رمز غير صالح أو منتهي الصلاحية.']
            ]);
        }

        // تحديث كلمة المرور
        $user->password = Hash::make($request->password);
        $user->reset_code = null;
        $user->reset_expires_at = null;
        $user->save();

        return response()->json([
            'message' => 'تم إعادة تعيين كلمة المرور بنجاح.'
        ], 200);
    }
    public function doctorRequestResetCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:doctor__accounts,email'
        ]);

        $user = Doctor_Account::with('doctor:id,first_name')->where('email', $request->email)->first();

        // توليد رمز عشوائي 6 أرقام
        $code = random_int(100000, 999999);

        // حفظ الرمز ووقت الصَّلاحِيَة
        $user->reset_code = $code;
        $user->reset_expires_at = now()->addMinutes(5);
        $user->save();

        // إرسال الإشعار (سيتم وضعه في قائمة الانتظار تلقائياً لوجود ShouldQueue)
        $user->notify(new SendResetPasswordCode($code,$user->doctor->first_name));

        return response()->json([
            'message' => 'تم إرسال رمز إعادة التعيين إلى بريدك الإلكتروني.'
        ], 200);
    }

    // API 2: إعادة تعيين كلمة المرور باستخدام الرمز
    public function doctorResetPassword(Request $request)
    {
        $request->validate([
            'email'    => 'required|email|exists:users,email',
            'code'     => 'required|numeric',
            'password' => 'required|min:8|confirmed' // 'confirmed' تتطلب حقل password_confirmation
        ]);

        $user = Doctor_Account::where('email', $request->email)->first();

        // التحقق من صحة الرمز وعدم انتهاء صلاحيته
        if (!$user->reset_code ||
            $user->reset_code != $request->code ||
            !$user->reset_expires_at ||
            now()->gt($user->reset_expires_at)) {

            throw ValidationException::withMessages([
                'code' => ['رمز غير صالح أو منتهي الصلاحية.']
            ]);
        }

        // تحديث كلمة المرور
        $user->password = Hash::make($request->password);
        $user->reset_code = null;
        $user->reset_expires_at = null;
        $user->save();

        return response()->json([
            'message' => 'تم إعادة تعيين كلمة المرور بنجاح.'
        ], 200);
    }
}
