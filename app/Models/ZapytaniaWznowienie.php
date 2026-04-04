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
        'data_otrzymania',
        'data_zlozenia',
        'preliminarz',
        'zakres_id',
        'user_opracowuje_id',
        'start',
        'end',
        'kwota',
        'waluta_id',
    ];

    public $timestamps = false; // Disable Laravel's default timestamps

    protected $casts = [
        'time' => 'datetime',
        'data_otrzymania' => 'date',
        'data_zlozenia' => 'date',
        'start' => 'date',
        'end' => 'date',
    ];

    public function zapytanie()
    {
        return $this->belongsTo(Zapytania::class, 'id_zapytania');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function zakres()
    {
        return $this->belongsTo(Zakres::class, 'zakres_id');
    }

    public function waluta()
    {
        return $this->belongsTo(Waluta::class, 'waluta_id');
    }

    public function opracowuje()
    {
        return $this->belongsTo(User::class, 'user_opracowuje_id');
    }
}
