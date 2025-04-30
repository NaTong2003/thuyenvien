<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Test;
use App\Models\Question;
use App\Models\TestQuestion;

class TestQuestionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy danh sách bài kiểm tra
        $tests = Test::all();
        
        foreach ($tests as $test) {
            // Số câu hỏi cho mỗi bài kiểm tra tùy theo loại
            $questionCount = 0;
            
            switch ($test->type) {
                case 'certification':
                    $questionCount = 40;
                    break;
                case 'assessment':
                    $questionCount = 20;
                    break;
                case 'placement':
                    $questionCount = 30;
                    break;
                case 'practice':
                    $questionCount = 10;
                    break;
                default:
                    $questionCount = 20;
            }
            
            // Lấy câu hỏi phù hợp với bài kiểm tra
            $questions = $this->getQuestionsForTest($test, $questionCount);
            
            // Thêm câu hỏi vào bài kiểm tra
            $order = 1;
            foreach ($questions as $question) {
                // Điểm cho mỗi câu hỏi dựa vào độ khó
                $points = 0;
                switch ($question->difficulty) {
                    case 'Dễ':
                        $points = 1;
                        break;
                    case 'Trung bình':
                        $points = 2;
                        break;
                    case 'Khó':
                        $points = 3;
                        break;
                    default:
                        $points = 1;
                }
                
                TestQuestion::create([
                    'test_id' => $test->id,
                    'question_id' => $question->id,
                    'order' => $order,
                    'points' => $points,
                ]);
                
                $order++;
            }
        }
    }
    
    /**
     * Lấy câu hỏi phù hợp với bài kiểm tra
     */
    private function getQuestionsForTest($test, $count)
    {
        $query = Question::query();
        
        // Lọc câu hỏi theo vị trí
        if ($test->position_id) {
            $query->where(function($q) use ($test) {
                $q->where('position_id', $test->position_id)
                  ->orWhereNull('position_id');
            });
        }
        
        // Lọc câu hỏi theo loại tàu
        if ($test->ship_type_id) {
            $query->where(function($q) use ($test) {
                $q->where('ship_type_id', $test->ship_type_id)
                  ->orWhereNull('ship_type_id');
            });
        }
        
        // Ưu tiên lấy câu hỏi có cùng độ khó với bài kiểm tra
        if ($test->difficulty) {
            $query->orderByRaw("CASE 
                WHEN difficulty = '{$test->difficulty}' THEN 1
                ELSE 2
            END");
        }
        
        // Lấy câu hỏi ngẫu nhiên
        $availableCount = $query->count();
        $finalCount = min($count, $availableCount);
        
        if ($finalCount < $count) {
            $this->command->info("Không đủ câu hỏi cho bài kiểm tra '{$test->title}'. Cần {$count} câu, chỉ có {$availableCount} câu phù hợp.");
        }
        
        return $query->inRandomOrder()->take($finalCount)->get();
    }
} 