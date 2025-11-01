<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\AssetCategory;
class Order extends Model
{
    use HasFactory;

    protected $primaryKey = 'orders_id';
    public $timestamps = false;

    protected $fillable = [
        'company_id',
        'vendor_id',
        'asset_category_id',
        'requests_id',
        'created_by',
        'item_name',
        'item_type',
        'quantity',
        'unit_cost',
        'order_status',
        'expected_delivery',
        'delivered_at'
    ];

    // Relationship to Asset Request
    public function request() {
        return $this->belongsTo(AssetRequest::class, 'requests_id');
    }

    // Relationship to Vendor
    public function vendor() {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    // Relationship to User who created the order
    public function creator() {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Total cost accessor (already computed in DB, optional here)
    public function getTotalCostAttribute() {
        return $this->quantity * $this->unit_cost;
    }

    public function category() {
        return $this->belongsTo(AssetCategory::class, 'asset_category_id', 'asset_category_id');
    }
}
