<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kraj extends Model
{
    use HasFactory;
    use SoftDeletes;


    public function clients()
    {
        return $this->hasMany(Client::class);
    }
    public function zapytania()
    {
        return $this->hasMany(Zapytania::class);
    }
}
