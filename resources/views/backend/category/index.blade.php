<x-backend.layouts.main title="{{ 'Қатегориялар' }}">

    <div class="container-fluid">

        <div class="card-custom shadow-sm">
            <div class="card-header-custom action-btns">
                <x-backend.action route="category" :back="true" :create="true"/>
            </div>

            <div class="card-body p-0">
                <form id="categoryFilterForm" method="GET" action="{{ route('category.index') }}">

                    <div class="table-responsive d-none d-md-block">
                        <table class="table mb-0">
                            <thead>
                            <tr class="text-center">
                                <th class="col-id">{!! sortLink('id', 'ID') !!}</th>
                                <th>{!! sortLink('title', 'Номи') !!}</th>
                                <th>{!! sortLink('type', 'Тури') !!}</th>
                                <th class="col-date">{!! sortLink('created_at', 'Яратилди') !!}</th>
                                <th></th>
                            </tr>
                            <tr class="filter-row">
                                <th><input type="text" name="filters[id]" value="{{ request('filters.id') }}" class="form-control" placeholder="№..."></th>
                                <th><input type="text" name="filters[title]" value="{{ request('filters.title') }}" class="form-control" placeholder="Қидирув..."></th>
                                <th>
                                    <select name="filters[type]" class="form-control">
                                        <option value="">Барчаси</option>
                                        @foreach(\App\Services\StatusService::getType() as $key => $label)
                                            <option value="{{ $key }}" {{ (string) request('filters.type') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </th>
                                <th>
                                    <div class="d-flex align-items-center">
                                        <input type="date" name="filters[created_from]" value="{{ request('filters.created_from') }}" class="form-control me-1" title="Дан">
                                        <input type="date" name="filters[created_to]" value="{{ request('filters.created_to') }}" class="form-control" title="Гача">
                                    </div>
                                </th>
                                <th class="p-0">
                                    <div class="d-flex justify-content-center align-items-center" style="min-height: 75px;">
                                        <button type="submit" class="btn btn-custom-search" title="Филтрлаш">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </div>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($categories as $category)
                                <tr class="text-center">
                                    <td>{{ $category->id }}</td>
                                    <td>
                                        <div class="fw-bold" style="color: var(--text-main);">{{ $category->title }}</div>
                                    </td>
                                    <td>
                                        <span class="badge-custom {{ \App\Services\StatusService::getTypeClass()[$category->type] }}">{{ \App\Services\StatusService::getType()[$category->type] }}</span>
                                    </td>
                                    <td>
                                        <div>
                                            <i class="bi bi-calendar3 me-1"></i> {{ $category->created_at?->format('d.m.Y') }}
                                            <div class="text-xs">{{ $category->created_at?->format('H:i') }}</div>
                                        </div>
                                    </td>
                                    <td class="text-center action-btns">
                                        <x-backend.action route="category" :id="$category->id" :view="true" :edit="true"/>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-5 text-center">
                                        <img src="{{ asset('images/systems/reference-not-found.png') }}" width="60" class="mb-3 opacity-20" alt="">
                                        <p class="text-muted">Маълумот топилмади</p>
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobile version start --}}
                    <div class="d-md-none p-3">
                        <div class="search-box-mobile mb-4">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0" style="border-radius: 12px 0 0 12px;"><i class="fa fa-search text-muted"></i></span>
                                <input type="text" name="filters[title]" value="{{ request('filters.title') }}" class="form-control border-start-0 ps-0" placeholder="     Қидирув..." style="border-radius: 0 12px 12px 0; height: 48px;">
                                <button type="submit" class="btn btn-primary ms-2" style="border-radius: 12px; width: 48px;"><i class="fa fa-arrow-right"></i></button>
                            </div>
                        </div>

                        @forelse($categories as $category)
                            <div class="mobile-card shadow-sm">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="d-flex align-items-center">
                                        <div class="img-wrapper me-3" style="width: 55px; height: 55px;">
                                            @if(optional($category->file)->path)
                                                <img src="{{ asset('storage/' . $category->file->path) }}" alt="">
                                            @else
                                                <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-light"><i class="bi bi-image"></i></div>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="fw-bold mb-0 text-dark">{{ $category->title }}</div>
                                            <span class="text-muted small">ID: {{ $category->id }}</span>
                                        </div>
                                    </div>
                                    <span class="badge-custom {{ \App\Services\StatusService::getTypeClass()[$category->type] }}">{{ \App\Services\StatusService::getType()[$category->type] }}</span>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <small class="text-muted d-block text-uppercase" style="font-size: 0.65rem;">Ота қатегория</small>
                                        <span class="small fw-medium">{{ $category->parent->title ?? '—' }}</span>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block text-uppercase" style="font-size: 0.65rem;">Яратилди</small>
                                        <span class="small fw-medium">{{ $category->created_at?->format('d.m.Y H:i') }}</span>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                    <span class="small text-muted"><i class="bi bi-person me-1"></i>{{ $category->user->username }}</span>
                                    <div class="action-btns">
                                        <x-backend.action route="category" :id="$category->id" :view="true" :edit="true"/>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="py-5 text-center">
                                <img src="{{ asset('images/systems/reference-not-found.png') }}" width="45"
                                     class="mb-3 opacity-20" alt="">
                                <div class="py-4">Маълумот топилмади</div>
                            </div>
                        @endforelse
                    </div>
                    {{-- Mobile version end --}}
                </form>
            </div>

            <div class="card-footer bg-white border-top-0 p-4">
                <div class="d-flex justify-content-center">
                    {{ $categories->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12 mb-3">
                <div class="card-stats count">
                    <div class="w-100">
                        <p>Қатегориялар</p>
                        <h5><strong>{{ number_format($categoryCount, 0, '', ' ') }} та</strong></h5>
                    </div>
                    <div>
                        <i class="bi bi-wallet2"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('categoryFilterForm').addEventListener('submit', function (e) {
            // Faqat ko‘rinib turgan inputni qoldiramiz
            this.querySelectorAll('input[name="filters[title]"]').forEach(select => {
                if (select.offsetParent === null) {
                    select.disabled = true;
                }
            });

            // Bo‘sh input/selectlarni olib tashlaymiz
            this.querySelectorAll('input[name^="filters"], select[name^="filters"]').forEach(input => {
                if (!input.value || !input.value.trim()) {
                    input.removeAttribute('name'); // name olib tashlanadi
                }
            });
        });
    </script>

</x-backend.layouts.main>
