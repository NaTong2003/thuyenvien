<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Test;
use App\Models\TestAttempt;
use App\Models\UserResponse;
use App\Models\Question;
use App\Models\Answer;
use Carbon\Carbon;

class TestAttemptsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy danh sách thuyền viên
        $seafarerRoleId = \App\Models\Role::where('name', 'Thuyền viên')->first()->id;
        $seafarers = User::where('role_id', $seafarerRoleId)->get();
        
        if ($seafarers->isEmpty()) {
            $this->command->error('Không có thuyền viên nào! Hãy chạy SeafarerUserSeeder trước.');
            return;
        }
        
        // Lấy danh sách bài kiểm tra
        $tests = Test::all();
        
        if ($tests->isEmpty()) {
            $this->command->error('Không có bài kiểm tra nào! Hãy chạy TestsSeeder trước.');
            return;
        }
        
        // Tạo các lượt thi hoàn thành
        $this->createCompletedAttempts($seafarers, $tests);
        
        // Tạo các lượt thi đang thực hiện
        $this->createOngoingAttempts($seafarers, $tests);
    }
    
    /**
     * Tạo các lượt thi đã hoàn thành
     */
    private function createCompletedAttempts($seafarers, $tests)
    {
        // Mỗi thuyền viên sẽ có một số lượt thi đã hoàn thành
        foreach ($seafarers as $seafarer) {
            // Lấy một số bài kiểm tra phù hợp với thuyền viên
            $suitableTests = $this->getSuitableTests($seafarer, $tests);
            
            // Số lượt thi cho mỗi thuyền viên
            $attemptCount = rand(2, 5);
            
            for ($i = 0; $i < $attemptCount && $i < count($suitableTests); $i++) {
                $test = $suitableTests[$i];
                
                // Lấy các câu hỏi của bài kiểm tra
                $questions = $test->questions()->withPivot('points')->get();
                
                if ($questions->count() > 0) {
                    // Tạo ngày giờ bắt đầu và kết thúc
                    $startTime = Carbon::now()->subDays(rand(1, 30))->subHours(rand(1, 24));
                    $endTime = (clone $startTime)->addMinutes($test->duration);
                    
                    // Tính điểm ngẫu nhiên, ưu tiên điểm cao hơn cho thuyền viên có kinh nghiệm cao
                    $experience = $seafarer->thuyenVien ? $seafarer->thuyenVien->experience : 0;
                    $baseScore = 50 + min(30, $experience * 2); // Điểm cơ bản dựa trên kinh nghiệm
                    $randomFactor = rand(-10, 20); // Yếu tố ngẫu nhiên
                    $score = min(100, max(0, $baseScore + $randomFactor));
                    
                    // Tạo lượt thi
                    $testAttempt = TestAttempt::create([
                        'user_id' => $seafarer->id,
                        'test_id' => $test->id,
                        'start_time' => $startTime,
                        'end_time' => $endTime,
                        'is_completed' => true,
                        'score' => $score,
                    ]);
                    
                    // Tạo câu trả lời cho lượt thi
                    $this->createResponsesForAttempt($testAttempt, $questions, $score);
                }
            }
        }
    }
    
    /**
     * Tạo các lượt thi đang thực hiện
     */
    private function createOngoingAttempts($seafarers, $tests)
    {
        // Chọn một số thuyền viên ngẫu nhiên để tạo lượt thi đang thực hiện
        $ongoingSeafarers = $seafarers->random(min(3, $seafarers->count()));
        
        foreach ($ongoingSeafarers as $seafarer) {
            // Lấy một bài kiểm tra phù hợp
            $suitableTests = $this->getSuitableTests($seafarer, $tests);
            
            if (count($suitableTests) > 0) {
                $test = $suitableTests[0];
                
                // Lấy các câu hỏi của bài kiểm tra
                $questions = $test->questions()->withPivot('points')->get();
                
                if ($questions->count() > 0) {
                    // Tạo ngày giờ bắt đầu trong vòng 2 giờ qua
                    $startTime = Carbon::now()->subMinutes(rand(5, 120));
                    
                    // Tạo lượt thi đang thực hiện (chưa có end_time)
                    $testAttempt = TestAttempt::create([
                        'user_id' => $seafarer->id,
                        'test_id' => $test->id,
                        'start_time' => $startTime,
                        'is_completed' => false,
                    ]);
                    
                    // Tạo câu trả lời cho một phần các câu hỏi
                    $answeredCount = min($questions->count(), rand(1, (int)($questions->count() * 0.7)));
                    $partialQuestions = $questions->take($answeredCount);
                    
                    $this->createResponsesForAttempt($testAttempt, $partialQuestions, 0);
                }
            }
        }
    }
    
    /**
     * Lấy các bài kiểm tra phù hợp với thuyền viên
     */
    private function getSuitableTests($seafarer, $tests)
    {
        $thuyenVien = $seafarer->thuyenVien;
        
        // Nếu thuyền viên không có thông tin chi tiết, lấy các bài kiểm tra không có ràng buộc
        if (!$thuyenVien || !$thuyenVien->position_id) {
            return $tests->filter(function($test) {
                return !$test->position_id && !$test->ship_type_id;
            })->shuffle()->values()->all();
        }
        
        // Lọc các bài kiểm tra phù hợp với vị trí và loại tàu
        $suitableTests = $tests->filter(function($test) use ($thuyenVien) {
            // Phù hợp nếu không có ràng buộc hoặc trùng với thông tin thuyền viên
            $positionMatch = !$test->position_id || $test->position_id == $thuyenVien->position_id;
            $shipTypeMatch = !$test->ship_type_id || $test->ship_type_id == $thuyenVien->ship_type_id;
            
            return $positionMatch && $shipTypeMatch;
        })->shuffle()->values()->all();
        
        return $suitableTests;
    }
    
    /**
     * Tạo câu trả lời cho lượt thi
     */
    private function createResponsesForAttempt($testAttempt, $questions, $targetScore)
    {
        $totalPoints = 0;
        $earnedPoints = 0;
        
        foreach ($questions as $question) {
            $points = $question->pivot->points;
            $totalPoints += $points;
            
            // Quyết định xem câu trả lời có đúng không dựa trên điểm mục tiêu
            $correctProbability = min(95, max(5, $targetScore)) / 100;
            $isCorrect = rand(0, 100) / 100 < $correctProbability;
            
            if ($question->type == 'Trắc nghiệm') {
                // Lấy đáp án đúng và sai
                $correctAnswer = $question->answers()->where('is_correct', true)->first();
                $incorrectAnswers = $question->answers()->where('is_correct', false)->get();
                
                if ($correctAnswer && $incorrectAnswers->count() > 0) {
                    // Chọn đáp án dựa trên xác suất đúng/sai
                    $answer = $isCorrect ? $correctAnswer : $incorrectAnswers->random();
                    
                    UserResponse::create([
                        'test_attempt_id' => $testAttempt->id,
                        'question_id' => $question->id,
                        'answer_id' => $answer->id,
                        'score' => $isCorrect ? $points : 0,
                    ]);
                    
                    if ($isCorrect) {
                        $earnedPoints += $points;
                    }
                }
            } else {
                // Câu hỏi tự luận, tình huống, thực hành
                $score = $isCorrect ? $points : rand(0, $points - 1);
                
                UserResponse::create([
                    'test_attempt_id' => $testAttempt->id,
                    'question_id' => $question->id,
                    'text_response' => "Câu trả lời mẫu cho câu hỏi " . $question->id,
                    'is_marked' => true,
                    'score' => $score,
                ]);
                
                $earnedPoints += $score;
            }
        }
        
        // Cập nhật điểm số nếu là lượt thi đã hoàn thành
        if ($testAttempt->is_completed && $totalPoints > 0) {
            $calculatedScore = ($earnedPoints / $totalPoints) * 100;
            
            // Làm tròn đến 2 chữ số thập phân
            $calculatedScore = round($calculatedScore, 2);
            
            TestAttempt::where('id', $testAttempt->id)->update(['score' => $calculatedScore]);
        }
    }
} 