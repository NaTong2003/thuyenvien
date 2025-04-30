<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'department',
        'description',
    ];

    /**
     * The thuyenViens that belong to the position.
     */
    public function thuyenViens()
    {
        return $this->hasMany(ThuyenVien::class);
    }

    /**
     * The questions that are related to the position.
     */
    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    /**
     * The tests that are related to the position.
     */
    public function tests()
    {
        return $this->hasMany(Test::class);
    }
}
