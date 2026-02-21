<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kontakt extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'parent_id',
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
        'oferta_id',
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

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Kontakt::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Kontakt::class, 'parent_id')->orderBy('created_at', 'asc');
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function($q) use ($search) {
                $q->where('subject', 'like', '%'.$search.'%')
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
        });
    }
}
