<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'salary', 'basic_salary', 'rewards', 'adversaries', 'date', 'company_id', 'housing_allowance', 'transportation_allowance', 'rewards_type', 'adversaries_type'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}
