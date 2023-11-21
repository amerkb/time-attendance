<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id', 'start_contract_date', 'end_contract_date', 'company_id',
        'contract_termination_date', 'contract_termination_period', 'contract_termination_reason', 'previous_terminate_period'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}
