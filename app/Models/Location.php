<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;
    protected $fillable = ['company_id', 'longitude', 'latitude', 'radius'];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}
