<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Question;
use App\Models\Position;
use App\Models\ShipType;
use App\Models\User;

class QuestionsSeeder extends Seeder
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
        
        // Lấy vị trí và loại tàu để sử dụng trong câu hỏi
        $boongPositions = Position::where('department', 'Boong')->get();
        $mayPositions = Position::where('department', 'Máy')->get();
        $cargoShips = ShipType::where('category', 'Cargo')->get();
        
        // Câu hỏi chung cho tất cả các vị trí
        $commonQuestions = [
            [
                'content' => 'Khi gặp tình huống người rơi xuống biển, hành động đầu tiên cần làm là gì?',
                'type' => 'Trắc nghiệm',
                'difficulty' => 'Trung bình',
            ],
            [
                'content' => 'Các thiết bị cứu sinh bắt buộc trên tàu biển bao gồm những gì?',
                'type' => 'Trắc nghiệm',
                'difficulty' => 'Dễ',
            ],
            [
                'content' => 'Quy trình báo động khẩn cấp trên tàu biển được thực hiện như thế nào?',
                'type' => 'Tự luận',
                'difficulty' => 'Khó',
            ],
            [
                'content' => 'Các biện pháp an toàn cần thực hiện khi có bão trên biển?',
                'type' => 'Tự luận',
                'difficulty' => 'Trung bình',
            ],
            [
                'content' => 'Trong tình huống hỏa hoạn trên tàu, bạn sẽ thực hiện các bước nào theo thứ tự?',
                'type' => 'Tình huống',
                'difficulty' => 'Khó',
            ],
        ];
        
        // Câu hỏi dành cho bộ phận boong
        $deckQuestions = [
            [
                'content' => 'Các loại bản đồ hàng hải (sea chart) và cách sử dụng chúng?',
                'type' => 'Tự luận',
                'difficulty' => 'Trung bình',
            ],
            [
                'content' => 'Cách xác định vị trí tàu trên biển bằng các phương pháp thiên văn?',
                'type' => 'Trắc nghiệm',
                'difficulty' => 'Khó',
            ],
            [
                'content' => 'Các quy tắc điều động tàu trong luồng lạch hẹp là gì?',
                'type' => 'Trắc nghiệm',
                'difficulty' => 'Trung bình',
            ],
            [
                'content' => 'Quy trình cập cảng an toàn bao gồm những bước nào?',
                'type' => 'Tình huống',
                'difficulty' => 'Khó',
            ],
            [
                'content' => 'Các thiết bị định vị hàng hải hiện đại và cách sử dụng chúng?',
                'type' => 'Thực hành',
                'difficulty' => 'Trung bình',
            ],
        ];
        
        // Câu hỏi dành cho bộ phận máy
        $engineQuestions = [
            [
                'content' => 'Cách kiểm tra và xử lý sự cố động cơ diesel tàu biển?',
                'type' => 'Tự luận',
                'difficulty' => 'Khó',
            ],
            [
                'content' => 'Các thông số vận hành chuẩn của động cơ chính là gì?',
                'type' => 'Trắc nghiệm',
                'difficulty' => 'Trung bình',
            ],
            [
                'content' => 'Quy trình bảo dưỡng định kỳ hệ thống làm mát động cơ?',
                'type' => 'Trắc nghiệm',
                'difficulty' => 'Dễ',
            ],
            [
                'content' => 'Xử lý tình huống khi lọc dầu bị tắc nghẽn?',
                'type' => 'Tình huống',
                'difficulty' => 'Trung bình',
            ],
            [
                'content' => 'Cách kiểm tra và hiệu chỉnh hệ thống điện trên tàu?',
                'type' => 'Thực hành',
                'difficulty' => 'Khó',
            ],
        ];
        
        // Câu hỏi cho tàu chở dầu
        $tankerQuestions = [
            [
                'content' => 'Các quy trình an toàn đặc biệt khi vận chuyển dầu thô là gì?',
                'type' => 'Trắc nghiệm',
                'difficulty' => 'Khó',
            ],
            [
                'content' => 'Cách ứng phó với tràn dầu trên biển?',
                'type' => 'Tình huống',
                'difficulty' => 'Khó',
            ],
            [
                'content' => 'Các thiết bị đo và kiểm soát khí gas trên tàu dầu?',
                'type' => 'Trắc nghiệm',
                'difficulty' => 'Trung bình',
            ],
        ];
        
        // Thêm câu hỏi chung
        foreach ($commonQuestions as $question) {
            Question::create([
                'content' => $question['content'],
                'category' => 'An toàn hàng hải',
                'type' => $question['type'],
                'difficulty' => $question['difficulty'],
                'created_by' => $admin->id,
            ]);
        }
        
        // Thêm câu hỏi cho bộ phận boong
        foreach ($deckQuestions as $question) {
            foreach ($boongPositions as $position) {
                Question::create([
                    'content' => $question['content'],
                    'category' => 'An toàn hàng hải',
                    'type' => $question['type'],
                    'difficulty' => $question['difficulty'],
                    'position_id' => $position->id,
                    'created_by' => $admin->id,
                ]);
            }
        }
        
        // Thêm câu hỏi cho bộ phận máy
        foreach ($engineQuestions as $question) {
            foreach ($mayPositions as $position) {
                Question::create([
                    'content' => $question['content'],
                    'category' => 'An toàn hàng hải',
                    'type' => $question['type'],
                    'difficulty' => $question['difficulty'],
                    'position_id' => $position->id,
                    'created_by' => $admin->id,
                ]);
            }
        }
        
        // Thêm câu hỏi cho tàu chở dầu
        $oilTanker = ShipType::where('name', 'like', '%dầu%')->first();
        if ($oilTanker) {
            foreach ($tankerQuestions as $question) {
                Question::create([
                    'content' => $question['content'],
                    'category' => 'An toàn hàng hải',
                    'type' => $question['type'],
                    'difficulty' => $question['difficulty'],
                    'ship_type_id' => $oilTanker->id,
                    'created_by' => $admin->id,
                ]);
            }
        }
    }
} 