<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginAttempt extends Model
{
    use HasFactory;

    protected $table = 'login_attempts';

    protected $primaryKey = 'log_id';
    public $timestamps = false;

    protected $fillable = [
        'username',
        'remote_ip',
        'success',
        'log_created',
        'log_update'
    ];
}
