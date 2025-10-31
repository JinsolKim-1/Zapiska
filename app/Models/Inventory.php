<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $table = 'inventory'; // <- specify the table name

    protected $primaryKey = 'inventory_id';

    protected $fillable = [
        'company_id',
        'asset_category_id',
        'asset_name',
        'description',
        'quantity',
        'unit_cost',
        'reorder_level',
        'last_restock',
        'supplier',
    ];

    // Relationship to category
    public function category()
    {
        return $this->belongsTo(AssetCategory::class, 'asset_category_id');
    }
}
