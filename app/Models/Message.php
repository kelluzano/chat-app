<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $appends = ['created_at_formatted'];

    protected $fillable = [
        'session_id', 'content', 'direction', 'user_id', 'seen', 'status'
    ];

    public function user(){
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function getCreatedAtFormattedAttribute()
    {
        return $this->created_at->format('d M g:i A');
    }
}
