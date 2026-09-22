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
        'system',
    ];

    protected $casts = [
        'system' => 'boolean',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function notable()
    {
        return $this->morphTo();
    }

    /** Załączniki dodane razem z komentarzem (wykorzystywane przez zgłoszenia). */
    public function files()
    {
        return $this->hasMany(ZgloszenieFile::class, 'note_id')->orderBy('id');
    }
}
