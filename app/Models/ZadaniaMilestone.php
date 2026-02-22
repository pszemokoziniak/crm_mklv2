<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ZadaniaMilestone extends Model
{
    protected $fillable = [
        'zadania_id',
        'name',
        'deadline',
        'completed_at'
    ];

    protected $casts = [
        'deadline' => 'date:Y-m-d',
        'completed_at' => 'datetime',
    ];

    public function zadania()
    {
        return $this->belongsTo(Zadania::class);
    }

    public function stages()
    {
        return $this->hasMany(ZadaniaStage::class, 'milestone_id')->orderBy('order');
    }
}
