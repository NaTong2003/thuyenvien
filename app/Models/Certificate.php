<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
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
        'test_attempt_id',
        'certificate_number',
        'title',
        'description',
        'issue_date',
        'expiry_date',
        'status',
        'issued_by',
        'revocation_reason',
        'certificate_file',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
    ];

    /**
     * The user that owns the certificate.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The test associated with the certificate.
     */
    public function test()
    {
        return $this->belongsTo(Test::class);
    }

    /**
     * The test attempt associated with the certificate.
     */
    public function testAttempt()
    {
        return $this->belongsTo(TestAttempt::class);
    }

    /**
     * The user that issued the certificate.
     */
    public function issuer()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    /**
     * Check if the certificate is active.
     */
    public function isActive()
    {
        return $this->status === 'active' && ($this->expiry_date === null || $this->expiry_date->isFuture());
    }

    /**
     * Check if the certificate is expired.
     */
    public function isExpired()
    {
        return $this->expiry_date !== null && $this->expiry_date->isPast();
    }

    /**
     * Check if the certificate is revoked.
     */
    public function isRevoked()
    {
        return $this->status === 'revoked';
    }
}
