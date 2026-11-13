@php
    // $permissions: [group => [subgroup => Collection<Permission>]], $assigned: string[], $readonly: bool
    $readonly = $readonly ?? false;
    $checked = ! $readonly && session()->hasOldInput() ? (array) old('permission', []) : $assigned;
    $actionLabels = [
        'index' => 'Lihat daftar',
        'show' => 'Lihat detail',
        'create' => 'Tambah',
        'edit' => 'Ubah',
        'delete' => 'Hapus',
    ];
    $actionRank = array_flip(array_keys($actionLabels));
@endphp

<div class="perm-picker">
    @forelse ($permissions as $group => $subgroups)
        <fieldset class="perm-group">
            <legend class="perm-group__title">{{ ucfirst(str_replace('-', ' ', $group)) }}</legend>

            @foreach ($subgroups as $subgroup => $items)
                @php
                    $subgroupId = 'perm-' . Str::slug($group . '-' . $subgroup);
                    $selected = $items->whereIn('name', $checked)->count();
                @endphp
                <div class="perm-subgroup" data-perm-subgroup>
                    <div class="perm-subgroup__head">
                        <span class="perm-subgroup__name" id="{{ $subgroupId }}">{{ ucfirst($subgroup) }}</span>
                        <span class="perm-subgroup__count" data-perm-count>{{ $selected }}/{{ $items->count() }}</span>
                        @unless ($readonly)
                            <label class="form-check perm-subgroup__all">
                                <input class="form-check-input" type="checkbox" data-perm-toggle
                                    aria-describedby="{{ $subgroupId }}">
                                <span class="form-check-label">Pilih semua</span>
                            </label>
                        @endunless
                    </div>

                    <div class="perm-grid" role="group" aria-labelledby="{{ $subgroupId }}">
                        @foreach ($items->sortBy(fn ($permission) => $actionRank[Str::afterLast($permission->name, '_')] ?? 99) as $permission)
                            @php
                                $action = Str::afterLast($permission->name, '_');
                                $isChecked = in_array($permission->name, $checked, true);
                            @endphp
                            <label class="perm-item {{ $isChecked ? 'is-checked' : '' }}">
                                <input class="form-check-input" type="checkbox" name="permission[]"
                                    value="{{ $permission->name }}" data-perm-item
                                    @checked($isChecked) @disabled($readonly)>
                                <span class="perm-item__text">
                                    <span class="perm-item__label">{{ $actionLabels[$action] ?? ucfirst(str_replace('_', ' ', $action)) }}</span>
                                    <code class="perm-item__name">{{ $permission->name }}</code>
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </fieldset>
    @empty
        <p class="text-muted mb-0">
            Belum ada permission. Jalankan <code>php artisan db:seed --class=PermissionSeeder</code> untuk mengisinya.
        </p>
    @endforelse
</div>

@once
    @push('style')
        <style>
            .perm-picker {
                display: grid;
                gap: 1rem;
            }

            .perm-picker .perm-group {
                margin: 0;
                padding: 1rem 1.25rem 1.25rem;
                border: 1px solid var(--bs-border-color);
                border-radius: .5rem;
            }

            .perm-picker .perm-group__title {
                float: none;
                width: auto;
                margin: 0 0 .75rem;
                padding: 0 .375rem;
                font-size: 1rem;
                font-weight: 700;
            }

            .perm-subgroup + .perm-subgroup {
                margin-top: 1rem;
                padding-top: 1rem;
                border-top: 1px dashed var(--bs-border-color);
            }

            .perm-subgroup__head {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                gap: .5rem 1rem;
                margin-bottom: .625rem;
            }

            .perm-subgroup__name {
                font-weight: 600;
            }

            .perm-subgroup__count {
                font-size: .875rem;
                font-variant-numeric: tabular-nums;
                color: var(--bs-secondary-color);
            }

            /* .perm-picker prefix outranks Mazer's html[data-bs-theme=dark] label/code rules */
            .perm-picker .perm-subgroup__all {
                margin: 0 0 0 auto;
                min-height: 44px;
                display: flex;
                align-items: center;
                gap: .5rem;
                padding-left: 0;
            }

            .perm-subgroup__all .form-check-input {
                float: none;
                margin: 0;
            }

            .perm-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(190px, 1fr));
                gap: .5rem;
            }

            .perm-picker .perm-item {
                display: flex;
                align-items: flex-start;
                gap: .625rem;
                min-height: 44px;
                margin: 0;
                padding: .625rem .75rem;
                border: 1px solid var(--bs-border-color);
                border-radius: .375rem;
                cursor: pointer;
                transition: border-color .15s ease-out, background-color .15s ease-out;
            }

            .perm-item:hover {
                border-color: var(--bs-primary);
            }

            .perm-item.is-checked {
                border-color: var(--bs-primary);
                background-color: var(--bs-primary-bg-subtle);
            }

            .perm-item:has(:disabled) {
                cursor: default;
            }

            .perm-item .form-check-input {
                flex: none;
                margin: .2rem 0 0;
            }

            .perm-item .form-check-input:disabled {
                opacity: 1;
            }

            .perm-item__text {
                display: grid;
                min-width: 0;
            }

            .perm-item__label {
                font-weight: 500;
            }

            .perm-picker .perm-item__name {
                font-size: .75rem;
                color: var(--bs-secondary-color);
                overflow-wrap: anywhere;
            }

            @media (prefers-reduced-motion: reduce) {
                .perm-item {
                    transition: none;
                }
            }
        </style>
    @endpush

    @push('script')
        <script>
            document.querySelectorAll('[data-perm-subgroup]').forEach((box) => {
                const toggle = box.querySelector('[data-perm-toggle]');
                const count = box.querySelector('[data-perm-count]');
                const items = [...box.querySelectorAll('[data-perm-item]')];

                const sync = () => {
                    const selected = items.filter((item) => item.checked).length;
                    items.forEach((item) => item.closest('.perm-item').classList.toggle('is-checked', item.checked));
                    count.textContent = `${selected}/${items.length}`;
                    if (toggle) {
                        toggle.checked = selected === items.length;
                        toggle.indeterminate = selected > 0 && selected < items.length;
                    }
                };

                toggle?.addEventListener('change', () => {
                    items.forEach((item) => { item.checked = toggle.checked; });
                    sync();
                });
                items.forEach((item) => item.addEventListener('change', sync));
                sync();
            });
        </script>
    @endpush
@endonce
