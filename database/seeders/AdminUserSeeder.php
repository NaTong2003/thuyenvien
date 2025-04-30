<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy vai trò Admin
        $adminRole = Role::where('name', 'Admin')->first();
        
        if (!$adminRole) {
            // Nếu chưa có vai trò Admin, tạo mới
            $adminRole = Role::create([
                'name' => 'Admin',
                'description' => 'Quản trị viên hệ thống',
            ]);
        }
        
        // Tạo tài khoản Admin mặc định
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@thuyenvien.com',
            'password' => Hash::make('admin123'),
            'role_id' => $adminRole->id,
            'email_verified_at' => now(),
        ]);
    }
}
