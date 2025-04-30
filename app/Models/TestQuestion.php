<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestQuestion extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'test_id',
        'question_id',
        'order',
        'points',
        'is_temporary',
        'test_attempt_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'order' => 'integer',
        'points' => 'float',
        'is_temporary' => 'boolean',
    ];

    /**
     * The test that the test question belongs to.
     */
    public function test()
    {
        return $this->belongsTo(Test::class);
    }

    /**
     * The question that the test question belongs to.
     */
    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * The test attempt that the test question belongs to.
     */
    public function testAttempt()
    {
        return $this->belongsTo(TestAttempt::class);
    }
}
