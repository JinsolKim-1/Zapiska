<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpecialRequest extends Model
{
    use HasFactory;

    protected $table = 'special_request';
    protected $primaryKey = 'special_id';
    public $timestamps = true;

    protected $fillable = [
        'company_id',
        'sector_id',
        'special_asset',
        'justification',
        'admin_approve'
    ];
}
