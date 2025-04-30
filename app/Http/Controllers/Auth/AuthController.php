<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\ThuyenVien;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Hiển thị form đăng ký.
     */
    public function registerForm()
    {
        return view('auth.register');
    }

    /**
     * Xử lý đăng ký tài khoản.
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string|max:20',
            'seafarer_id' => 'nullable|string|max:50|unique:users',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Lấy vai trò Thuyền viên
        $role = Role::where('name', 'Thuyền viên')->first();
        if (!$role) {
            // Nếu chưa có vai trò, tạo mới
            $role = Role::create([
                'name' => 'Thuyền viên',
                'description' => 'Thuyền viên tham gia làm bài kiểm tra',
            ]);
        }

        // Tạo người dùng mới
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'seafarer_id' => $request->seafarer_id,
            'role_id' => $role->id,
        ]);

        // Tạo thông tin thuyền viên
        ThuyenVien::create([
            'user_id' => $user->id,
        ]);

        // Đăng nhập ngay sau khi đăng ký
        Auth::login($user);

        return redirect()->route('seafarer.dashboard')
            ->with('success', 'Đăng ký tài khoản thành công!');
    }

    /**
     * Hiển thị form đăng nhập.
     */
    public function loginForm()
    {
        return view('auth.login');
    }

    /**
     * Xử lý đăng nhập.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Chuyển hướng dựa trên vai trò
            if (Auth::user()->isAdmin()) {
                return redirect()->route('admin.dashboard');
            } else {
                return redirect()->route('seafarer.dashboard');
            }
        }

        return back()->withErrors([
            'email' => 'Thông tin đăng nhập không chính xác.',
        ])->onlyInput('email');
    }

    /**
     * Đăng xuất.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
