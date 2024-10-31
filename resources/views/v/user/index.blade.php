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
                                    <th>Email</th>
                                    <th>Roles</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $user)
                                    <tr>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            @foreach ($user->roles()->pluck('name') as $item)
                                                <span class="badge bg-primary"> {{ $item }}</span>
                                            @endforeach
                                        </td>
                                        <td>
                                            @can('acl_user_edit')
                                                <a href="{{ route('user.edit', $user->id) }}"
                                                    class="btn btn-success d-inline ">Edit</a>
                                            @endcan
                                            @can('acl_user_delete')
                                                <form action="{{ route('user.destroy', $user->id) }}" method="POST"
                                                    class="d-inline">

                                                    @csrf
                                                    @method('DELETE')

                                                    <button class="btn btn-danger">
                                                        Delete
                                                    </button>
                                                </form>
                                            @endcan

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
