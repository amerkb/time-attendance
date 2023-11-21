<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use ParagonIE\Sodium\Compat;

class Alert extends Model
{
    use HasFactory;
    protected $fillable = ['email', 'content', 'type', 'company_id', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
