<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SectorBudget extends Model
{
    use HasFactory;

    protected $table = 'sector_budgets';
    protected $primaryKey = 'budget_id';
    protected $fillable = [
        'company_id',
        'sector_id',
        'total_budget',
        'used_budget',
        'start_date',
        'end_date'
    ];

    // Relationships
    public function sector() {
        return $this->belongsTo(Sector::class, 'sector_id', 'sector_id');
    }

    public function company() {
        return $this->belongsTo(Company::class, 'company_id', 'company_id');
    }
}

// app/Models/Sector.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sector extends Model
{
    use HasFactory;

    protected $table = 'sectors';
    protected $primaryKey = 'sector_id';
    protected $fillable = ['sector_name', 'company_id'];

    public function budget() {
        return $this->hasOne(SectorBudget::class, 'sector_id', 'sector_id');
    }
}
