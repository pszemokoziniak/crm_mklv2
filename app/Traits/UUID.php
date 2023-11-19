<?php
declare(strict_types=1);

namespace App\Traits;

use Illuminate\Support\Str;

trait UUID
{
    /**
     * Boot function from Laravel.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(static function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = Str::uuid()->toString();
            }
        });
    }

    /**
     * Get the value indicating whether the IDs are incrementing.
     *
     * @return bool
     */
    public function getIncrementing(): bool
    {
        return false;
    }

    /**
     * Get the auto-incrementing key type.
     *
     * @return string
     */
    public function getKeyType(): string
    {
        return 'string';
    }
}
