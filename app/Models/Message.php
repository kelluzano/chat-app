<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use HasFactory, SoftDeletes;

    protected $appends = ['created_at_formatted', 'last_message_formatted'];

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

    public function getLastMessageFormattedAttribute(){
        return $this->created_at->diffForHumans();
    }

    public function session(){
        return $this->belongsTo(Session::class, 'session_id');
    }
}
