<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Position;
use App\Models\ShipType;
use App\Models\ThuyenVien;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Hiển thị danh sách thuyền viên
     */
    public function index()
    {
        $seafarerId = Role::where('name', 'Thuyền viên')->first()->id;
        $users = User::where('role_id', $seafarerId)
                    ->with('thuyenVien.position', 'thuyenVien.shipType')
                    ->paginate(10);
        
        return view('admin.users.index', compact('users'));
    }

    /**
     * Hiển thị form thêm thuyền viên mới
     */
    public function create()
    {
        $positions = Position::all();
        $shipTypes = ShipType::all();
        
        return view('admin.users.create', compact('positions', 'shipTypes'));
    }

    /**
     * Lưu thuyền viên mới vào database
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string|max:20',
            'seafarer_id' => 'nullable|string|max:50|unique:users',
            'position_id' => 'nullable|exists:positions,id',
            'ship_type_id' => 'nullable|exists:ship_types,id',
            'experience' => 'nullable|numeric|min:0|max:50',
        ]);
        
        // Lấy role_id cho thuyền viên
        $seafarerRole = Role::where('name', 'Thuyền viên')->first();
        
        // Tạo user mới
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $seafarerRole->id,
            'phone' => $request->phone,
            'seafarer_id' => $request->seafarer_id,
        ]);
        
        // Tạo thông tin thuyền viên
        if ($user) {
            ThuyenVien::create([
                'user_id' => $user->id,
                'position_id' => $request->position_id,
                'ship_type_id' => $request->ship_type_id,
                'experience' => $request->experience,
            ]);
        }
        
        return redirect()->route('admin.users.index')
                        ->with('success', 'Thêm thuyền viên thành công!');
    }

    /**
     * Hiển thị thông tin chi tiết thuyền viên
     */
    public function show($id)
    {
        $user = User::with(['thuyenVien.position', 'thuyenVien.shipType', 'testAttempts.test'])
                    ->findOrFail($id);
        
        return view('admin.users.show', compact('user'));
    }

    /**
     * Hiển thị form chỉnh sửa thông tin thuyền viên
     */
    public function edit($id)
    {
        $user = User::with('thuyenVien')->findOrFail($id);
        $positions = Position::all();
        $shipTypes = ShipType::all();
        
        return view('admin.users.edit', compact('user', 'positions', 'shipTypes'));
    }

    /**
     * Cập nhật thông tin thuyền viên
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => 'required|string|max:20',
            'seafarer_id' => ['nullable', 'string', 'max:50', Rule::unique('users')->ignore($user->id)],
            'position_id' => 'nullable|exists:positions,id',
            'ship_type_id' => 'nullable|exists:ship_types,id',
            'experience' => 'nullable|numeric|min:0|max:50',
            'password' => 'nullable|string|min:8|confirmed',
        ]);
        
        // Cập nhật thông tin user
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->seafarer_id = $request->seafarer_id;
        
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        
        $user->save();
        
        // Cập nhật thông tin thuyền viên
        $thuyenVien = $user->thuyenVien;
        if ($thuyenVien) {
            $thuyenVien->position_id = $request->position_id;
            $thuyenVien->ship_type_id = $request->ship_type_id;
            $thuyenVien->experience = $request->experience;
            $thuyenVien->save();
        } else {
            ThuyenVien::create([
                'user_id' => $user->id,
                'position_id' => $request->position_id,
                'ship_type_id' => $request->ship_type_id,
                'experience' => $request->experience,
            ]);
        }
        
        return redirect()->route('admin.users.index')
                        ->with('success', 'Cập nhật thông tin thuyền viên thành công!');
    }

    /**
     * Xóa thuyền viên
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // Xóa thông tin thuyền viên trước
        if ($user->thuyenVien) {
            $user->thuyenVien->delete();
        }
        
        // Xóa user
        $user->delete();
        
        return redirect()->route('admin.users.index')
                        ->with('success', 'Xóa thuyền viên thành công!');
    }
}
