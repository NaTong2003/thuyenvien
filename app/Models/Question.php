<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'content',
        'category',
        'category_id',
        'type',
        'difficulty',
        'position_id',
        'ship_type_id',
        'created_by',
    ];

    /**
     * The position that the question belongs to.
     */
    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    /**
     * The ship type that the question belongs to.
     */
    public function shipType()
    {
        return $this->belongsTo(ShipType::class);
    }

    /**
     * The category that the question belongs to.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * The user that created the question.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * The answers for the question.
     */
    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    /**
     * The tests that contain this question.
     */
    public function tests()
    {
        return $this->belongsToMany(Test::class, 'test_questions')
            ->withPivot('order', 'points')
            ->withTimestamps();
    }

    /**
     * The user responses for the question.
     */
    public function userResponses()
    {
        return $this->hasMany(UserResponse::class);
    }
}
