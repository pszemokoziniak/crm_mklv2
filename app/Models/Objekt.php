<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Objekt extends Model
{
    use HasFactory;
    use SoftDeletes;
    use UUID;

    public function objekt()
    {
        return $this->hasMany(FutureProject::class);
    }
}
