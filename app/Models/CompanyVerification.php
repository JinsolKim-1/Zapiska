<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyVerification extends Model
{
    use HasFactory;

    protected $table = 'company_verification';
    protected $primaryKey = 'verification_id';
    protected $fillable = ['company_id','company_token','expires_at','verified_at','status'];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'company_id');
    }

    public function isExpired(): bool
    {
        return now()->gt($this->expires_at);
    }

}
