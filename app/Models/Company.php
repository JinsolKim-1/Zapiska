<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $table = 'companies';
    protected $primaryKey = 'company_id';

    protected $fillable = [
        'creator_id',
        'company_name',
        'company_email',
        'company_number',
        'company_address',
        'company_desc',
        'company_website',
        'verification_notes',
        'verification_status',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id', 'user_id');
    }
}
