<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Client extends Model
{
    use SoftDeletes, LogsActivity;

    protected $guarded = [];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d h:i:s'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where($field ?? 'id', $value)->withTrashed()->firstOrFail();
    }

    public function branza()
    {
        return $this->belongsTo(Branza::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class, );
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function kraj()
    {
        return $this->belongsTo(Kraj::class);
    }
    public function zapytania(): HasMany
    {
        return $this->hasMany(Zapytania::class);
    }
    public function oferty(): HasMany
    {
        return $this->hasMany(Oferta::class);
    }
    public function kontakty(): HasMany
    {
        return $this->hasMany(Kontakt::class);
    }
    public function zadania(): HasMany
    {
        return $this->hasMany(Zadania::class);
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
                    $query->where('nazwa', 'like', '%'.$keyword.'%')
                        ->orWhereHas('branza', function ($query) use ($keyword) {
                            $query->where('name', 'like', '%'.$keyword.'%');
                        })
                        ->orWhereHas('kraj', function ($query) use ($keyword) {
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
        })->when($filters['status'] ?? null, function ($query, $status) {
            if ($status === 'aktywni') {
                $sixMonthsAgo = Carbon::now()->subMonths(6);
                $query->where(function ($query) use ($sixMonthsAgo) {
                    $query->whereHas('zapytania', function ($query) use ($sixMonthsAgo) {
                        $query->where('created_at', '>=', $sixMonthsAgo);
                    })->orWhereHas('kontakty', function ($query) use ($sixMonthsAgo) {
                        $query->where('created_at', '>=', $sixMonthsAgo);
                    })->orWhereHas('oferty', function ($query) use ($sixMonthsAgo) {
                        $query->where('created_at', '>=', $sixMonthsAgo);
                    });
                });
            } elseif ($status === 'nieaktywni') {
                $sixMonthsAgo = Carbon::now()->subMonths(6);
                $query->whereDoesntHave('zapytania', function ($query) use ($sixMonthsAgo) {
                    $query->where('created_at', '>=', $sixMonthsAgo);
                })->whereDoesntHave('kontakty', function ($query) use ($sixMonthsAgo) {
                    $query->where('created_at', '>=', $sixMonthsAgo);
                })->whereDoesntHave('oferty', function ($query) use ($sixMonthsAgo) {
                    $query->where('created_at', '>=', $sixMonthsAgo);
                });
            } elseif ($status === 'zapytania') {
                $query->whereHas('zapytania', function ($query) {
                    $query->whereYear('created_at', Carbon::now()->year);
                });
            }
        });
    }
}
