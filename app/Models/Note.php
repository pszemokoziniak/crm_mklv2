<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    protected $fillable = [
        'user_id',
        'notable_type',
        'notable_id',
        'body',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function notable()
    {
        return $this->morphTo();
    }
}
