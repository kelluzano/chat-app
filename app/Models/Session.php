<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Session extends Model
{
    use HasFactory, SoftDeletes;

    protected $appends = ['created_at_formatted'];

    protected $fillable = ['uniqueId', 'channel_name', 'assigned_to', 'assigned_date', 'dispostion', 'disposition_date', 'close_date'];


    public function client(){
        return $this->hasOne(ClientDetail::class, 'uniqueId', 'uniqueId');
    }

    public function messages(){
        return $this->hasMany(Message::class, 'session_id');
    }

    public function getCreatedAtFormattedAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    public function lastMessageReceived(){
        return $this->hasOne(Message::class, 'session_id')->latest();
    }
}
