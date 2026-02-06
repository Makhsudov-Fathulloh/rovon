<x-backend.layouts.main title="{{ 'Бўлимлар' }}">

    <style>
        /* Kanban umumiy stil */
        .kanban-org {
            margin-bottom: 24px;
        }

        .org-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 12px;
            text-align: center;
        }

        .kanban {
            display: flex;
            gap: 12px;
            width: 100%;
        }

        .kanban-col {
            padding: 8px;
            border-radius: 6px;
            color: #fff;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            text-align: center;
            transition: 0.3s ease;
        }

        .kanban-col:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.2);
        }

        .kanban-card {
            background: #fff;
            padding: 6px;
            margin-bottom: 6px;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .1);
            color: #000;
            transition: 0.3s ease;
        }

        .kanban-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
        }

        .badge {
            float: right;
            background: #0984e3;
            color: #fff;
            font-weight: bold;
            padding: 2px 6px;
        }

        .section-color-1 {
            background: linear-gradient(135deg, #00b894 30%, #2ecc71 90%);
        }

        .section-color-2 {
            background: linear-gradient(135deg, #0984e3 30%, #0984e3 90%);
        }

        .section-color-3 {
            background: linear-gradient(135deg, #6c5ce7 30%, #5a4fd4 90%);
        }

        .section-color-4 {
            background: linear-gradient(135deg, #fd79a8 30%, #e84393 90%);
        }

        .section-color-5 {
            background: linear-gradient(135deg, #fdcb6e 30%, #f6b93b 90%);
        }

        .section-color-6 {
            background: linear-gradient(135deg, #e17055 30%, #d35400 90%);
        }

        .section-color-7 {
            background: linear-gradient(135deg, #00cec9 30%, #00bcd4 90%);
        }

        .section-color-8 {
            background: linear-gradient(135deg, #636e72 30%, #2d3436 90%);
        }

        .section-color-9 {
            background: linear-gradient(135deg, #fab1a0 30%, #e17055 90%);
        }

        .section-color-10 {
            background: linear-gradient(135deg, #81ecec 30%, #00cec9 90%);
        }
    </style>

    <div class="container-fluid">

        <div class="card-custom shadow-sm">
            <div class="card-header-custom action-btns">
                <div class="row justify-content-start">
                    <div class="col-sm-12 col-md-auto text-start">
                        <x-backend.action route="section" :back="true" :create="true"/>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <form id="sectionFilterForm" method="GET" action="{{ route('section.index') }}">
                    <div class="table-responsive d-none d-md-block">
                        <table class="table mb-0">
                            <thead>
                            <tr class="text-center">
                                <th class="col-id">{!! sortLink('id', 'ID') !!}</th>
                                <th class="col-title">{!! sortLink('organization_id', 'Филиал') !!}</th>
                                <th class="col-title">{!! sortLink('title', 'Номи') !!}</th>
                                <th class="col-date">{!! sortLink('created_at', 'Яратилди') !!}</th>
                                <th></th> {{-- Search btn --}}
                            </tr>
                            {{-- Filter Inputs --}}
                            <tr>
                                <th><input type="text" name="filters[id]" value="{{ request('filters.id') }}"
                                           class="form-control form-control-sm w-100 filter-numeric" placeholder="№...">
                                </th>
                                <th>
                                    <select name="filters[organization_id]"
                                            class="form-control form-control-sm filter-select2 w-100">
                                        <option value="">Барчаси</option>
                                        @foreach($organizations as $id => $title)
                                            <option
                                                value="{{ $id }}" {{ request('filters.organization_id') == $id ? 'selected' : '' }}>
                                                {{ $title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </th>
                                <th>
                                    <select name="filters[title]"
                                            class="form-control form-control-sm filter-select2 w-100">
                                        <option value="">Барчаси</option>
                                        @foreach($titles as $id => $title)
                                            <option
                                                value="{{ $title }}" {{ request('filters.title') == $title ? 'selected' : '' }}>
                                                {{ $title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </th>
                                <th>
                                    <div class="d-flex">
                                        <input type="date" name="filters[created_from]"
                                               value="{{ request('filters.created_from') }}"
                                               class="form-control form-control-sm me-1" placeholder="From">
                                        <input type="date" name="filters[created_to]"
                                               value="{{ request('filters.created_to') }}"
                                               class="form-control form-control-sm" placeholder="To">
                                    </div>
                                </th>

                                @if(session('date_format_errors'))
                                    <div class="alert alert-danger mt-2">
                                        <ul>
                                            @foreach(session('date_format_errors') as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <th class="p-0">
                                    <div class="d-flex justify-content-center align-items-center"
                                         style="min-height: 75px;">
                                        <button type="submit" class="btn btn-custom-search" title="Филтрлаш">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </div>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($sections as $section)
                                <tr class="text-center" id="row-{{ $section->id }}">
                                    <td class="col-id">{{ $section->id }}</td>
                                    <td style="width: 30%">{{ optional($section->organization)->title }}</td>
                                    <td style="width: 25%">{{ $section->title }}</td>
                                    <td>{{ $section->created_at?->format('Y-m-d H:i') }}</td>
                                    <td class="text-center action-btns">
                                        <x-backend.action
                                            route="section" listRoute="shift" :id="$section->id"
                                            :list="true" :view="true" :edit="true"
                                            listTitle="Сменаларни кўриш"
                                            viewClass="btn btn-secondary btn-sm"
                                        />
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">Маълумот топилмади</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobile version start --}}
                    <div class="d-md-none p-3">
                        <div class="d-flex m-4">
                            <select name="filters[title]" class="form-control form-control-sm filter-select2 w-100"
                                    data-placeholder="Бўлимни танланг">
                                <option value="">Барчаси</option>
                                @foreach($titles as $id => $title)
                                    <option
                                        value="{{ $title }}" {{ request('filters.title') == $title ? 'selected' : '' }}>
                                        {{ $title }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-sm btn-outline-info" title="Қидириш">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                        @forelse($sections as $section)
                            <div class="mobile-card shadow-sm">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="d-flex align-items-center">
                                        <div class="img-wrapper me-3" style="width: 55px; height: 55px;">
                                            @if(optional($section->file)->path)
                                                <img src="{{ asset('storage/' . $section->file->path) }}"
                                                     alt="">
                                            @else
                                                <div
                                                    class="w-100 h-100 d-flex align-items-center justify-content-center bg-light">
                                                    <i class="bi bi-image"></i></div>
                                            @endif
                                        </div>
                                        <div>
                                            <div
                                                class="fw-bold mb-0 text-dark">{{ $section->title }}</div>
                                            <span class="text-muted small">ID: {{ $section->id }}</span>
                                        </div>
                                    </div>
                                    <span class="badge-custom {{ \App\Services\StatusService::getTypeClass()[4] }}">
                                        <div class="fw-bold text-center" style="line-height: 1;">
                                            <div class="text-success"></div>
                                            <div style="height: 2px; background-color: #000; margin: 3px 0;"></div>
                                            <div class="text-danger"></div>
                                        </div>
                                    </span>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <small class="text-muted d-block text-uppercase"
                                               style="font-size: 0.65rem;">Филиал </small>
                                        <span
                                            class="small fw-medium">{{ optional($section->organization)->title }}</span>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block text-uppercase"
                                               style="font-size: 0.65rem;">Яратилди</small>
                                        <span
                                            class="small fw-medium">{{ $section->created_at?->format('d.m.Y H:i') }}</span>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                    <span class="small text-muted"><i class="bi bi-person me-1"></i>
                                        @foreach($section->organization->users as $user)
                                            <span class="badge bg-info">{{ $user->username }}</span>
                                        @endforeach
                                    </span>
                                    <div class="action-btns">
                                        <x-backend.action
                                            route="section" listRoute="shift" :id="$section->id"
                                            :list="true" :view="true" :edit="true"
                                            listTitle="Сменаларни кўриш"
                                            viewClass="btn btn-secondary btn-sm"
                                        />
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
                    {{ $sections->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12 mb-3">
                <div class="card-stats count">
                    <div class="w-100">
                        <p>Бўлимлар</p>
                        <h5><strong>{{ number_format($sectionCount, 0, '', ' ') }} та</strong></h5>
                    </div>
                    <div>
                        <i class="bi bi-wallet2"></i>
                    </div>
                </div>
            </div>
        </div>

        @foreach($orgSection as $organization)
            <div class="kanban-org">
                <div class="org-title">{{ $organization->title }}</div>
                <div class="kanban">
                    @php
                        $sectionCount = $organization->section->count();
                        $flexWidth = $sectionCount > 0 ? (100 / $sectionCount) . '%' : '100%';
                    @endphp

                    @foreach($organization->section as $section)
                        <div class="kanban-col section-color-{{ ($loop->index % 10) + 1 }}"
                             style="flex-basis: {{ $flexWidth }};">

                            <h5>{{ $section->title }}</h5>

                            @foreach($section->stages as $stage)
                                @php
                                    $balance = $stage->balances
                                        ->where('section_id', $section->id)
                                        ->first();
                                @endphp

                                <div class="kanban-card">
                                    {{ $stage->title }}
                                    <span class="badge">
                                                {{ number_format($balance?->balance, 0, '', ' ') ?? 0 }}
                                            </span>
                                </div>
                            @endforeach

                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

    </div>

    <script>
        document.getElementById('sectionFilterForm').addEventListener('submit', function (e) {
            // Faqat ko‘rinib turgan inputni qoldiramiz
            this.querySelectorAll('input[name="filters[title]"], select[name="filters[title]"]').forEach(select => {
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


