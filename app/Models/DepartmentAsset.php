<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepartmentAsset extends Model
{
    use HasFactory;

    protected $table = 'department_asset';
    protected $primaryKey = 'dept_asset_id';
    public $timestamps = false;

    protected $fillable = [
        'company_id',
        'sector_id',
        'asset_id',
        'inventory_id',
        'assigned_quantity',
        'assigned_by',
        'assigned_at',
        'status',
        'notes',
    ];

    // Relationship to Asset
    public function asset()
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    // Relationship to User who assigned it
    public function assignedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'assigned_by');
    }
}
