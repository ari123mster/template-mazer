@extends('layouts.dashboard')
@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <div class="section-header-back">
                    <a href="{{ route('user.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
                </div>


            </div>

            <div class="section-body">
                <h2 class="section-title">Edit Data User</h2>

                <section id="multiple-column-form">
                    <div class="row match-height">
                        <div class="col-md-12 ">
                            <div class="card">

                                <div class="card-content">
                                    <div class="card-body">
                                        <form action="{{ route('user.update', $user->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="form-body">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label>Nama </label>
                                                    </div>
                                                    <div class="col-md-8 form-group">
                                                        <input type="text" id="name" class="form-control"
                                                            name="name" value="{{ $user->name }}">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label>Email</label>
                                                    </div>
                                                    <div class="col-md-8 form-group">
                                                        <input type="text" id="email" class="form-control"
                                                            name="email" value="{{ $user->email }}">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label>Roles</label>
                                                    </div>
                                                    <div class="col-md-8 form-group">
                                                        <select class="form-control" name="role">

                                                            @foreach ($roles as $role)
                                                                <option value="{{ $role->id }}"
                                                                    @if ($user->hasRole($role->name)) selected @endif>
                                                                    {{ $role->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>


                                                    <div class="col-md-4">
                                                        <label>password </label>
                                                    </div>
                                                    <div class="col-md-8 form-group">
                                                        <input type="text" id="password" class="form-control"
                                                            name="password"
                                                            placeholder="Leave blank to keep current password">
                                                    </div>

                                                    <div class="col-sm-12 d-flex justify-content-end">
                                                        <button type="submit"
                                                            class="btn btn-primary me-1 mb-1">Submit</button>
                                                        <button type="reset"
                                                            class="btn btn-light-secondary me-1 mb-1">Reset</button>
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

            </div>
        </section>
    </div>
@endsection
