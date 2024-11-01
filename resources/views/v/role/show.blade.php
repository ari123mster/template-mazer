@extends('layouts.dashboard')
@section('content')
    <section id="multiple-column-form">
        <div class="row match-height">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">edit Hak Akses</h4>
                    </div>

                    <div class="card-content">
                        <div class="card-body">


                            <form action="{{ route('role.update', $roles->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="form-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label>Nama</label>
                                        </div>
                                        <div class="col-md-8 form-group">
                                            <input type="text" id="name" class="form-control" name="name"
                                                value="{{ $roles->name }}" readonly>
                                        </div>
                                        <div class="col-md-4">
                                            <label>Hak Akses</label>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="accordion" id="accordionExample">
                                                @foreach ($permissionsData as $key => $data)
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header" id="heading{{ ucfirst($key) }}">
                                                            <button class="accordion-button collapsed" type="button"
                                                                data-bs-toggle="collapse"
                                                                data-bs-target="#collapse{{ ucfirst($key) }}"
                                                                aria-expanded="false"
                                                                aria-controls="collapse{{ ucfirst($key) }}">
                                                                <p>{{ ucfirst($key) }}</p>
                                                            </button>
                                                        </h2>
                                                        <div id="collapse{{ ucfirst($key) }}"
                                                            class="accordion-collapse collapse"
                                                            aria-labelledby="heading{{ ucfirst($key) }}"
                                                            data-bs-parent="#accordionExample">
                                                            <div class="accordion-body">
                                                                <h5>Assigned Permissions</h5>
                                                                <div class="table-responsive">
                                                                    <table class="table">
                                                                        <tbody>
                                                                            @foreach ($data['assigned'] as $assigned)
                                                                                <tr>
                                                                                    <td>
                                                                                        <input type="text"
                                                                                            class="form-control"
                                                                                            value="{{ str_replace('_', ' ', $assigned->name) }}"
                                                                                            readonly>
                                                                                    </td>
                                                                                </tr>
                                                                            @endforeach
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>

                                            <div class="col-sm-12 d-flex justify-content-end mt-3">
                                                <a href="{{ route('role.index') }}"
                                                    class="btn btn-secondary me-1 mb-1">Back</a>
                                            </div>


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
