<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kontakt extends Model
{
    protected $fillable = [
        'id',
        'parent_id',
        'client_id',
        'call_date',
        'call_time',
        'next_call_date',
        'next_call_time',
        'subject',
        'contact_type',
        'description',
        'user_id',
        'opiekun_id',
        'kontakt_person_id',
        'zapytania_id',
        'oferta_id',
        'future_project_id',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'call_date' => 'date',
        'next_call_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function opiekun(): BelongsTo
    {
        return $this->belongsTo(User::class, 'opiekun_id');
    }

    public function zapytania(): BelongsTo
    {
        return $this->belongsTo(Zapytania::class);
    }
    public function oferta(): BelongsTo
    {
        return $this->belongsTo(Oferta::class);
    }
    public function futureProject(): BelongsTo
    {
        return $this->belongsTo(FutureProject::class);
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
            $keywords = array_filter(array_map('trim', explode('+', $search)), fn ($k) => $k !== '');
            foreach ($keywords as $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('subject', 'like', '%'.$keyword.'%')
                        ->orWhere('description', 'like', '%'.$keyword.'%')
                        ->orWhereHas('kontaktperson', function ($query) use ($keyword) {
                            $query->where('first_name', 'like', '%'.$keyword.'%')
                                ->orWhere('last_name', 'like', '%'.$keyword.'%');
                        })
                        ->orWhereHas('client', function ($query) use ($keyword) {
                            $query->where('nazwa', 'like', '%'.$keyword.'%');
                        })
                        ->orWhereHas('user', function ($query) use ($keyword) {
                            $query->where('first_name', 'like', '%'.$keyword.'%')
                                ->orWhere('last_name', 'like', '%'.$keyword.'%');
                        })
                        ->orWhereHas('opiekun', function ($query) use ($keyword) {
                            $query->where('first_name', 'like', '%'.$keyword.'%')
                                ->orWhere('last_name', 'like', '%'.$keyword.'%');
                        });
                });
            }
        });
    }
}
