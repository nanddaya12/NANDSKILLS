<?php

namespace App\Observers;

use App\Services\ActivityLogger;

/**
 * AuditObserver — attach to any Eloquent model to auto-log CRUD events.
 *
 * Usage in AppServiceProvider::boot():
 *   User::observe(AuditObserver::class);
 *   Admission::observe(AuditObserver::class);
 */
class AuditObserver
{
    public function created($model): void
    {
        ActivityLogger::log(
            'CREATE',
            class_basename($model) . ' created (ID: ' . $model->id . ')',
            $model,
            null,
            $model->getAttributes()
        );
    }

    public function updated($model): void
    {
        $dirty = $model->getDirty();
        if (empty($dirty)) return;

        $before = array_intersect_key($model->getOriginal(), $dirty);

        ActivityLogger::log(
            'UPDATE',
            class_basename($model) . ' updated (ID: ' . $model->id . ')',
            $model,
            $before,
            $dirty
        );
    }

    public function deleted($model): void
    {
        ActivityLogger::log(
            'DELETE',
            class_basename($model) . ' deleted (ID: ' . $model->id . ')',
            $model,
            $model->getAttributes()
        );
    }
}
