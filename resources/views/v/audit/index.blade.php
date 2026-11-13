@extends('layouts.dashboard')

@php
    $eventLabels = ['created' => 'Tambah', 'updated' => 'Ubah', 'deleted' => 'Hapus'];
    $eventBadges = ['created' => 'bg-light-success', 'updated' => 'bg-light-warning', 'deleted' => 'bg-light-danger'];
@endphp

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Activity Log</h3>
                    <p class="text-subtitle text-muted">Riwayat tambah, ubah, dan hapus data beserta pelakunya.</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">ACL</li>
                            <li class="breadcrumb-item active" aria-current="page">Activity Log</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <form method="GET" action="{{ route('audit.index') }}" class="row g-2 align-items-end">
                        <div class="col-12 col-sm-4 col-lg-3">
                            <label for="event" class="form-label">Aksi</label>
                            <select id="event" name="event" class="form-select">
                                <option value="">Semua aksi</option>
                                @foreach ($eventLabels as $value => $label)
                                    <option value="{{ $value }}" @selected(($filters['event'] ?? '') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-sm-4 col-lg-3">
                            <label for="model" class="form-label">Data</label>
                            <select id="model" name="model" class="form-select">
                                <option value="">Semua data</option>
                                @foreach ($models as $model)
                                    <option value="{{ $model }}" @selected(($filters['model'] ?? '') === $model)>{{ class_basename($model) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-sm-4 col-lg-3 d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Terapkan</button>
                            @if ($filters)
                                <a href="{{ route('audit.index') }}" class="btn btn-light-secondary">Reset</a>
                            @endif
                        </div>
                    </form>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Waktu</th>
                                    <th>User</th>
                                    <th>Aksi</th>
                                    <th>Data</th>
                                    <th>IP</th>
                                    <th class="text-end"><span class="visually-hidden">Detail</span></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($logs as $log)
                                    <tr>
                                        <td class="text-nowrap" title="{{ $log->created_at->format('d M Y H:i:s') }}">
                                            {{ $log->created_at->format('d M Y H:i') }}
                                        </td>
                                        <td>{{ $log->user->name ?? 'Sistem' }}</td>
                                        <td>
                                            <span class="badge {{ $eventBadges[$log->event] ?? 'bg-light-secondary' }}">
                                                {{ $eventLabels[$log->event] ?? $log->event }}
                                            </span>
                                        </td>
                                        <td>{{ class_basename($log->model) }} #{{ $log->model_id }}</td>
                                        <td class="text-muted">{{ $log->ip }}</td>
                                        <td class="text-end">
                                            @can('acl_audit_show')
                                                <a href="{{ route('audit.show', $log) }}" class="btn btn-sm btn-light-info">Detail</a>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-muted">
                                            {{ $filters ? 'Tidak ada log yang cocok dengan filter ini.' : 'Belum ada perubahan data yang tercatat.' }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{ $logs->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </section>
    </div>
@endsection
