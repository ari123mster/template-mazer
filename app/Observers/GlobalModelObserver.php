<?php

namespace App\Observers;

use Illuminate\Database\Eloquent\Model;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class GlobalModelObserver
{
    /**
     * Handle the Model "created" event.
     */
    protected function log(Model $model, string $event)
    {
        if ($model instanceof \App\Models\ActivityLog) {
            return; // Cegah log dari log
        }

      $changes = collect($model->getChanges())->except(['updated_at', 'deleted_at','last_seen']); // atau tambahkan field lain yang tidak penting

if ($event === 'updated' && $changes->isEmpty()) {
    return;
}

        $request = request();

        ActivityLog::create([
            'model'     => get_class($model),
            'model_id'  => $model->getKey(),
            'event'     => $event,
            'old_data'  => $event === 'updated' ? $model->getOriginal() : null,
            'new_data'  => $model->getAttributes(),
            'user_id'   => Auth::id(),
            'ip'        => $request->ip(),
            'headers'   => json_encode($request->headers->all()),
        ]);
    }

    public function created(Model $model)
    {
        $this->log($model, 'created');
    }

    public function updated(Model $model)
    {
        $this->log($model, 'updated');
    }

    public function deleted(Model $model)
    {
        $this->log($model, 'deleted');
    }

    /**
     * Handle the Model "restored" event.
     */
    public function restored(Model $model): void
    {
        //
    }

    /**
     * Handle the Model "force deleted" event.
     */
    public function forceDeleted(Model $model): void
    {
        //
    }
}
