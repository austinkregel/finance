<?php

declare(strict_types=1);

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;
use Spatie\Activitylog\ActivitylogServiceProvider;

/**
 * @mixin Model
 */
trait LogsActivity
{
    public function log($message): void
    {
        $activity = activity(Str::snake(class_basename(static::class)));

        if (auth()->check()) {
            $activity->causedBy(auth()->user());
        } else {
            $activity->causedByAnonymous();
        }

        $activity->on($this)
            ->inLog('activity')
            ->log($message);
    }

    public function activities(): MorphMany
    {
        return $this->morphMany(ActivitylogServiceProvider::determineActivityModel(), 'subject');
    }
}
