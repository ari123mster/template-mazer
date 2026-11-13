<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    // Never rendered, including in rows recorded before the observer started stripping them.
    private const SECRET_KEYS = ['password', 'remember_token', 'api_token'];

    public function __construct()
    {
        $this->middleware('permission:acl_audit_index')->only(['index']);
        $this->middleware('permission:acl_audit_show')->only(['show']);
    }

    public function index(Request $request)
    {
        $filters = $request->validate([
            'event' => ['nullable', 'in:created,updated,deleted'],
            'model' => ['nullable', 'string', 'max:255'],
        ]);

        $logs = ActivityLog::with('user')
            ->when($filters['event'] ?? null, fn ($query, $event) => $query->where('event', $event))
            ->when($filters['model'] ?? null, fn ($query, $model) => $query->where('model', $model))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        $models = ActivityLog::query()->distinct()->orderBy('model')->pluck('model');

        return view('v.audit.index', compact('logs', 'models', 'filters'));
    }

    public function show(ActivityLog $activityLog)
    {
        $old = collect($activityLog->old_data ?? [])->except(self::SECRET_KEYS);
        $new = collect($activityLog->new_data ?? [])->except(self::SECRET_KEYS);

        $changes = $old->keys()->merge($new->keys())->unique()
            ->reject(fn ($key) => in_array($key, ['created_at', 'updated_at'], true))
            ->map(fn ($key) => [
                'field' => $key,
                'old' => $old->get($key),
                'new' => $new->get($key),
                'changed' => $activityLog->event === 'updated' && $old->get($key) !== $new->get($key),
            ])
            ->values();

        $headers = collect($activityLog->headers ?? [])->except('authorization');

        return view('v.audit.show', ['log' => $activityLog->load('user'), 'changes' => $changes, 'headers' => $headers]);
    }
}
