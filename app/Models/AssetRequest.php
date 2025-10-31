<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetRequest extends Model
{
    use HasFactory;

    protected $primaryKey = 'requests_id';
    protected $table = 'asset_request'; // matches your SQL
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'company_id',
        'sector_id',
        'user_id',
        'asset_name',
        'quantity',
        'status',
        'total_cost',
        'request_date',
    ];

    // 🔹 Relationships
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

    public function receipt()
    {
        return $this->hasOne(Receipt::class, 'requests_id');
    }
}
