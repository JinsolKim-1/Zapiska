<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sector extends Model
{
    use HasFactory;

    protected $table = 'sectors'; // optional if you follow Laravel naming convention
    protected $primaryKey = 'sector_id';

    protected $fillable = [
        'company_id',
        'manager_id',
        'department_name',
    ];

    // Relation to users in this sector
    public function users() {
        return $this->hasMany(User::class, 'sector_id');
    }

    // Relation to manager (a user)
    public function manager() {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    
}
