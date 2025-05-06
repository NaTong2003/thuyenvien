<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'seafarer_id',
        'phone',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the role that the user belongs to.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the thuyenVien associated with the user.
     */
    public function thuyenVien()
    {
        return $this->hasOne(ThuyenVien::class);
    }

    /**
     * Get the test attempts for the user.
     */
    public function testAttempts()
    {
        return $this->hasMany(TestAttempt::class);
    }

    /**
     * Get the certificates for the user.
     */
    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    /**
     * Get the certificates issued by the user.
     */
    public function issuedCertificates()
    {
        return $this->hasMany(Certificate::class, 'issued_by');
    }

    /**
     * Check if the user has admin role.
     */
    public function isAdmin()
    {
        return $this->role && $this->role->name === 'Admin';
    }

    /**
     * Check if the user is a thuyenVien.
     */
    public function isThuyenVien()
    {
        return $this->role && $this->role->name === 'Thuyền viên';
    }
}
