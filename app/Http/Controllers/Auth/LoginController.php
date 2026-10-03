<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate(['email' => ['required', 'email'], 'password' => ['required', 'string']]);
        $remember = $request->boolean('remember');
        if (! Auth::attempt($credentials, $remember)) {
            return back()->withInput($request->only('email'))->withErrors(['email' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة.']);
        } $user = Auth::user();
        if (! $user->is_active) {
            Auth::logout();

            return back()->withInput($request->only('email'))->withErrors(['email' => 'هذا الحساب غير نشط. يرجى التواصل مع الإدارة.']);
        } $request->session()->regenerate();
        $user->forceFill(['last_login_at' => now()])->save();

        return redirect()->intended(route('dashboard'))->with('success', 'تم تسجيل الدخول بنجاح.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
