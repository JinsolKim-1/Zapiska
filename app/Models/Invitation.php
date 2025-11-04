<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Invitation extends Model
{
    use HasFactory;

    protected $primaryKey = 'invitation_id';

    protected $fillable = [
        'company_id',
        'inviter_id',
        'invitee_email',
        'role_id',
        'status',
        'approved_by',
        'invite_token',
        'expires_at',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invitation) {
            $invitation->invite_token = Str::uuid(); // unique token
            $invitation->expires_at = now()->addDays(3); // expires after 3 days
        });
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function inviter()
    {
        return $this->belongsTo(User::class, 'inviter_id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
}
