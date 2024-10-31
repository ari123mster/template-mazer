@extends('layouts.dashboard')
@section('content')
    <section id="multiple-column-form">
        <div class="row match-height">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Tambah Data User</h4>
                    </div>

                    <div class="card-content">
                        <div class="card-body">
                            <form action="{{ route('user.store') }}" method="POST">
                                @csrf
                                <div class="form-body">
                                    <div class="row">


                                        <div class="col-md-4">
                                            <label>Nama</label>
                                        </div>
                                        <div class="col-md-8 form-group">
                                            <input type="text" id="name" class="form-control" name="name"
                                                placeholder="nama">
                                        </div>
                                        <div class="col-md-4">
                                            <label>Email</label>
                                        </div>
                                        <div class="col-md-8 form-group">
                                            <input type="email" id="email" class="form-control" name="email"
                                                placeholder="email">
                                        </div>
                                        <div class="col-md-4">
                                            <label>password</label>
                                        </div>
                                        <div class="col-md-8 form-group">
                                            <input type="text" id="password" class="form-control" name="password"
                                                placeholder="password">
                                        </div>
                                        <div class="col-md-4">
                                            <label>Hak Akses</label>
                                        </div>
                                        <div class="col-md-8 form-group">
                                            <select class="form-control" name="roles" id="roles">
                                                <option>--pilih--</option>
                                                @foreach ($roles as $roles)
                                                    <option value="{{ $roles->id }}">{{ $roles->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-sm-12 d-flex justify-content-end">
                                            <button type="submit" class="btn btn-primary me-1 mb-1">Submit</button>
                                            <button type="reset" class="btn btn-light-secondary me-1 mb-1">Reset</button>
                                        </div>
                                    </div>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
