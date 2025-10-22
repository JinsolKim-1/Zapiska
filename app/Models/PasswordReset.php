<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PasswordReset extends Model
{
    use HasFactory;

    // 🔹 Table name
    protected $table = 'password_reset';

    // 🔹 Primary key
    protected $primaryKey = 'reset_id';

    // 🔹 Disable default timestamps
    public $timestamps = false;

    // 🔹 Fillable fields
    protected $fillable = [
        'email',
        'res_token',       // store hashed token
        'res_created_at',
    ];

    // 🔹 Casts
    protected $casts = [
        'res_created_at' => 'datetime',
    ];

    /**
     * Check if the token is expired
     *
     * @param int $minutes Validity in minutes
     * @return bool
     */
    public function isExpired($minutes = 10)
    {
        return $this->res_created_at->addMinutes($minutes)->lt(now());
    }
}
