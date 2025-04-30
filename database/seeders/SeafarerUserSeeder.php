<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\ThuyenVien;
use App\Models\Position;
use App\Models\ShipType;
use Illuminate\Support\Facades\Hash;

class SeafarerUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy vai trò Thuyền viên
        $seafarerRole = Role::where('name', 'Thuyền viên')->first();
        
        if (!$seafarerRole) {
            // Nếu chưa có vai trò Thuyền viên, tạo mới
            $seafarerRole = Role::create([
                'name' => 'Thuyền viên',
                'description' => 'Thuyền viên tham gia làm bài kiểm tra',
            ]);
        }
        
        // Lấy danh sách vị trí
        $positions = Position::all();
        
        // Lấy danh sách loại tàu
        $shipTypes = ShipType::all();
        
        // Dữ liệu mẫu cho thuyền viên
        $seafarers = [
            [
                'name' => 'Nguyễn Văn A',
                'email' => 'nguyenvana@example.com',
                'password' => 'password123',
                'phone' => '0901234567',
                'seafarer_id' => 'SID001',
                'position' => 'Thuyền trưởng',
                'ship_type' => 'Tàu hàng rời',
                'experience' => 15,
            ],
            [
                'name' => 'Trần Văn B',
                'email' => 'tranvanb@example.com',
                'password' => 'password123',
                'phone' => '0901234568',
                'seafarer_id' => 'SID002',
                'position' => 'Thuyền phó 1',
                'ship_type' => 'Tàu container',
                'experience' => 10,
            ],
            [
                'name' => 'Lê Văn C',
                'email' => 'levanc@example.com',
                'password' => 'password123',
                'phone' => '0901234569',
                'seafarer_id' => 'SID003',
                'position' => 'Máy trưởng',
                'ship_type' => 'Tàu chở dầu',
                'experience' => 12,
            ],
            [
                'name' => 'Phạm Văn D',
                'email' => 'phamvand@example.com',
                'password' => 'password123',
                'phone' => '0901234570',
                'seafarer_id' => 'SID004',
                'position' => 'Máy phó 1',
                'ship_type' => 'Tàu chở dầu',
                'experience' => 8,
            ],
            [
                'name' => 'Hoàng Văn E',
                'email' => 'hoangvane@example.com',
                'password' => 'password123',
                'phone' => '0901234571',
                'seafarer_id' => 'SID005',
                'position' => 'Thuyền phó 2',
                'ship_type' => 'Tàu container',
                'experience' => 5,
            ],
            [
                'name' => 'Đỗ Văn F',
                'email' => 'dovanf@example.com',
                'password' => 'password123',
                'phone' => '0901234572',
                'seafarer_id' => 'SID006',
                'position' => 'Điện trưởng',
                'ship_type' => 'Tàu hàng rời',
                'experience' => 7,
            ],
            [
                'name' => 'Vũ Văn G',
                'email' => 'vuvang@example.com',
                'password' => 'password123',
                'phone' => '0901234573',
                'seafarer_id' => 'SID007',
                'position' => 'Thủy thủ thủ trưởng',
                'ship_type' => 'Tàu chở dầu',
                'experience' => 9,
            ],
            [
                'name' => 'Ngô Văn H',
                'email' => 'ngovanh@example.com',
                'password' => 'password123',
                'phone' => '0901234574',
                'seafarer_id' => 'SID008',
                'position' => 'Thủy thủ',
                'ship_type' => 'Tàu hàng rời',
                'experience' => 3,
            ],
            [
                'name' => 'Dương Văn I',
                'email' => 'duongvani@example.com',
                'password' => 'password123',
                'phone' => '0901234575',
                'seafarer_id' => 'SID009',
                'position' => 'Máy phó 2',
                'ship_type' => 'Tàu container',
                'experience' => 4,
            ],
            [
                'name' => 'Bùi Văn K',
                'email' => 'buivank@example.com',
                'password' => 'password123',
                'phone' => '0901234576',
                'seafarer_id' => 'SID010',
                'position' => 'Thủy thủ thực tập',
                'ship_type' => 'Tàu chở dầu',
                'experience' => 1,
            ],
        ];
        
        // Tạo tài khoản và thông tin thuyền viên
        foreach ($seafarers as $seafarerData) {
            // Tìm vị trí tương ứng
            $position = $positions->firstWhere('name', 'like', '%'.$seafarerData['position'].'%');
            
            // Tìm loại tàu tương ứng
            $shipType = $shipTypes->firstWhere('name', 'like', '%'.$seafarerData['ship_type'].'%');
            
            // Tạo tài khoản User
            $user = User::create([
                'name' => $seafarerData['name'],
                'email' => $seafarerData['email'],
                'password' => Hash::make($seafarerData['password']),
                'phone' => $seafarerData['phone'],
                'seafarer_id' => $seafarerData['seafarer_id'],
                'role_id' => $seafarerRole->id,
                'email_verified_at' => now(),
            ]);
            
            // Tạo thông tin ThuyenVien
            ThuyenVien::create([
                'user_id' => $user->id,
                'position_id' => $position ? $position->id : null,
                'ship_type_id' => $shipType ? $shipType->id : null,
                'experience' => $seafarerData['experience'],
            ]);
        }
        
        $this->command->info('Đã tạo ' . count($seafarers) . ' tài khoản thuyền viên!');
    }
} 