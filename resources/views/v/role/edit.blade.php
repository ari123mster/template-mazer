@extends('layouts.dashboard')
@section('content')
    <section id="multiple-column-form">
        <div class="row match-height">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Edit Hak Akses</h4>
                    </div>

                    <div class="card-content">
                        <div class="card-body">
                            <form action="{{ route('role.update', $roles->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="form-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="name">Nama</label>
                                        </div>
                                        <div class="col-md-8 form-group">
                                            <input type="text" id="name" class="form-control" name="name"
                                                value="{{ $roles->name }}">
                                        </div>

                                        <div class="col-md-4 mt-3">
                                            <label>Hak Akses</label>
                                        </div>
                                        <div class="col-md-8 mt-3">
                                            <div class="accordion" id="accordionPermissions">
                                                @foreach ($permissions as $parent => $children)
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header" id="heading{{ Str::slug($parent) }}">
                                                            <button class="accordion-button collapsed" type="button"
                                                                data-bs-toggle="collapse"
                                                                data-bs-target="#collapse{{ Str::slug($parent) }}"
                                                                aria-expanded="false"
                                                                aria-controls="collapse{{ Str::slug($parent) }}">
                                                                {{ str_replace('_', ' ', ucfirst($parent)) }}
                                                            </button>
                                                        </h2>
                                                        <div id="collapse{{ Str::slug($parent) }}"
                                                            class="accordion-collapse collapse"
                                                            aria-labelledby="heading{{ Str::slug($parent) }}"
                                                            data-bs-parent="#accordionPermissions">
                                                            <div class="accordion-body">
                                                                <table class="table">
                                                                    <tbody>
                                                                        @foreach ($children as $perm)
                                                                            <tr>
                                                                                <td style="width: 30px;">
                                                                                    <input type="checkbox"
                                                                                        class="form-check-input"
                                                                                        id="perm-{{ $perm['id'] }}"
                                                                                        name="permission[]"
                                                                                        value="{{ $perm['name'] }}"
                                                                                        {{ in_array($perm['id'], $rolePermissions) ? 'checked' : '' }}>
                                                                                </td>
                                                                                <td>
                                                                                    <label for="perm-{{ $perm['id'] }}">
                                                                                        {{ $perm['child_label'] ? ucfirst($perm['child_label']) : str_replace('_', ' ', $perm['name']) }}
                                                                                    </label>
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>

                                        <div class="col-sm-12 d-flex justify-content-end mt-3">
                                            <button type="submit" class="btn btn-primary me-1 mb-1">Update</button>
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
