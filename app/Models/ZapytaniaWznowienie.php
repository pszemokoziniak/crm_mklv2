<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ZapytaniaWznowienie extends Model
{

    protected $table = 'zapytania_wznowienias';

    protected $fillable = [
        'text',
        'id_zapytania',
        'id_user',
        'time',
    ];

    public $timestamps = false; // Disable Laravel's default timestamps

    protected $casts = [
        'time' => 'datetime',
    ];

    public function zapytanie()
    {
        return $this->belongsTo(Zapytania::class, 'id_zapytania');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
