@php
    // $role: Role|null (null = create)
    $isSuperAdmin = $role?->name === 'super-admin';
@endphp

<form action="{{ $role ? route('role.update', $role->id) : route('role.store') }}" method="POST" novalidate>
    @csrf
    @if ($role)
        @method('PUT')
    @endif

    <div class="card">
        <div class="card-body">
            @if ($isSuperAdmin)
                <div class="alert alert-light-info" role="note">
                    Role <strong>super-admin</strong> selalu lolos semua pemeriksaan hak akses, apa pun permission
                    yang dicentang. Namanya tidak dapat diubah.
                </div>
            @endif

            <div class="row">
                <div class="col-12 col-lg-6 form-group">
                    <label for="name" class="form-label">Nama role</label>
                    <input type="text" id="name" name="name" maxlength="125" required
                        class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name', $role?->name) }}" placeholder="contoh: operator"
                        @readonly($isSuperAdmin) @error('name') aria-describedby="name-error" @enderror>
                    @error('name')
                        <div class="invalid-feedback" id="name-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <h5 class="mt-2 mb-1">Permission</h5>
            <p class="text-muted mb-3">Centang aksi yang boleh dijalankan oleh pemilik role ini.</p>
            @error('permission.*')
                <div class="alert alert-light-danger">{{ $message }}</div>
            @enderror

            @include('v.role.partials.permissions', ['readonly' => false])
        </div>

        <div class="card-footer d-flex flex-wrap justify-content-between gap-2">
            <a href="{{ route('role.index') }}" class="btn btn-light-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">{{ $role ? 'Simpan perubahan' : 'Simpan role' }}</button>
        </div>
    </div>
</form>
