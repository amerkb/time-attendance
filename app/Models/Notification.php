<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    protected $fillable = ['id', 'post_id', 'notifier_id', 'post_id', 'read_at', 'message', 'user_id', 'type'];

    public function user()
    {
        return $this->belongsTo(User::class, 'notifier_id', 'id');
    }
}
