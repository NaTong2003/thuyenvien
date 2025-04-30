<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShipType extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'category',
        'description',
    ];

    /**
     * The questions that are related to the ship type.
     */
    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    /**
     * The tests that are related to the ship type.
     */
    public function tests()
    {
        return $this->hasMany(Test::class);
    }
}
