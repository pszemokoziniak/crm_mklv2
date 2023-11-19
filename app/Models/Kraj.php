<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kraj extends Model
{
    use HasFactory;
    use SoftDeletes;
    use UUID;

    public function clients()
    {
        return $this->hasMany(Client::class);
    }
    public function zapytania()
    {
        return $this->hasMany(Zapytania::class);
    }
}
