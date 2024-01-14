<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KontaktPerson extends Model
{
    use HasFactory;
    use UUID;

    protected  $table = 'kontakt_persons';
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
