<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SuperAdmin extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'superadmins';
    protected $primaryKey = 'super_id';
    public $timestamps = false;

    protected $fillable = [
        'super_username',
        'super_email',
        'super_password',
        'first_name',
        'last_name',
        'contact',
        'profile',
        'status',
    ];

    protected $hidden = [
        'super_password',
    ];

    public function getAuthPassword()
    {
        return $this->super_password;
    }
}
