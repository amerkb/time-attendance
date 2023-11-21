<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    use HasFactory;
    protected $fillable = [
        'type', 'status', 'extra_status', 'user_id', 'company_id', 'car_number', 'car_model', 'manufacturing_year',
        'Mechanic_card_number', 'car_image', 'laptop_type', 'serial_laptop_number', 'laptop_color',
        'laptop_image', 'serial_mobile_number', 'mobile_color', 'mobile_image', 'mobile_type', 'mobile_sim', 'reason_reject', 'reason_clearance_reject',
        'deposit_request_date', 'clearance_request_date'
    ];


    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
