<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;
    protected $fillable = ['company_id', 'mac_address'];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}
