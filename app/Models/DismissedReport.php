<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DismissedReport extends Model
{
    use HasFactory;
    protected $table = 'dismissed_reports';
    protected $fillable = ['user_id', 'start_date', 'end_date', 'dismissed_termination_period', 'elapsed_term_period'];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
