<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ZadaniaAssignment extends Model
{
    protected $fillable = [
        'zadania_id',
        'user_id',
        'assigned_by',
        'note',
        'assigned_at'
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
    ];

    public function zadania()
    {
        return $this->belongsTo(Zadania::class);
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assigner()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
