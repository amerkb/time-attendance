<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OverTimeAttendance extends Model
{
    use HasFactory;
    protected $table = 'over_time_attendances';
    protected $fillable = ['user_id', 'date', 'status', 'login_time', 'logout_time', 'company_id', 'custom_updated_at'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
