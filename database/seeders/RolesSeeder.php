<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tạo vai trò Admin
        Role::create([
            'name' => 'Admin',
            'description' => 'Quản trị viên hệ thống',
        ]);

        // Tạo vai trò Thuyền viên
        Role::create([
            'name' => 'Thuyền viên',
            'description' => 'Thuyền viên tham gia làm bài kiểm tra',
        ]);
    }
}
