@extends('layouts.dashboard')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Menu Logs User</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">ACL</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Logs</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Tabel Pengguna -->
        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h4>Data Pengguna</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="table1">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Name</th>
                                    <th>Roles</th>
                                    <th>Last Seen</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($allUsers as $index => $user)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>
                                            @foreach ($user->roles()->pluck('name') as $item)
                                                <span class="badge bg-primary">{{ $item }}</span>
                                            @endforeach
                                        </td>
                                        <td>{{ $user->formatted_last_seen }}</td>
                                        <td>
                                            <span class="badge {{ $user->isActive() ? 'bg-primary' : 'bg-danger' }}">
                                                {{ $user->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>

        <!-- Tabel Aktivitas Pengguna -->
        <section class="section mt-4">
            <div class="card">
                <div class="card-header">
                    <h4>Aktivitas Pengguna</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="table2">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Model</th>
                                    <th>Model ID</th>
                                    <th>Event</th>
                                    <th>User</th>
                                    <th>IP</th>
                                    <th>Old Data</th>
                                    <th>New Data</th>
                                    <th>Headers</th>
                                    <th>Waktu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($logs as $index => $log)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ class_basename($log->model) }}</td>
                                        <td>{{ $log->model_id }}</td>
                                        <td>{{ ucfirst($log->event) }}</td>
                                        <td>{{ optional($log->user)->name ?? 'Guest' }}</td>
                                        <td>{{ $log->ip }}</td>
                                        <td>
                                            <pre style="max-height: 150px; overflow:auto;">{{ json_encode($log->old_data, JSON_PRETTY_PRINT) }}</pre>
                                        </td>
                                        <td>
                                            <pre style="max-height: 150px; overflow:auto;">{{ json_encode($log->new_data, JSON_PRETTY_PRINT) }}</pre>
                                        </td>
                                        <td>
                                            <pre style="max-height: 150px; overflow:auto;">{{ json_encode($log->headers, JSON_PRETTY_PRINT) }}</pre>
                                        </td>
                                        <td>{{ $log->created_at->format('d M Y H:i:s') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
