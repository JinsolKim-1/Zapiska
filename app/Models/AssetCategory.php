<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetCategory extends Model
{
    use HasFactory;

    protected $table = 'asset_categories';
    protected $primaryKey = 'asset_category_id';
    public $timestamps = false; // since you use asset_created_at manually

    protected $fillable = [
        'company_id',
        'category_name',
        'created_by',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assets()
    {
        return $this->hasMany(Asset::class, 'asset_category_id');
    }
}
