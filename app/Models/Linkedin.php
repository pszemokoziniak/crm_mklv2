<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Linkedin extends Model
{
    use HasFactory;
    use SoftDeletes;


    protected $casts = [
        'updated_at'  => 'datetime:Y-m-d H:i'
    ];
}
