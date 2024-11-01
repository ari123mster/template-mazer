@extends('layouts.dashboard')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Menu User</h3>

                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href=""></a>ACL</li>
                            <li class="breadcrumb-item active" aria-current="page">user</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Minimal jQuery Datatable start -->

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <div class="button">
                        @can('acl_user_create')
                            <a href="{{ route('user.create') }}" class="btn btn-primary">Tambah Data</a>
                        @endcan
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="table1">
                            <thead>
                                <tr>
                                    <th>Name</th>

                                    <th>Roles</th>
                                    <th>last seen</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($allUsers as $user)
                                    <tr>
                                        <td>{{ $user->name }}</td>

                                        <td>
                                            @foreach ($user->roles()->pluck('name') as $item)
                                                <span class="badge bg-primary"> {{ $item }}</span>
                                            @endforeach
                                        </td>
                                        <td>{{ $user->formatted_last_seen }}</td>
                                        <td> <span class="badge {{ $user->isActive() ? 'bg-primary' : 'bg-danger' }}">
                                                {{ $user->status }}
                                            </span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </section>
        <!-- Basic Tables end -->

    </div>
@endsection
