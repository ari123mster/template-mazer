@extends('layouts.dashboard')
@section('content')
    <section id="multiple-column-form">
        <div class="row match-height">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Lihat Hak Akses</h4>
                    </div>

                    <div class="card-content">
                        <div class="card-body">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <form action="{{ route('role.store') }}" method="POST">
                                @csrf

                                <div class="form-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label>Nama</label>
                                        </div>
                                        <div class="col-md-8 form-group">
                                            <input type="text" id="name" class="form-control" name="name">
                                        </div>
                                        <div class="col-md-4">
                                            <label>Hak Akses</label>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="accordion" id="accordionExample">
                                                <div class="accordion" id="accordionExample">
                                                    @foreach ($permissions as $key => $permissionGroup)
                                                        <div class="accordion-item">
                                                            <h2 class="accordion-header" id="heading{{ ucfirst($key) }}">
                                                                <button class="accordion-button collapsed" type="button"
                                                                    data-bs-toggle="collapse"
                                                                    data-bs-target="#collapse{{ ucfirst($key) }}"
                                                                    aria-expanded="false"
                                                                    aria-controls="collapse{{ ucfirst($key) }}">
                                                                    <p>{{ ucfirst($key) }} </p>
                                                                </button>
                                                            </h2>
                                                            <div id="collapse{{ ucfirst($key) }}"
                                                                class="accordion-collapse collapse"
                                                                aria-labelledby="heading{{ ucfirst($key) }}"
                                                                data-bs-parent="#accordionExample">
                                                                <div class="accordion-body">
                                                                    <div class="table-responsive">
                                                                        <table>
                                                                            @foreach ($permissionGroup as $permission)
                                                                                <tr>
                                                                                    <td>
                                                                                        <input class="form-check-input"
                                                                                            type="checkbox"
                                                                                            name="permission[]"
                                                                                            value="{{ $permission->name }}">
                                                                                    </td>
                                                                                    <td>
                                                                                        {{ str_replace('_', ' ', $permission->name) }}
                                                                                    </td>
                                                                                </tr>
                                                                            @endforeach
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>

                                            </div>
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
