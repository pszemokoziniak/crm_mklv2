<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Załącznik zgłoszenia — print screen albo plik dodany w komentarzu.
 */
class ZgloszenieFile extends Model
{
    protected $fillable = [
        'zgloszenie_id',
        'note_id',
        'path',
        'original_name',
        'mime',
        'size',
        'uploaded_by',
    ];

    protected $casts = [
        'zgloszenie_id' => 'integer',
        'note_id' => 'integer',
        'size' => 'integer',
    ];

    protected $appends = ['is_image'];

    public function zgloszenie(): BelongsTo
    {
        return $this->belongsTo(Zgloszenie::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getIsImageAttribute(): bool
    {
        return str_starts_with((string) $this->mime, 'image/');
    }
}
