<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;

    protected $primaryKey = 'vendor_id';
    protected $fillable = [
        'company_id', 'vendor_name', 'contact_person', 
        'email', 'phone', 'address', 'api_source', 'api_vendor_id'
    ];

    // Vendor belongs to a company
    public function company() {
        return $this->belongsTo(Company::class, 'company_id');
    }
}
