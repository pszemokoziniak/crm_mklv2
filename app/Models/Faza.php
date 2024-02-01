<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Faza extends Model
{
    use HasFactory;
    use SoftDeletes;


    public function faza()
    {
        return $this->hasMany(FutureProject::class);
    }
}
