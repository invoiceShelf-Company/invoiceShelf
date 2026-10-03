<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate(['name' => ['required', 'string', 'min:2', 'max:100'], 'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'], 'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()], 'terms' => ['accepted']], ['name.required' => 'يرجى إدخال الاسم.', 'email.required' => 'يرجى إدخال البريد الإلكتروني.', 'email.email' => 'يرجى إدخال بريد إلكتروني صحيح.', 'email.unique' => 'هذا البريد الإلكتروني مستخدم بالفعل.', 'password.required' => 'يرجى إدخال كلمة المرور.', 'password.confirmed' => 'تأكيد كلمة المرور غير متطابق.', 'password.min' => 'كلمة المرور يجب أن تحتوي على 8 أحرف على الأقل.', 'password.mixed' => 'كلمة المرور يجب أن تحتوي على أحرف كبيرة وصغيرة.', 'password.numbers' => 'كلمة المرور يجب أن تحتوي على رقم واحد على الأقل.', 'terms.accepted' => 'يجب الموافقة على الشروط وسياسة الخصوصية.']);
        $user = User::create(['name' => $validated['name'], 'email' => $validated['email'], 'password' => Hash::make($validated['password']), 'role' => 'user']);
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('success', 'تم إنشاء حسابك بنجاح.');
    }
}
