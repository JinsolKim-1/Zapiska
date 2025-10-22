<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailVerification extends Model
{
    use HasFactory;

    protected $table = 'email_verification';
    protected $primaryKey = 'ver_id';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'ver_code',
        'expire_at',
        'verified_at',
        'created_at',
    ];

    // 🔹 Relationship back to user
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    protected $casts = [
        'expire_at' => 'datetime',
    ];

}
