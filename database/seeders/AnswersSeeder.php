<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Question;
use App\Models\Answer;

class AnswersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy tất cả câu hỏi trắc nghiệm
        $questions = Question::where('type', 'Trắc nghiệm')->get();
        
        foreach ($questions as $question) {
            // Tạo câu trả lời dựa trên nội dung câu hỏi
            switch ($question->content) {
                case 'Khi gặp tình huống người rơi xuống biển, hành động đầu tiên cần làm là gì?':
                    $this->createAnswersForManOverboard($question);
                    break;
                    
                case 'Các thiết bị cứu sinh bắt buộc trên tàu biển bao gồm những gì?':
                    $this->createAnswersForLifesavingEquipment($question);
                    break;
                    
                case 'Cách xác định vị trí tàu trên biển bằng các phương pháp thiên văn?':
                    $this->createAnswersForCelestialNavigation($question);
                    break;
                    
                case 'Các quy tắc điều động tàu trong luồng lạch hẹp là gì?':
                    $this->createAnswersForNarrowChannelRules($question);
                    break;
                    
                case 'Các thông số vận hành chuẩn của động cơ chính là gì?':
                    $this->createAnswersForEngineParameters($question);
                    break;
                    
                case 'Quy trình bảo dưỡng định kỳ hệ thống làm mát động cơ?':
                    $this->createAnswersForCoolingSystem($question);
                    break;
                    
                case 'Các quy trình an toàn đặc biệt khi vận chuyển dầu thô là gì?':
                    $this->createAnswersForOilTransportSafety($question);
                    break;
                    
                case 'Các thiết bị đo và kiểm soát khí gas trên tàu dầu?':
                    $this->createAnswersForGasMonitoringEquipment($question);
                    break;
                    
                default:
                    // Tạo câu trả lời mặc định nếu không khớp với câu hỏi nào ở trên
                    $this->createDefaultAnswers($question);
                    break;
            }
        }
    }
    
    /**
     * Tạo câu trả lời cho câu hỏi người rơi xuống biển
     */
    private function createAnswersForManOverboard($question)
    {
        Answer::create([
            'question_id' => $question->id,
            'content' => 'Hét lên "Người rơi xuống biển" và ném ngay phao cứu sinh về phía nạn nhân.',
            'is_correct' => true,
            'explanation' => 'Ném phao cứu sinh ngay lập tức giúp nạn nhân có điểm tựa và dễ dàng nhìn thấy vị trí từ xa.',
        ]);
        
        Answer::create([
            'question_id' => $question->id,
            'content' => 'Điều động tàu quay lại vị trí nạn nhân ngay lập tức.',
            'is_correct' => false,
            'explanation' => 'Việc điều động tàu quay lại phải tuân theo quy trình và không phải là hành động đầu tiên.',
        ]);
        
        Answer::create([
            'question_id' => $question->id,
            'content' => 'Báo cáo ngay cho thuyền trưởng.',
            'is_correct' => false,
            'explanation' => 'Báo cáo cho thuyền trưởng là cần thiết nhưng không phải hành động đầu tiên cần làm.',
        ]);
        
        Answer::create([
            'question_id' => $question->id,
            'content' => 'Nhảy xuống biển để cứu nạn nhân.',
            'is_correct' => false,
            'explanation' => 'Hành động này rất nguy hiểm và có thể gây thêm nạn nhân.',
        ]);
    }
    
    /**
     * Tạo câu trả lời cho câu hỏi về thiết bị cứu sinh
     */
    private function createAnswersForLifesavingEquipment($question)
    {
        Answer::create([
            'question_id' => $question->id,
            'content' => 'Phao áo, phao tròn, xuồng cứu sinh và bè cứu sinh.',
            'is_correct' => true,
            'explanation' => 'Đây là những thiết bị cứu sinh bắt buộc theo quy định SOLAS.',
        ]);
        
        Answer::create([
            'question_id' => $question->id,
            'content' => 'Chỉ cần phao áo và xuồng cứu sinh là đủ.',
            'is_correct' => false,
            'explanation' => 'Thiếu các thiết bị cứu sinh bắt buộc khác theo quy định.',
        ]);
        
        Answer::create([
            'question_id' => $question->id,
            'content' => 'Máy bay trực thăng cứu hộ.',
            'is_correct' => false,
            'explanation' => 'Đây không phải là thiết bị cứu sinh bắt buộc trên tàu.',
        ]);
        
        Answer::create([
            'question_id' => $question->id,
            'content' => 'Tàu cứu hộ đi kèm.',
            'is_correct' => false,
            'explanation' => 'Đây không phải là thiết bị cứu sinh bắt buộc trên tàu.',
        ]);
    }
    
    /**
     * Tạo câu trả lời cho câu hỏi về phương pháp thiên văn
     */
    private function createAnswersForCelestialNavigation($question)
    {
        Answer::create([
            'question_id' => $question->id,
            'content' => 'Sử dụng sextant để đo góc cao của thiên thể và tính toán vị trí dựa trên bảng hải đồ thiên văn.',
            'is_correct' => true,
            'explanation' => 'Đây là phương pháp chính xác để xác định vị trí tàu bằng thiên văn.',
        ]);
        
        Answer::create([
            'question_id' => $question->id,
            'content' => 'Chỉ cần nhìn vào vị trí mặt trời.',
            'is_correct' => false,
            'explanation' => 'Phương pháp này không đủ chính xác để xác định vị trí tàu.',
        ]);
        
        Answer::create([
            'question_id' => $question->id,
            'content' => 'Dựa vào la bàn từ và hướng sao Bắc Đẩu.',
            'is_correct' => false,
            'explanation' => 'Phương pháp này chỉ xác định được hướng Bắc chứ không xác định được vị trí chính xác.',
        ]);
        
        Answer::create([
            'question_id' => $question->id,
            'content' => 'Dựa vào GPS, không cần thiên văn.',
            'is_correct' => false,
            'explanation' => 'GPS là phương pháp hiện đại nhưng câu hỏi yêu cầu xác định bằng phương pháp thiên văn.',
        ]);
    }
    
    /**
     * Tạo câu trả lời cho câu hỏi về quy tắc điều động tàu
     */
    private function createAnswersForNarrowChannelRules($question)
    {
        Answer::create([
            'question_id' => $question->id,
            'content' => 'Đi bên phải của luồng, giảm tốc độ và tuân thủ tín hiệu giao thông thủy.',
            'is_correct' => true,
            'explanation' => 'Theo quy tắc quốc tế phòng ngừa đâm va trên biển COLREG.',
        ]);
        
        Answer::create([
            'question_id' => $question->id,
            'content' => 'Đi giữa luồng để có đủ độ sâu.',
            'is_correct' => false,
            'explanation' => 'Điều này vi phạm quy tắc luồng lạch hẹp trong COLREG.',
        ]);
        
        Answer::create([
            'question_id' => $question->id,
            'content' => 'Tăng tốc để qua luồng nhanh chóng.',
            'is_correct' => false,
            'explanation' => 'Việc tăng tốc có thể gây nguy hiểm trong luồng hẹp.',
        ]);
        
        Answer::create([
            'question_id' => $question->id,
            'content' => 'Không cần tuân thủ quy tắc nào nếu là tàu lớn.',
            'is_correct' => false,
            'explanation' => 'Mọi tàu đều phải tuân thủ quy tắc hàng hải.',
        ]);
    }
    
    /**
     * Tạo câu trả lời cho câu hỏi về thông số động cơ
     */
    private function createAnswersForEngineParameters($question)
    {
        Answer::create([
            'question_id' => $question->id,
            'content' => 'Nhiệt độ nước làm mát, áp suất dầu bôi trơn, tốc độ vòng quay, và nhiệt độ khí xả.',
            'is_correct' => true,
            'explanation' => 'Đây là những thông số quan trọng cần giám sát khi vận hành động cơ chính.',
        ]);
        
        Answer::create([
            'question_id' => $question->id,
            'content' => 'Chỉ cần theo dõi tốc độ vòng quay của động cơ.',
            'is_correct' => false,
            'explanation' => 'Không đủ thông số để đảm bảo động cơ hoạt động an toàn.',
        ]);
        
        Answer::create([
            'question_id' => $question->id,
            'content' => 'Màu sắc của khói thải từ ống khói.',
            'is_correct' => false,
            'explanation' => 'Đây là một dấu hiệu nhưng không phải thông số vận hành chuẩn.',
        ]);
        
        Answer::create([
            'question_id' => $question->id,
            'content' => 'Mức tiêu thụ nhiên liệu theo giờ.',
            'is_correct' => false,
            'explanation' => 'Đây là thông số quan trọng nhưng không phải thông số chính để giám sát an toàn động cơ.',
        ]);
    }
    
    /**
     * Tạo câu trả lời cho câu hỏi về hệ thống làm mát
     */
    private function createAnswersForCoolingSystem($question)
    {
        Answer::create([
            'question_id' => $question->id,
            'content' => 'Kiểm tra rò rỉ, vệ sinh két giãn nở, kiểm tra bơm nước, thay thế chất làm mát theo định kỳ.',
            'is_correct' => true,
            'explanation' => 'Đây là quy trình bảo dưỡng chuẩn cho hệ thống làm mát.',
        ]);
        
        Answer::create([
            'question_id' => $question->id,
            'content' => 'Chỉ cần thay nước làm mát mỗi năm một lần.',
            'is_correct' => false,
            'explanation' => 'Quy trình này không đầy đủ cho bảo dưỡng hệ thống làm mát.',
        ]);
        
        Answer::create([
            'question_id' => $question->id,
            'content' => 'Kiểm tra khi động cơ gặp sự cố quá nhiệt.',
            'is_correct' => false,
            'explanation' => 'Đây là xử lý sự cố, không phải bảo dưỡng định kỳ.',
        ]);
        
        Answer::create([
            'question_id' => $question->id,
            'content' => 'Bảo dưỡng khi kết thúc hành trình.',
            'is_correct' => false,
            'explanation' => 'Bảo dưỡng định kỳ phải theo lịch cụ thể, không phải chỉ sau mỗi hành trình.',
        ]);
    }
    
    /**
     * Tạo câu trả lời cho câu hỏi về an toàn vận chuyển dầu
     */
    private function createAnswersForOilTransportSafety($question)
    {
        Answer::create([
            'question_id' => $question->id,
            'content' => 'Kiểm soát khí gas, hệ thống trơ, thiết bị chống tĩnh điện, và quy trình xếp dỡ đặc biệt.',
            'is_correct' => true,
            'explanation' => 'Đây là các quy trình an toàn đặc biệt khi vận chuyển dầu thô.',
        ]);
        
        Answer::create([
            'question_id' => $question->id,
            'content' => 'Chỉ cần hệ thống phòng cháy chữa cháy.',
            'is_correct' => false,
            'explanation' => 'Hệ thống PCCC là cần thiết nhưng không đủ cho an toàn tàu dầu.',
        ]);
        
        Answer::create([
            'question_id' => $question->id,
            'content' => 'Cùng quy trình an toàn như tàu chở hàng thông thường.',
            'is_correct' => false,
            'explanation' => 'Tàu dầu cần quy trình an toàn đặc biệt khác với tàu thông thường.',
        ]);
        
        Answer::create([
            'question_id' => $question->id,
            'content' => 'Kiểm tra độ kín của két chứa mỗi tháng.',
            'is_correct' => false,
            'explanation' => 'Đây chỉ là một phần nhỏ trong quy trình an toàn tàu dầu.',
        ]);
    }
    
    /**
     * Tạo câu trả lời cho câu hỏi về thiết bị đo khí gas
     */
    private function createAnswersForGasMonitoringEquipment($question)
    {
        Answer::create([
            'question_id' => $question->id,
            'content' => 'Máy đo nồng độ khí hydrocarbon, máy đo oxy, máy đo khí H2S, và hệ thống báo động khí gas tự động.',
            'is_correct' => true,
            'explanation' => 'Đây là những thiết bị cần thiết để giám sát khí gas trên tàu dầu.',
        ]);
        
        Answer::create([
            'question_id' => $question->id,
            'content' => 'Chỉ cần máy đo nồng độ oxy.',
            'is_correct' => false,
            'explanation' => 'Không đủ để kiểm soát các loại khí nguy hiểm khác.',
        ]);
        
        Answer::create([
            'question_id' => $question->id,
            'content' => 'Máy đo khí CO2 từ hệ thống chữa cháy.',
            'is_correct' => false,
            'explanation' => 'Đây là thiết bị cho hệ thống chữa cháy, không phải để kiểm soát khí gas từ hàng hóa.',
        ]);
        
        Answer::create([
            'question_id' => $question->id,
            'content' => 'Thiết bị đo áp suất trong két dầu.',
            'is_correct' => false,
            'explanation' => 'Đây là thiết bị đo áp suất, không phải thiết bị giám sát khí gas.',
        ]);
    }
    
    /**
     * Tạo câu trả lời mặc định cho các câu hỏi khác
     */
    private function createDefaultAnswers($question)
    {
        // Tạo 4 phương án trả lời mặc định, một đúng và ba sai
        Answer::create([
            'question_id' => $question->id,
            'content' => 'Phương án A (đúng)',
            'is_correct' => true,
            'explanation' => 'Đây là đáp án đúng vì lý do [giải thích tương ứng].',
        ]);
        
        Answer::create([
            'question_id' => $question->id,
            'content' => 'Phương án B',
            'is_correct' => false,
            'explanation' => 'Đây là đáp án sai vì [giải thích lý do sai].',
        ]);
        
        Answer::create([
            'question_id' => $question->id,
            'content' => 'Phương án C',
            'is_correct' => false,
            'explanation' => 'Đây là đáp án sai vì [giải thích lý do sai].',
        ]);
        
        Answer::create([
            'question_id' => $question->id,
            'content' => 'Phương án D',
            'is_correct' => false,
            'explanation' => 'Đây là đáp án sai vì [giải thích lý do sai].',
        ]);
    }
} 