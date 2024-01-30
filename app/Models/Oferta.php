<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Oferta extends Model
{
    use HasFactory;
    use SoftDeletes;
    use UUID;

    protected $casts = [
        'created_at' => 'date:Y-m-d',
    ];
    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where($field ?? 'id', $value)->withTrashed()->firstOrFail();
    }
    public function ofertastatus(): BelongsTo
    {
        return $this->belongsTo(OfertaStatus::class);
    }
    public function zapytania()
    {
        return $this->belongsTo(Zapytania::class)->withTrashed();
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function kraj()
    {
        return $this->belongsTo(Kraj::class);
    }
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
    public function waluta()
    {
        return $this->belongsTo(Waluta::class);
    }
    public function status()
    {
        return $this->belongsTo(OfertaStatus::class, 'oferta_status_id', 'id');
    }
    public function scopeOrderByCreatedAt($query)
    {
        $query->orderBy('created_at', 'DESC');
    }
    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where('typ', 'like', '%'.$search.'%')
                ->orWhereHas('client', function ($query) use ($search) {
                    $query->where('nazwa', 'like', '%'.$search.'%');
                })
                ->orWhereHas('zapytania', function ($query) use ($search) {
                    $query->where('nazwa_projektu', 'like', '%'.$search.'%');
                })
                ->orWhereHas('status', function ($query) use ($search) {
                    $query->where('name', 'like', '%'.$search.'%');
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
