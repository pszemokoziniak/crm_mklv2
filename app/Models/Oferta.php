<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Oferta extends Model
{
    use SoftDeletes, LogsActivity;

    // Statusy oferty (porownywane lowercase) ktore powoduja
    // automatyczna archiwizacje powiazanego zapytania.
    protected const TERMINAL_LOST_STATUSES = ['przegrana', 'rezygnacja'];

    protected $guarded = [];

    public static function isLostStatusName(?string $name): bool
    {
        if (!$name) {
            return false;
        }
        return in_array(strtolower(trim($name)), self::TERMINAL_LOST_STATUSES, true);
    }

    protected $casts = [
        'created_at' => 'date:Y-m-d',
        'data_kontakt' => 'date',
        'data_wyslania' => 'date',
    ];

    protected static function booted()
    {
        static::created(function ($oferta) {
            self::maybeArchiveParentZapytanie($oferta);
        });
        static::updated(function ($oferta) {
            if ($oferta->wasChanged('oferta_status_id')) {
                self::maybeArchiveParentZapytanie($oferta);
            }
        });
    }

    protected static function maybeArchiveParentZapytanie(self $oferta): void
    {
        try {
            if (!$oferta->oferta_status_id) {
                return;
            }
            $status = OfertaStatus::find($oferta->oferta_status_id);
            if (!$status) {
                return;
            }
            $name = strtolower(trim($status->name));
            if (!in_array($name, self::TERMINAL_LOST_STATUSES, true)) {
                return;
            }
            $zapytanie = $oferta->zapytania()->first();
            if ($zapytanie && !$zapytanie->trashed()) {
                $zapytanie->delete();
                Log::info('Auto-archiwizacja zapytania (status oferty)', [
                    'oferta_id' => $oferta->id,
                    'zapytania_id' => $zapytanie->id,
                    'status' => $name,
                ]);
            }
            // Sama oferta tez trafia do archiwum (status terminalny = nic juz z nia nie robimy)
            if (!$oferta->trashed()) {
                $oferta->delete();
                Log::info('Auto-archiwizacja oferty (status terminalny)', [
                    'oferta_id' => $oferta->id,
                    'status' => $name,
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('Blad auto-archiwizacji oferty/zapytania: ' . $e->getMessage(), [
                'oferta_id' => $oferta->id,
            ]);
        }
    }

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
    public function utrataDetail()
    {
        return $this->hasOne(OfertaUtrataDetail::class);
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
                $idKeyword = ltrim($keyword, '#');
                $query->where(function ($query) use ($keyword, $idKeyword) {
                    $query->where('typ', 'like', '%'.$keyword.'%')
                        ->when(ctype_digit($idKeyword), function ($query) use ($idKeyword) {
                            $query->orWhere('ofertas.id', (int) $idKeyword);
                        })
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
        })->when($filters['status'] ?? null, function ($query, $status) {
            $query->whereHas('status', function ($q) use ($status) {
                $q->whereRaw('LOWER(TRIM(name)) = ?', [strtolower(trim($status))]);
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
