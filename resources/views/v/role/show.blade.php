@extends('layouts.dashboard')
@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Role {{ $role->name }}</h3>
                    <p class="text-subtitle text-muted">
                        {{ count($assigned) }} permission aktif.
                        @if ($role->name === 'super-admin')
                            Role ini selalu lolos semua pemeriksaan hak akses.
                        @endif
                    </p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('role.index') }}">Hak Akses</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Detail</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-body">
                    @include('v.role.partials.permissions', ['readonly' => true])
                </div>
                <div class="card-footer d-flex flex-wrap justify-content-between gap-2">
                    <a href="{{ route('role.index') }}" class="btn btn-light-secondary">Kembali</a>
                    @can('acl_role_edit')
                        <a href="{{ route('role.edit', $role->id) }}" class="btn btn-primary">Ubah role</a>
                    @endcan
                </div>
            </div>
        </section>
    </div>
@endsection
