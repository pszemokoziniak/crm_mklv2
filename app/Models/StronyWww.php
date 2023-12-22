<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StronyWww extends Model
{
    use HasFactory;
    use SoftDeletes;
    use UUID;

    protected $casts = [
        'updated_at'  => 'datetime:Y-m-d H:i'
    ];
}
