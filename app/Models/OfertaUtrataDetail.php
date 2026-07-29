<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfertaUtrataDetail extends Model
{
    protected $guarded = [];

    protected $casts = [
        'szansa_na_renegocjacje' => 'boolean',
    ];

    public function oferta()
    {
        return $this->belongsTo(Oferta::class);
    }

    public function powod()
    {
        return $this->belongsTo(PowodUtraty::class, 'powod_utraty_id');
    }

    public function powodDodatkowy()
    {
        return $this->belongsTo(PowodUtraty::class, 'powod_utraty_dodatkowy_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
