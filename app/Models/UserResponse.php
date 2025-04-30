<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserResponse extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'test_attempt_id',
        'question_id',
        'answer_id',
        'text_response',
        'response_type',
        'is_marked',
        'score',
        'admin_comment',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_marked' => 'boolean',
        'score' => 'float',
        'response_type' => 'string',
        'admin_comment' => 'string',
    ];

    /**
     * The test attempt that the user response belongs to.
     */
    public function testAttempt()
    {
        return $this->belongsTo(TestAttempt::class);
    }

    /**
     * The question that the user response belongs to.
     */
    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * The answer that the user selected.
     */
    public function answer()
    {
        return $this->belongsTo(Answer::class);
    }

    /**
     * Check if the response is correct.
     */
    public function isCorrect()
    {
        // Trắc nghiệm: kiểm tra đáp án đã chọn có đúng không
        if ($this->answer_id && $this->question->type === 'Trắc nghiệm') {
            return $this->answer->is_correct;
        }
        
        // Các loại câu hỏi khác: dựa vào điểm số đã chấm
        return $this->score > 0;
    }
}
