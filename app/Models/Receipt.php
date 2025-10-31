<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    use HasFactory;

    // Match your primary key
    protected $primaryKey = 'receipt_id';
    public $incrementing = true;
    protected $keyType = 'int';

    // Explicit table name
    protected $table = 'receipts';
    public $timestamps = false; 
    // Allow mass assignment for these fields
    protected $fillable = [
        'company_id',
        'requests_id',
        'sector_id',
        'user_id',
        'asset_name',
        'quantity',
        'total_cost',
        'approved_by',
        'verification_code',
        'qr_code_path',
        'receipt_number',
        'request_status',
        'receipt_date',
        'receipt_image',
    ];
        protected $casts = [
        'receipt_date' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function sector()
    {
        return $this->belongsTo(Sector::class, 'sector_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function assetRequest()
    {
        return $this->belongsTo(AssetRequest::class, 'requests_id');
    }
}
