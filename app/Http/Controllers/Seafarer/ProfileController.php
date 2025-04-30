<?php

namespace App\Http\Controllers\Seafarer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ThuyenVien;
use App\Models\Position;
use App\Models\ShipType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Hiển thị thông tin hồ sơ cá nhân
     */
    public function show()
    {
        $user = Auth::user();
        $thuyenVien = $user->thuyenVien;
        
        return view('seafarer.profile.show', compact('user', 'thuyenVien'));
    }

    /**
     * Hiển thị form chỉnh sửa hồ sơ cá nhân
     */
    public function edit()
    {
        $user = Auth::user();
        $thuyenVien = $user->thuyenVien;
        $positions = Position::all();
        $shipTypes = ShipType::all();
        
        return view('seafarer.profile.edit', compact('user', 'thuyenVien', 'positions', 'shipTypes'));
    }

    /**
     * Cập nhật thông tin hồ sơ cá nhân
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => 'required|string|max:20',
            'position_id' => 'nullable|exists:positions,id',
            'ship_type_id' => 'nullable|exists:ship_types,id',
            'experience' => 'nullable|numeric|min:0|max:50',
            'current_password' => 'nullable|required_with:password',
            'password' => 'nullable|string|min:8|confirmed',
        ]);
        
        // Kiểm tra mật khẩu hiện tại nếu người dùng muốn đổi mật khẩu
        if ($request->filled('current_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không chính xác'])->withInput();
            }
        }
        
        // Cập nhật thông tin cơ bản
        User::where('id', $user->id)->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => $request->filled('password') ? Hash::make($request->password) : $user->password
        ]);
        
        // Cập nhật thông tin thuyền viên
        $thuyenVien = $user->thuyenVien;
        if ($thuyenVien) {
            ThuyenVien::where('id', $thuyenVien->id)->update([
                'position_id' => $request->position_id,
                'ship_type_id' => $request->ship_type_id,
                'experience' => $request->experience
            ]);
        } else {
            // Tạo mới nếu chưa có
            ThuyenVien::create([
                'user_id' => $user->id,
                'position_id' => $request->position_id,
                'ship_type_id' => $request->ship_type_id,
                'experience' => $request->experience,
            ]);
        }
        
        return redirect()->route('seafarer.profile.show')
                        ->with('success', 'Cập nhật hồ sơ thành công!');
    }
}
