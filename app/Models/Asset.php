<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;

    protected $table = 'assets';
    protected $primaryKey = 'asset_id';
    public $timestamps = false;

    protected $fillable = [
        'company_id',
        'user_id',
        'asset_category_id',
        'asset_description',
        'purchase_date',
        'purchase_cost',
        'location',
        'asset_status',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category()
    {
        return $this->belongsTo(AssetCategory::class, 'asset_category_id');
    }
    
    public function sector()
    {
        return $this->belongsTo(Sector::class, 'sector_id');
    }
}
