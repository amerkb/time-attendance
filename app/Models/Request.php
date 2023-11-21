<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'vacation_type',
        'payment_type',
        'type',
        'date',
        'status',
        'reason',
        'company_id',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'reject_reason',
        'attachments',
        'justify_type',
        'person',
        'dead_person',
        'degree_of_kinship'
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
