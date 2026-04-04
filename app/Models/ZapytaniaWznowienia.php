<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ZapytaniaWznowienia extends Model
{

    protected $table = 'zapytania_wznowienias';

    public $timestamps = false;

    protected $fillable = [
        'text',
        'id_zapytania',
        'id_user',
        'time',
    ];

    protected $casts = [
        'time' => 'datetime',
    ];

    public function zapytania()
    {
        return $this->belongsTo(Zapytania::class, 'id_zapytania');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
