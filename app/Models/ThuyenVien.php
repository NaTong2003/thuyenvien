<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThuyenVien extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'position_id',
        'ship_type_id',
        'experience',
        'age',
    ];

    /**
     * Get the user that owns the thuyenVien.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the position that the thuyenVien belongs to.
     */
    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    /**
     * Get the ship type that the thuyenVien belongs to.
     */
    public function shipType()
    {
        return $this->belongsTo(ShipType::class);
    }
}
