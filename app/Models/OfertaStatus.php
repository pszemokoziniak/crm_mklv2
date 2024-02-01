<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OfertaStatus extends Model
{
    use HasFactory;
    use SoftDeletes;


    public function oferta()
    {
        return $this->hasMany(Oferta::class);
    }
}
