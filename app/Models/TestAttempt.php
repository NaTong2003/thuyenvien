<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestAttempt extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'test_id',
        'start_time',
        'end_time',
        'is_completed',
        'needs_marking',
        'is_marked',
        'score',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_completed' => 'boolean',
        'needs_marking' => 'boolean',
        'is_marked' => 'boolean',
        'score' => 'float',
    ];

    /**
     * The user that made the test attempt.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The test that the test attempt belongs to.
     */
    public function test()
    {
        return $this->belongsTo(Test::class);
    }

    /**
     * The user responses for the test attempt.
     */
    public function userResponses()
    {
        return $this->hasMany(UserResponse::class);
    }

    /**
     * The certificates associated with this test attempt.
     */
    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    /**
     * Get the duration of the test attempt in minutes.
     */
    public function getDurationInMinutes()
    {
        if (!$this->end_time || !$this->start_time) {
            return null;
        }

        return $this->start_time->diffInMinutes($this->end_time);
    }

    /**
     * Check if the test attempt is in progress.
     */
    public function isInProgress()
    {
        return $this->start_time && !$this->end_time;
    }

    /**
     * Check if the test attempt is completed.
     */
    public function isCompleted()
    {
        return $this->is_completed;
    }

    /**
     * Check if the test attempt passed.
     */
    public function isPassed()
    {
        // Lấy điểm chuẩn từ bài kiểm tra hoặc sử dụng giá trị mặc định 50
        $passingScore = $this->test->passing_score ?? 50;
        return $this->is_completed && $this->score >= $passingScore;
    }
}
