<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Oferta extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'created_at' => 'date:Y-m-d',
    ];
    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where($field ?? 'id', $value)->withTrashed()->firstOrFail();
    }
    public function ofertastatus(): BelongsTo
    {
        return $this->belongsTo(OfertaStatus::class, 'oferta_status_id', 'id');
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
            $keywords = array_filter(explode('+', $search), 'trim');

            foreach ($keywords as $keyword) {
                $keyword = trim($keyword);
                $query->where(function ($query) use ($keyword) {
                    $query->where('typ', 'like', '%'.$keyword.'%')
                        ->orWhereHas('client', function ($query) use ($keyword) {
                            $query->where('nazwa', 'like', '%'.$keyword.'%');
                        })
                        ->orWhereHas('zapytania', function ($query) use ($keyword) {
                            $query->where('nazwa_projektu', 'like', '%'.$keyword.'%');
                        })
                        ->orWhereHas('status', function ($query) use ($keyword) {
                            $query->where('name', 'like', '%'.$keyword.'%');
                        })
                        ->orWhereHas('user', function ($query) use ($keyword) {
                            $query->where('first_name', 'like', '%'.$keyword.'%')
                                ->orWhere('last_name', 'like', '%'.$keyword.'%');
                        });
                });
            }
        })->when($filters['trashed'] ?? null, function ($query, $trashed) {
            if ($trashed === 'with') {
                $query->withTrashed();
            } elseif ($trashed === 'only') {
                $query->onlyTrashed();
            }
        });
    }
}
