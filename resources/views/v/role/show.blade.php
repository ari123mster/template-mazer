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
                                            <div class="accordion" id="permissionsAccordion">
                                                @foreach ($permissionsData as $parent => $data)
                                                    @php
                                                        // Buat ID unik untuk accordion collapse berdasarkan parent nama
                                                        $collapseId = 'collapse' . Str::slug($parent);
                                                        $headingId = 'heading' . Str::slug($parent);
                                                    @endphp
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header" id="{{ $headingId }}">
                                                            <button class="accordion-button collapsed" type="button"
                                                                data-bs-toggle="collapse"
                                                                data-bs-target="#{{ $collapseId }}" aria-expanded="false"
                                                                aria-controls="{{ $collapseId }}">
                                                                {{ ucfirst(str_replace('_', ' ', $parent)) }}
                                                            </button>
                                                        </h2>

                                                        <div id="{{ $collapseId }}" class="accordion-collapse collapse"
                                                            aria-labelledby="{{ $headingId }}"
                                                            data-bs-parent="#permissionsAccordion">
                                                            <div class="accordion-body">
                                                                @if (count($data['assigned']) > 0)
                                                                    <ul class="list-group">
                                                                        @foreach ($data['assigned'] as $perm)
                                                                            @php
                                                                                // Ambil bagian child permission setelah parent prefix
                                                                                $childName = Str::after(
                                                                                    $perm->name,
                                                                                    $parent . '_',
                                                                                );
                                                                                $childLabel = ucwords(
                                                                                    str_replace('_', ' ', $childName),
                                                                                );
                                                                            @endphp
                                                                            <li class="list-group-item">
                                                                                {{ $childLabel }}
                                                                            </li>
                                                                        @endforeach
                                                                    </ul>
                                                                @else
                                                                    <p class="text-muted">Tidak ada permission yang
                                                                        ditugaskan.</p>
                                                                @endif
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
