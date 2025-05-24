<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'position_id',
        'ship_type_id',
        'duration',
        'is_random',
        'is_active',
        'type',
        'difficulty',
        'passing_score',
        'category',
        'category_id',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_random' => 'boolean',
        'is_active' => 'boolean',
        'duration' => 'integer',
    ];

    /**
     * The position that the test belongs to.
     */
    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    /**
     * The ship type that the test belongs to.
     */
    public function shipType()
    {
        return $this->belongsTo(ShipType::class);
    }

    /**
     * The category that the test belongs to.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * The user that created the test.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * The questions in the test.
     */
    public function questions()
    {
        return $this->belongsToMany(Question::class, 'test_questions')
            ->withPivot('order', 'points')
            ->withTimestamps();
    }

    /**
     * The test attempts for the test.
     */
    public function testAttempts()
    {
        return $this->hasMany(TestAttempt::class);
    }

    /**
     * The settings for the test.
     */
    public function settings()
    {
        return $this->hasOne(TestSetting::class);
    }
}
