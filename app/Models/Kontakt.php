<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kontakt extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'client_id',
        'call_date',
        'call_time',
        'next_call_date',
        'next_call_time',
        'subject',
        'description',
        'user_id',
        'kontakt_person_id',
        'zapytania_id',
        'created_at',
        'updated_at',
    ];

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
    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where('subject', 'like', '%'.$search.'%')
                ->orWhere('description', 'like', '%'.$search.'%')
                ->orWhereHas('kontaktperson', function ($query) use ($search) {
                    $query->where('first_name', 'like', '%'.$search.'%')
                        ->orWhere('last_name', 'like', '%'.$search.'%');
                })
                ->orWhereHas('client', function ($query) use ($search) {
                    $query->where('nazwa', 'like', '%'.$search.'%');
                })
                ->orWhereHas('user', function ($query) use ($search) {
                    $query->where('first_name', 'like', '%'.$search.'%')
                        ->orWhere('last_name', 'like', '%'.$search.'%');
                });
        });
    }
}
