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
                                                value="{{ $roles->name }}">
                                        </div>
                                        <div class="col-md-4">
                                            <label>Hak Akses</label>
                                        </div>
                                        <div class="col-md-8">
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
                                                                        @foreach ($permissionGroup['all'] as $permission)
                                                                            <tr>
                                                                                <td>
                                                                                    <input class="form-check-input"
                                                                                        type="checkbox" name="permission[]"
                                                                                        value="{{ $permission->name }}"
                                                                                        @if (in_array($permission->name, $permissionGroup['assigned']->pluck('name')->toArray())) checked @endif>
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

                                        <div class="col-sm-12 d-flex justify-content-end">
                                            <button type="submit" class="btn btn-primary me-1 mb-1"
                                                style="margin-top: 10px;">Submit</button>
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
