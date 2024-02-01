<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Objekt extends Model
{
    use HasFactory;
    use SoftDeletes;


    public function objekt()
    {
        return $this->hasMany(FutureProject::class);
    }
}
