<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Zapytania extends Model
{
    use SoftDeletes, LogsActivity;

    const PENDING_DAYS = 15;
    const OLD_DAYS = 30;

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'date:Y-m-d',
        'data_zlozenia' => 'date', // Added this line
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
    public function otrzymal(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_otrzymal_id', 'id');
    }

    public function waluta(): BelongsTo
    {
        return $this->belongsTo(Waluta::class);
    }

    public function oferty(): HasMany
    {
        return $this->hasMany(Oferta::class, 'zapytania_id')->withTrashed();
    }

    public function wznowienia(): HasMany
    {
        return $this->hasMany(ZapytaniaWznowienie::class, 'id_zapytania');
    }

    /**
     * Alias for Laravel's implicit route model binding (scopeBindings)
     * which incorrectly pluralizes Polish words.
     */
    public function wznowienies(): HasMany
    {
        return $this->wznowienia();
    }

    public function scopeOrderByCreatedAt($query)
    {
        $query->orderBy('created_at', 'DESC');
    }

    public function scopePendingOrOld($query, $pendingDays = self::PENDING_DAYS, $oldDays = self::OLD_DAYS)
    {
        return $query->whereNull('deleted_at')
            ->whereNotNull('data_zlozenia')
            ->where('data_zlozenia', '<=', now()->addDays($pendingDays))
            ->where('data_zlozenia', '>=', now()->subDays($oldDays));
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            // Only apply search conditions if $search is not an empty string
            if (!empty($search)) {
                $query->where(function ($query) use ($search) {
                    $query->where('nazwa_projektu', 'like', '%'.$search.'%')
                        ->orWhere('id_zapyt', 'like', '%'.$search.'%')
                        ->orWhereHas('client', function ($query) use ($search) {
                            $query->where('nazwa', 'like', '%'.$search.'%');
                        })
                        ->orWhereHas('user', function ($query) use ($search) {
                            $query->where('first_name', 'like', '%'.$search.'%')
                                ->orWhere('last_name', 'like', '%'.$search.'%');
                        })
                        ->orWhereHas('kraj', function ($query) use ($search) {
                            $query->where('name', 'like', '%'.$search.'%');
                        })
                        ->orWhereHas('zakres', function ($query) use ($search) {
                            $query->where('name', 'like', '%'.$search.'%');
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
