@extends('layouts.dashboard')
@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Hak Akses</h3>
                    <p class="text-subtitle text-muted">Role menentukan menu dan aksi yang boleh dipakai setiap user.</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item active" aria-current="page">Hak Akses</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            @if (session('success'))
                <div class="alert alert-light-success alert-dismissible fade show" role="status">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <h4 class="card-title mb-0">{{ $roles->count() }} role</h4>
                    @can('acl_role_create')
                        <a href="{{ route('role.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-lg" aria-hidden="true"></i> Tambah role
                        </a>
                    @endcan
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="table1">
                            <thead>
                                <tr>
                                    <th>Role</th>
                                    <th class="text-end">Permission</th>
                                    <th class="text-end">User</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($roles as $role)
                                    <tr>
                                        <td>
                                            <span class="fw-semibold">{{ $role->name }}</span>
                                            @if ($role->name === 'super-admin')
                                                <span class="badge bg-light-primary ms-1">akses penuh</span>
                                            @endif
                                        </td>
                                        <td class="text-end tabular">{{ $role->permissions_count }}</td>
                                        <td class="text-end tabular">{{ $role->users_count }}</td>
                                        <td class="text-end text-nowrap">
                                            @can('acl_role_show')
                                                <a href="{{ route('role.show', $role->id) }}"
                                                    class="btn btn-sm btn-light-info">Detail</a>
                                            @endcan
                                            @can('acl_role_edit')
                                                <a href="{{ route('role.edit', $role->id) }}"
                                                    class="btn btn-sm btn-light-primary">Ubah</a>
                                            @endcan
                                            @if ($role->name !== 'super-admin')
                                                @can('acl_role_delete')
                                                    <form action="{{ route('role.destroy', $role->id) }}" method="POST"
                                                        class="d-inline"
                                                        onsubmit="return confirm('Hapus role ini? {{ $role->users_count }} user akan kehilangan role ini.')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-light-danger">Hapus</button>
                                                    </form>
                                                @endcan
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
@push('style')
    <style>
        .tabular {
            font-variant-numeric: tabular-nums;
        }
    </style>
@endpush
