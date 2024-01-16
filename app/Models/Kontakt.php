<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kontakt extends Model
{
    use HasFactory;
    use UUID;
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function zapytania(): BelongsTo
    {
        return $this->belongsTo(Zapytania::class);
    }
    public function oferta(): BelongsTo
    {
        return $this->belongsTo(Oferta::class);
    }
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
    public function kontaktperson(): BelongsTo
    {
        return $this->belongsTo(KontaktPerson::class, 'kontakt_person_id', 'id');
    }
}
