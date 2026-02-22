<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Zadania extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'responsible_person_id',
        'subject',
        'description',
        'deadline',
        'user_id',
    ];

    protected static function booted()
    {
        static::created(function ($zadania) {
            $zadania->assignments()->create([
                'user_id' => $zadania->responsible_person_id,
                'assigned_by' => Auth::id() ?? $zadania->user_id,
                'assigned_at' => now(),
                'note' => 'Initial assignment',
            ]);
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
            $query->where('subject', 'like', '%'.$search.'%')
                ->orWhere('description', 'like', '%'.$search.'%')
                ->orWhereHas('responsiblePerson', function ($query) use ($search) {
                    $query->where('first_name', 'like', '%'.$search.'%')
                        ->orWhere('last_name', 'like', '%'.$search.'%');
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
