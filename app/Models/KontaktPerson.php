<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KontaktPerson extends Model
{
    protected $table = 'kontakt_persons';

    protected $fillable = [
        'first_name',
        'last_name',
        'position',
        'phone1',
        'phone2',
        'email',
        'miasto',
        'client_id',
        'description',
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
