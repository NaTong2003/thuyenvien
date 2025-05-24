<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestSetting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'test_id',
        'shuffle_questions',
        'shuffle_answers',
        'allow_back',
        'show_result_immediately',
        'max_attempts',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'shuffle_questions' => 'boolean',
        'shuffle_answers' => 'boolean',
        'allow_back' => 'boolean',
        'show_result_immediately' => 'boolean',
        'max_attempts' => 'integer',
    ];

    /**
     * The test that the settings belong to.
     */
    public function test()
    {
        return $this->belongsTo(Test::class);
    }
} 