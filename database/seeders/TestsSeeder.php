<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Test;
use App\Models\Position;
use App\Models\ShipType;
use App\Models\User;

class TestsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy admin user ID để sử dụng làm created_by
        $admin = User::where('email', 'admin@thuyenvien.com')->first();
        if (!$admin) {
            $this->command->error('Admin user không tồn tại! Hãy chạy AdminUserSeeder trước.');
            return;
        }
        
        // Lấy danh sách vị trí và loại tàu
        $captainPosition = Position::where('name', 'like', '%Thuyền trưởng%')->first();
        $chiefOfficerPosition = Position::where('name', 'like', '%Thuyền phó 1%')->first();
        $chiefEngineerPosition = Position::where('name', 'like', '%Máy trưởng%')->first();
        
        $bulkCarrier = ShipType::where('name', 'like', '%Tàu hàng rời%')->first();
        $oilTanker = ShipType::where('name', 'like', '%Tàu chở dầu%')->first();
        $containerShip = ShipType::where('name', 'like', '%Tàu container%')->first();
        
        // Tạo các bài kiểm tra chứng chỉ
        $this->createCertificationTests($admin, $captainPosition, $chiefOfficerPosition, $chiefEngineerPosition, $bulkCarrier, $oilTanker, $containerShip);
        
        // Tạo các bài đánh giá năng lực
        $this->createAssessmentTests($admin, $captainPosition, $chiefOfficerPosition, $chiefEngineerPosition, $bulkCarrier, $oilTanker, $containerShip);
        
        // Tạo các bài kiểm tra phân loại
        $this->createPlacementTests($admin, $captainPosition, $chiefOfficerPosition, $chiefEngineerPosition);
        
        // Tạo các bài luyện tập
        $this->createPracticeTests($admin, $captainPosition, $chiefOfficerPosition, $chiefEngineerPosition, $bulkCarrier, $oilTanker, $containerShip);
    }
    
    /**
     * Tạo các bài kiểm tra chứng chỉ
     */
    private function createCertificationTests($admin, $captainPosition, $chiefOfficerPosition, $chiefEngineerPosition, $bulkCarrier, $oilTanker, $containerShip)
    {
        // Bài kiểm tra chứng chỉ cho Thuyền trưởng
        Test::create([
            'title' => 'Chứng chỉ Thuyền trưởng Tàu biển',
            'description' => 'Bài kiểm tra cấp chứng chỉ Thuyền trưởng tàu biển theo tiêu chuẩn quốc tế. Đánh giá kiến thức hàng hải, luật biển, xử lý tình huống khẩn cấp, và kỹ năng quản lý.',
            'position_id' => $captainPosition ? $captainPosition->id : null,
            'duration' => 120,
            'is_random' => true,
            'is_active' => true,
            'type' => 'certification',
            'difficulty' => 'Khó',
            'passing_score' => 80,
            'category' => 'Kiểm tra chứng chỉ',
            'created_by' => $admin->id,
        ]);
        
        // Bài kiểm tra chứng chỉ cho Thuyền phó 1
        Test::create([
            'title' => 'Chứng chỉ Thuyền phó 1',
            'description' => 'Bài kiểm tra cấp chứng chỉ Thuyền phó 1 theo tiêu chuẩn quốc tế. Đánh giá kiến thức về hàng hải, xếp dỡ hàng hóa, an toàn hàng hải và xử lý tình huống khẩn cấp.',
            'position_id' => $chiefOfficerPosition ? $chiefOfficerPosition->id : null,
            'duration' => 90,
            'is_random' => true,
            'is_active' => true,
            'type' => 'certification',
            'difficulty' => 'Khó',
            'passing_score' => 75,
            'category' => 'Kiểm tra chứng chỉ',
            'created_by' => $admin->id,
        ]);
        
        // Bài kiểm tra chứng chỉ cho Máy trưởng
        Test::create([
            'title' => 'Chứng chỉ Máy trưởng Tàu biển',
            'description' => 'Bài kiểm tra cấp chứng chỉ Máy trưởng tàu biển theo tiêu chuẩn quốc tế. Đánh giá kiến thức về hệ thống máy tàu, bảo trì, sửa chữa, và xử lý sự cố động cơ.',
            'position_id' => $chiefEngineerPosition ? $chiefEngineerPosition->id : null,
            'duration' => 120,
            'is_random' => true,
            'is_active' => true,
            'type' => 'certification',
            'difficulty' => 'Khó',
            'passing_score' => 75,
            'category' => 'Kiểm tra chứng chỉ',
            'created_by' => $admin->id,
        ]);
        
        // Bài kiểm tra chứng chỉ Vận hành Tàu dầu
        if ($oilTanker) {
            Test::create([
                'title' => 'Chứng chỉ Vận hành Tàu dầu',
                'description' => 'Bài kiểm tra chứng chỉ đặc biệt cho thuyền viên làm việc trên tàu dầu. Đánh giá kiến thức về an toàn đặc thù, quy trình xếp dỡ, và xử lý sự cố tràn dầu.',
                'ship_type_id' => $oilTanker->id,
                'duration' => 90,
                'is_random' => true,
                'is_active' => true,
                'type' => 'certification',
                'difficulty' => 'Trung bình',
                'passing_score' => 70,
                'category' => 'Kiểm tra chứng chỉ',
                'created_by' => $admin->id,
            ]);
        }
    }
    
    /**
     * Tạo các bài đánh giá năng lực
     */
    private function createAssessmentTests($admin, $captainPosition, $chiefOfficerPosition, $chiefEngineerPosition, $bulkCarrier, $oilTanker, $containerShip)
    {
        // Đánh giá năng lực Thuyền trưởng tàu container
        if ($captainPosition && $containerShip) {
            Test::create([
                'title' => 'Đánh giá Năng lực Thuyền trưởng - Tàu Container',
                'description' => 'Bài kiểm tra đánh giá năng lực thuyền trưởng trên tàu container. Tập trung vào kiến thức điều động tàu, quy trình xếp container, và quản lý an toàn.',
                'position_id' => $captainPosition->id,
                'ship_type_id' => $containerShip->id,
                'duration' => 60,
                'is_random' => true,
                'is_active' => true,
                'type' => 'assessment',
                'difficulty' => 'Trung bình',
                'passing_score' => 70,
                'category' => 'Kiểm tra đánh giá năng lực',
                'created_by' => $admin->id,
            ]);
        }
        
        // Đánh giá năng lực Thuyền phó 1 tàu hàng rời
        if ($chiefOfficerPosition && $bulkCarrier) {
            Test::create([
                'title' => 'Đánh giá Năng lực Thuyền phó 1 - Tàu Hàng rời',
                'description' => 'Bài kiểm tra đánh giá năng lực thuyền phó 1 trên tàu hàng rời. Tập trung vào kiến thức xếp dỡ hàng rời, ổn định tàu, và an toàn khoang hàng.',
                'position_id' => $chiefOfficerPosition->id,
                'ship_type_id' => $bulkCarrier->id,
                'duration' => 60,
                'is_random' => true,
                'is_active' => true,
                'type' => 'assessment',
                'difficulty' => 'Trung bình',
                'passing_score' => 70,
                'category' => 'Kiểm tra đánh giá năng lực',
                'created_by' => $admin->id,
            ]);
        }
        
        // Đánh giá năng lực Máy trưởng tàu dầu
        if ($chiefEngineerPosition && $oilTanker) {
            Test::create([
                'title' => 'Đánh giá Năng lực Máy trưởng - Tàu dầu',
                'description' => 'Bài kiểm tra đánh giá năng lực máy trưởng trên tàu dầu. Tập trung vào kiến thức về hệ thống bơm dầu, hệ thống kiểm soát khí gas, và an toàn buồng máy.',
                'position_id' => $chiefEngineerPosition->id,
                'ship_type_id' => $oilTanker->id,
                'duration' => 60,
                'is_random' => true,
                'is_active' => true,
                'type' => 'assessment',
                'difficulty' => 'Trung bình',
                'passing_score' => 70,
                'category' => 'Kiểm tra đánh giá năng lực',
                'created_by' => $admin->id,
            ]);
        }
    }
    
    /**
     * Tạo các bài kiểm tra phân loại
     */
    private function createPlacementTests($admin, $captainPosition, $chiefOfficerPosition, $chiefEngineerPosition)
    {
        // Bài kiểm tra phân loại chung
        Test::create([
            'title' => 'Kiểm tra Phân loại Năng lực Hàng hải',
            'description' => 'Bài kiểm tra phân loại trình độ kiến thức hàng hải chung. Giúp xác định mức độ hiểu biết và phân loại thuyền viên theo trình độ.',
            'duration' => 45,
            'is_random' => true,
            'is_active' => true,
            'type' => 'placement',
            'difficulty' => 'Trung bình',
            'category' => 'Kiểm tra phân loại',
            'created_by' => $admin->id,
        ]);
        
        // Bài kiểm tra phân loại cho bộ phận boong
        Test::create([
            'title' => 'Kiểm tra Phân loại Sỹ quan Boong',
            'description' => 'Bài kiểm tra phân loại trình độ kiến thức chuyên môn cho sỹ quan boong. Giúp xác định và phân loại sỹ quan theo năng lực thực tế.',
            'duration' => 60,
            'is_random' => true,
            'is_active' => true,
            'type' => 'placement',
            'difficulty' => 'Trung bình',
            'category' => 'Kiểm tra phân loại',
            'created_by' => $admin->id,
        ]);
        
        // Bài kiểm tra phân loại cho bộ phận máy
        Test::create([
            'title' => 'Kiểm tra Phân loại Sỹ quan Máy',
            'description' => 'Bài kiểm tra phân loại trình độ kiến thức chuyên môn cho sỹ quan máy. Giúp xác định và phân loại sỹ quan theo năng lực thực tế.',
            'duration' => 60,
            'is_random' => true,
            'is_active' => true,
            'type' => 'placement',
            'difficulty' => 'Trung bình',
            'category' => 'Kiểm tra phân loại',
            'created_by' => $admin->id,
        ]);
    }
    
    /**
     * Tạo các bài luyện tập
     */
    private function createPracticeTests($admin, $captainPosition, $chiefOfficerPosition, $chiefEngineerPosition, $bulkCarrier, $oilTanker, $containerShip)
    {
        // Bài luyện tập an toàn hàng hải
        Test::create([
            'title' => 'Luyện tập An toàn Hàng hải',
            'description' => 'Bài luyện tập kiến thức và quy trình an toàn hàng hải cơ bản. Gồm các tình huống thường gặp và biện pháp xử lý.',
            'duration' => 30,
            'is_random' => true,
            'is_active' => true,
            'type' => 'practice',
            'difficulty' => 'Dễ',
            'category' => 'Kiểm tra luyện tập',
            'created_by' => $admin->id,
        ]);
        
        // Bài luyện tập xử lý tình huống khẩn cấp
        Test::create([
            'title' => 'Luyện tập Xử lý Tình huống Khẩn cấp',
            'description' => 'Bài luyện tập kỹ năng xử lý các tình huống khẩn cấp trên tàu biển như cháy, va chạm, thủng tàu, người rơi xuống biển.',
            'duration' => 30,
            'is_random' => true,
            'is_active' => true,
            'type' => 'practice',
            'difficulty' => 'Trung bình',
            'category' => 'Kiểm tra luyện tập',
            'created_by' => $admin->id,
        ]);
        
        // Bài luyện tập điều động tàu
        if ($captainPosition || $chiefOfficerPosition) {
            Test::create([
                'title' => 'Luyện tập Điều động Tàu',
                'description' => 'Bài luyện tập kiến thức và kỹ năng điều động tàu trong các tình huống khác nhau như cập cảng, tránh va, và đi trong luồng hẹp.',
                'position_id' => $captainPosition ? $captainPosition->id : ($chiefOfficerPosition ? $chiefOfficerPosition->id : null),
                'duration' => 30,
                'is_random' => true,
                'is_active' => true,
                'type' => 'practice',
                'difficulty' => 'Khó',
                'category' => 'Kiểm tra luyện tập',
                'created_by' => $admin->id,
            ]);
        }
        
        // Bài luyện tập hệ thống máy tàu
        if ($chiefEngineerPosition) {
            Test::create([
                'title' => 'Luyện tập Hệ thống Máy tàu',
                'description' => 'Bài luyện tập kiến thức về vận hành, bảo dưỡng và xử lý sự cố hệ thống máy tàu biển.',
                'position_id' => $chiefEngineerPosition->id,
                'duration' => 30,
                'is_random' => true,
                'is_active' => true,
                'type' => 'practice',
                'difficulty' => 'Trung bình',
                'category' => 'Kiểm tra luyện tập',
                'created_by' => $admin->id,
            ]);
        }
    }
} 