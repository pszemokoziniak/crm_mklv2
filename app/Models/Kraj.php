<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kraj extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['name', 'waluta_id'];

    public function waluta(): BelongsTo
    {
        return $this->belongsTo(Waluta::class);
    }

    public function clients()
    {
        return $this->hasMany(Client::class);
    }

    public function zapytania()
    {
        return $this->hasMany(Zapytania::class);
    }
}
