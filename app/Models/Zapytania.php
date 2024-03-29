<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Zapytania extends Model
{
    use HasFactory;
    use SoftDeletes;


    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'date:Y-m-d',
    ];
    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where($field ?? 'id', $value)->withTrashed()->firstOrFail();
    }
    public function zakres(): BelongsTo
    {
        return $this->belongsTo(Zakres::class);
    }
    public function kraj(): BelongsTo
    {
        return $this->belongsTo(Kraj::class);
    }
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function opracowuje(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_opracowuje_id', 'id');
    }

    public function waluta(): BelongsTo
    {
        return $this->belongsTo(Waluta::class);
    }

    public function oferta()
    {
        return $this->belongsTo(Oferta::class, 'id', 'zapytania_id')->withTrashed();
    }

    public function scopeOrderByCreatedAt($query)
    {
        $query->orderBy('created_at', 'DESC');
    }
    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where('nazwa_projektu', 'like', '%'.$search.'%')
                ->orWhereHas('zakres', function ($query) use ($search) {
                    $query->where('name', 'like', '%'.$search.'%');
                })
                ->orWhereHas('kraj', function ($query) use ($search) {
                    $query->where('name', 'like', '%'.$search.'%');
                })
                ->orWhereHas('client', function ($query) use ($search) {
                    $query->where('nazwa', 'like', '%'.$search.'%');
                })
                ->orWhereHas('user', function ($query) use ($search) {
                    $query->where('first_name', 'like', '%'.$search.'%')
                        ->orWhere('last_name', 'like', '%'.$search.'%');
                });
        })->when($filters['trashed'] ?? null, function ($query, $trashed) {
            if ($trashed === 'with') {
                $query->withTrashed();
            } elseif ($trashed === 'only') {
                $query->onlyTrashed();
            }
        });
    }
}
