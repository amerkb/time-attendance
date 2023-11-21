<?php

namespace App\Models;

use App\Statuses\EmployeeStatus;
use App\Statuses\UserTypes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = ['name', 'email', 'commercial_record', 'start_commercial_record', 'end_commercial_record', 'percentage', 'check_type'];


    public function employees()
    {
        return $this->hasMany(User::class)
            ->whereIn('type', [UserTypes::HR, UserTypes::EMPLOYEE])
            ->whereNotIn('status', [EmployeeStatus::TEMPORARY_DISMISSED, EmployeeStatus::PERMANENT_DISMISSED]);
    }
    public function dismissedEmployees()
    {
        return $this->hasMany(User::class)
            ->whereIn('type', [UserTypes::HR, UserTypes::EMPLOYEE])
            ->whereIn('status', [EmployeeStatus::TEMPORARY_DISMISSED, EmployeeStatus::PERMANENT_DISMISSED]);
    }

    public function admin()
    {
        return $this->hasOne(User::class)->where('type', UserTypes::ADMIN);
    }

    public function holidays()
    {
        return $this->hasMany(Holiday::class);
    }




    public function deposits()
    {
        return $this->hasMany(Deposit::class);
    }

    public function percentage()
    {
        return $this->hasMany(Percentage::class);
    }


    public function JustifyRequests()
    {
        return $this->hasMany(JustifyRequest::class);
    }
    public function VacationRequests()
    {
        return $this->hasMany(VacationRequest::class);
    }

    public function EmployeeAvailableTime()
    {
        return $this->belongsTo(EmployeeAvailableTime::class);
    }

    public function locations()
    {
        return $this->hasOne(Location::class);
    }

    public function addresess()
    {
        return $this->hasMany(Address::class);
    }

    public function alerts()
    {
        return $this->hasMany(Alert::class);
    }
}
