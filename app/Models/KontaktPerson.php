<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KontaktPerson extends Model
{
    use HasFactory;


    protected  $table = 'kontakt_persons';
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
