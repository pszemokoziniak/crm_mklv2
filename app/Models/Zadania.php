<?php

namespace App\Models;

use App\Notifications\ReminderRuleNotification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class Zadania extends Model
{
    use SoftDeletes;

    const STATUS_AKTYWNE = 'aktywne';
    const STATUS_DO_AKCEPTACJI = 'do_akceptacji';
    const STATUS_ZAMKNIETE = 'zamkniete';

    protected $fillable = [
        'responsible_person_id',
        'subject',
        'description',
        'deadline',
        'user_id',
        'client_id',
        'status',
        'closed_by',
        'closed_at',
    ];

    protected $casts = [
        'deadline' => 'date:Y-m-d',
        'closed_at' => 'datetime',
    ];

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d');
    }

    protected static function booted()
    {
        static::created(function ($zadania) {
            $zadania->assignments()->create([
                'user_id' => $zadania->responsible_person_id,
                'assigned_by' => Auth::id() ?? $zadania->user_id,
                'assigned_at' => now(),
                'note' => 'Initial assignment',
            ]);

            try {
                $rules = ReminderRule::where('active', true)
                    ->where('event', ReminderRule::EVENT_ZADANIA_UTWORZONE)
                    ->get();

                foreach ($rules as $rule) {
                    $recipients = $rule->resolveRecipients($zadania);
                    if ($recipients->isEmpty()) {
                        continue;
                    }
                    Notification::send($recipients, new ReminderRuleNotification($rule, $zadania, 0));
                }
            } catch (\Throwable $e) {
                Log::warning('Reminder dispatch on Zadania create failed: '.$e->getMessage(), [
                    'zadania_id' => $zadania->id,
                ]);
            }
        });

        static::updating(function ($zadania) {
            if ($zadania->isDirty('responsible_person_id')) {
                $zadania->assignments()->create([
                    'user_id' => $zadania->responsible_person_id,
                    'assigned_by' => Auth::id(),
                    'assigned_at' => now(),
                    'note' => 'Responsible person changed',
                ]);
            }
        });
    }

    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where($field ?? 'id', $value)->withTrashed()->firstOrFail();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function closedByUser()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function responsiblePerson()
    {
        return $this->belongsTo(User::class, 'responsible_person_id', 'id');
    }

    public function assignments()
    {
        return $this->hasMany(ZadaniaAssignment::class, 'zadania_id')->orderBy('assigned_at', 'desc');
    }

    public function stages()
    {
        return $this->hasMany(ZadaniaStage::class, 'zadania_id')->orderBy('order');
    }

    public function milestones()
    {
        return $this->hasMany(ZadaniaMilestone::class, 'zadania_id')->orderBy('deadline');
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $keywords = array_filter(array_map('trim', explode('+', $search)), fn ($k) => $k !== '');
            foreach ($keywords as $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('subject', 'like', '%'.$keyword.'%')
                        ->orWhere('description', 'like', '%'.$keyword.'%')
                        ->orWhereHas('responsiblePerson', function ($query) use ($keyword) {
                            $query->where('first_name', 'like', '%'.$keyword.'%')
                                ->orWhere('last_name', 'like', '%'.$keyword.'%');
                        })
                        ->orWhereHas('user', function ($query) use ($keyword) {
                            $query->where('first_name', 'like', '%'.$keyword.'%')
                                ->orWhere('last_name', 'like', '%'.$keyword.'%');
                        });
                });
            }
        })->when($filters['status'] ?? null, function ($query, $status) {
            $query->where('status', $status);
        })->when($filters['trashed'] ?? null, function ($query, $trashed) {
            if ($trashed === 'with') {
                $query->withTrashed();
            } elseif ($trashed === 'only') {
                $query->onlyTrashed();
            }
        });
    }
}
