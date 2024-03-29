<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class FutureProject extends Model
{
    use HasFactory;
    use SoftDeletes;


    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where($field ?? 'id', $value)->withTrashed()->firstOrFail();
    }
    public function faza(): BelongsTo
    {
        return $this->belongsTo(Faza::class);
    }
    public function kraj(): BelongsTo
    {
        return $this->belongsTo(Kraj::class);
    }
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class)->withTrashed();
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function objekt()
    {
        return $this->belongsTo(Objekt::class);
    }

    public function scopeOrderByCreatedAt($query)
    {
        $query->orderBy('created_at', 'DESC');
    }
    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where('nazwa', 'like', '%'.$search.'%')
//                ->orWhereHas('zakres', function ($query) use ($search) {
//                    $query->where('name', 'like', '%'.$search.'%');
//                })
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
