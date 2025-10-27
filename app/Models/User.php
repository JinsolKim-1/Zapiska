<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Role;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $primaryKey = 'user_id';

    protected $fillable = [
        'username',
        'email',
        'password',
        'firstname',
        'lastname',
        'contact',
        'profile',
        'verification',
        'profile_complete',
        'company_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function emailVerifications()
    {
        return $this->hasMany(EmailVerification::class, 'user_id', 'user_id');
    }

    public function latestVerification()
    {
        return $this->hasOne(EmailVerification::class, 'user_id', 'user_id')->latestOfMany('ver_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'company_id');
    }

    public function createdCompany()
    {
        return $this->hasOne(Company::class, 'creator_id', 'user_id');
    }

    // In User.php
    public function role() {
        return $this->belongsTo(Role::class, 'role_id', 'role_id');
    }

}
