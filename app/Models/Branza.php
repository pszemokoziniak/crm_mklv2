<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\UUID;

class Branza extends Model
{
    use HasFactory;
    use SoftDeletes;
    use UUID;

    public function clients()
    {
        return $this->hasMany(Client::class);
    }
}
