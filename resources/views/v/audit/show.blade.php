@extends('layouts.dashboard')

@php
    $eventLabels = ['created' => 'menambah', 'updated' => 'mengubah', 'deleted' => 'menghapus'];
    $format = fn ($value) => is_scalar($value) || $value === null
        ? (string) ($value ?? '—')
        : json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
@endphp

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>{{ class_basename($log->model) }} #{{ $log->model_id }}</h3>
                    <p class="text-subtitle text-muted">
                        {{ $log->user->name ?? 'Sistem' }} {{ $eventLabels[$log->event] ?? $log->event }} data ini
                        pada {{ $log->created_at->format('d M Y H:i:s') }} dari IP {{ $log->ip ?? '—' }}.
                    </p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('audit.index') }}">Activity Log</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Detail</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Isi data</h4>
                    @if ($log->event === 'updated')
                        <p class="text-muted mb-0 mt-1">Baris yang disorot adalah kolom yang berubah.</p>
                    @endif
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Kolom</th>
                                    @if ($log->event === 'updated')
                                        <th>Sebelum</th>
                                        <th>Sesudah</th>
                                    @else
                                        <th>Nilai</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($changes as $row)
                                    <tr class="{{ $row['changed'] ? 'table-warning' : '' }}">
                                        <td class="fw-semibold">{{ $row['field'] }}</td>
                                        @if ($log->event === 'updated')
                                            <td class="audit-value">{{ $format($row['old']) }}</td>
                                            <td class="audit-value">{{ $format($row['new']) }}</td>
                                        @else
                                            <td class="audit-value">{{ $format($row['new'] ?? $row['old']) }}</td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @if ($headers->isNotEmpty())
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Request</h4>
                    </div>
                    <div class="card-body">
                        <dl class="row mb-0">
                            @foreach ($headers as $name => $value)
                                <dt class="col-sm-3">{{ $name }}</dt>
                                <dd class="col-sm-9 audit-value">{{ is_array($value) ? implode(', ', $value) : $value }}</dd>
                            @endforeach
                        </dl>
                    </div>
                </div>
            @endif

            <a href="{{ url()->previous() === url()->current() ? route('audit.index') : url()->previous() }}" class="btn btn-light-secondary">Kembali</a>
        </section>
    </div>
@endsection
@push('style')
    <style>
        .audit-value {
            overflow-wrap: anywhere;
        }
    </style>
@endpush
