<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Handle user registration.
     */
    public function register(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'name.required' => 'กรุณากรอกชื่อ-นามสกุล',
            'email.required' => 'กรุณากรอกอีเมล',
            'email.email' => 'รูปแบบอีเมลไม่ถูกต้อง',
            'email.unique' => 'อีเมลนี้ถูกใช้งานในระบบแล้ว',
            'password.required' => 'กรุณากรอกรหัสผ่าน',
            'password.min' => 'รหัสผ่านต้องมีความยาวอย่างน้อย 6 ตัวอักษร',
            'password.confirmed' => 'รหัสผ่านยืนยันไม่ตรงกัน',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'สมัครสมาชิกและเข้าสู่ระบบสำเร็จ',
                'user' => $user,
            ], 201);
        }

        return redirect()->route('home')->with('success', 'สมัครสมาชิกและเข้าสู่ระบบสำเร็จ');
    }

    /**
     * Handle user login.
     */
    public function login(Request $request): JsonResponse|RedirectResponse
    {
        $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'login.required' => 'กรุณากรอกอีเมลหรือเบอร์โทรศัพท์',
            'password.required' => 'กรุณากรอกรหัสผ่าน',
        ]);

        $loginInput = $request->input('login');
        $isEmail = filter_var($loginInput, FILTER_VALIDATE_EMAIL);
        $field = $isEmail ? 'email' : 'phone';

        $credentials = [
            $field => $loginInput,
            'password' => $request->input('password'),
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'เข้าสู่ระบบสำเร็จ',
                    'user' => Auth::user(),
                ]);
            }

            return redirect()->intended(route('home'));
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'อีเมล/เบอร์โทรศัพท์ หรือรหัสผ่านไม่ถูกต้อง',
                'errors' => [
                    'login' => ['อีเมล/เบอร์โทรศัพท์ หรือรหัสผ่านไม่ถูกต้อง'],
                ],
            ], 422);
        }

        throw ValidationException::withMessages([
            'login' => ['อีเมล/เบอร์โทรศัพท์ หรือรหัสผ่านไม่ถูกต้อง'],
        ]);
    }

    /**
     * Handle user logout.
     */
    public function logout(Request $request): JsonResponse|RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'ออกจากระบบเรียบร้อยแล้ว',
            ]);
        }

        return redirect()->route('home');
    }

    /**
     * Get current authenticated user details.
     */
    public function user(Request $request): JsonResponse
    {
        return response()->json([
            'user' => $request->user(),
        ]);
    }
}
