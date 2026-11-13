@extends('layouts.dashboard')

@section('content')
    <div class="page-heading">
        <h3>Halo, {{ auth()->user()->name }}</h3>
        <p class="text-subtitle text-muted">
            Role: {{ auth()->user()->getRoleNames()->join(', ') ?: 'belum ada' }}
        </p>
    </div>

    <div class="page-content">
        <section class="row">
            <div class="col-12 col-lg-8">
                <div class="card">
                    <div class="card-header d-flex flex-wrap align-items-baseline justify-content-between gap-2">
                        <h4 class="card-title mb-0">Sedang online</h4>
                        <span class="text-muted">
                            {{ $onlineUsers->count() }} dari {{ $totalUsers }} user aktif dalam 10 menit terakhir
                        </span>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0 dash-list">
                            @foreach ($onlineUsers as $online)
                                <li class="d-flex flex-wrap justify-content-between gap-2">
                                    <span>
                                        <span class="fw-semibold">{{ $online->name }}</span>
                                        @foreach ($online->roles as $role)
                                            <span class="badge bg-light-primary ms-1">{{ $role->name }}</span>
                                        @endforeach
                                    </span>
                                    <span class="text-muted">{{ \Illuminate\Support\Carbon::parse($online->last_seen)->diffForHumans() }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                @can('acl_log_index')
                    <div class="card">
                        <div class="card-header d-flex align-items-baseline justify-content-between gap-2">
                            <h4 class="card-title mb-0">Perubahan data terakhir</h4>
                            <a href="{{ route('log.index') }}">Lihat semua log</a>
                        </div>
                        <div class="card-body">
                            @if ($recentLogs->isEmpty())
                                <p class="text-muted mb-0">Belum ada perubahan data yang tercatat.</p>
                            @else
                                <ul class="list-unstyled mb-0 dash-list">
                                    @foreach ($recentLogs as $log)
                                        <li class="d-flex flex-wrap justify-content-between gap-2">
                                            <span>
                                                <span class="fw-semibold">{{ $log->user->name ?? 'Sistem' }}</span>
                                                {{ ['created' => 'membuat', 'updated' => 'mengubah', 'deleted' => 'menghapus'][$log->event] ?? $log->event }}
                                                {{ class_basename($log->model) }} #{{ $log->model_id }}
                                            </span>
                                            <span class="text-muted">{{ $log->created_at->diffForHumans() }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                @endcan
            </div>

            <div class="col-12 col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <dl class="mb-0 dash-totals">
                            <div>
                                <dt>User terdaftar</dt>
                                <dd>{{ $totalUsers }}</dd>
                            </div>
                            <div>
                                <dt>Role</dt>
                                <dd>{{ $totalRoles }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                @canany(['acl_user_index', 'acl_role_index'])
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Akses cepat</h4>
                        </div>
                        <div class="card-body d-grid gap-2">
                            @can('acl_user_create')
                                <a href="{{ route('user.create') }}" class="btn btn-primary">Tambah user</a>
                            @endcan
                            @can('acl_user_index')
                                <a href="{{ route('user.index') }}" class="btn btn-light-secondary">Kelola user</a>
                            @endcan
                            @can('acl_role_index')
                                <a href="{{ route('role.index') }}" class="btn btn-light-secondary">Kelola hak akses</a>
                            @endcan
                        </div>
                    </div>
                @endcanany
            </div>
        </section>
    </div>
@endsection
@push('style')
    <style>
        .dash-list li {
            padding: .625rem 0;
            border-bottom: 1px solid var(--bs-border-color);
        }

        .dash-list li:last-child {
            border-bottom: 0;
        }

        .dash-totals {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .dash-totals dt {
            font-weight: 500;
            color: var(--bs-secondary-color);
        }

        .dash-totals dd {
            margin: 0;
            font-size: 2rem;
            font-weight: 700;
            font-variant-numeric: tabular-nums;
        }
    </style>
@endpush
