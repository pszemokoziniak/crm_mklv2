<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class FutureProject extends Model
{
    use SoftDeletes, LogsActivity;

    protected $guarded = [];

    protected $casts = [
        'data_kontakt' => 'date',
        'data_start' => 'date',
        'data_end' => 'date',
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
    public function opiekun(): BelongsTo
    {
        return $this->belongsTo(User::class, 'opiekun_id');
    }

    public function objekt()
    {
        return $this->belongsTo(Objekt::class);
    }

    public function kontakty(): HasMany
    {
        return $this->hasMany(Kontakt::class, 'future_project_id');
    }

    public function scopeOrderByCreatedAt($query)
    {
        $query->orderBy('created_at', 'DESC');
    }
    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $keywords = array_filter(array_map('trim', explode('+', $search)), fn ($k) => $k !== '');
            foreach ($keywords as $keyword) {
                $query->where(function ($query) use ($keyword) {
                    $query->where('nazwa', 'like', '%'.$keyword.'%')
                        ->orWhere('miasto', 'like', '%'.$keyword.'%')
                        ->orWhereHas('kraj', function ($query) use ($keyword) {
                            $query->where('name', 'like', '%'.$keyword.'%');
                        })
                        ->orWhereHas('client', function ($query) use ($keyword) {
                            $query->where('nazwa', 'like', '%'.$keyword.'%');
                        })
                        ->orWhereHas('objekt', function ($query) use ($keyword) {
                            $query->where('name', 'like', '%'.$keyword.'%');
                        })
                        ->orWhereHas('faza', function ($query) use ($keyword) {
                            $query->where('name', 'like', '%'.$keyword.'%');
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
