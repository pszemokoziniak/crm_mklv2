<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ZadaniaStage extends Model
{
    protected $fillable = [
        'zadania_id',
        'milestone_id',
        'name',
        'order',
        'status',
        'completed_at'
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function zadania()
    {
        return $this->belongsTo(Zadania::class);
    }

    public function milestone()
    {
        return $this->belongsTo(ZadaniaMilestone::class);
    }
}
