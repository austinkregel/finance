<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @mixin Model
 */
trait LogsActivity
{
    use \Spatie\Activitylog\Traits\LogsActivity;

    public function log($message): void
    {
        $activity = activity(Str::snake(class_basename(static::class)));

        if (auth()->check()) {
            $activity->causedBy(auth()->user());
        } else {
            $activity->causedByAnonymous();
        }

        $activity->on($this)
            ->log($message);
    }
}
