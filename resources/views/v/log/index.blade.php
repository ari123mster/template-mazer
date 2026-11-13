@extends('layouts.dashboard')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Log Aktif</h3>
                    <p class="text-subtitle text-muted">User dianggap online bila membuka aplikasi dalam 10 menit terakhir.</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">ACL</li>
                            <li class="breadcrumb-item active" aria-current="page">Log Aktif</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="table1">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Role</th>
                                    <th>Terakhir terlihat</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($allUsers as $user)
                                    <tr>
                                        <td>{{ $user->name }}</td>
                                        <td>
                                            @foreach ($user->roles as $role)
                                                <span class="badge bg-light-primary">{{ $role->name }}</span>
                                            @endforeach
                                        </td>
                                        <td data-order="{{ $user->last_seen ? \Illuminate\Support\Carbon::parse($user->last_seen)->timestamp : 0 }}">
                                            {{ $user->last_seen ? \Illuminate\Support\Carbon::parse($user->last_seen)->diffForHumans() : 'belum pernah' }}
                                        </td>
                                        <td>
                                            @if ($user->isActive() && $user->last_seen)
                                                <span class="badge bg-success">Online</span>
                                            @else
                                                <span class="badge bg-light-secondary">Offline</span>
                                            @endif
                                        </td>
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
