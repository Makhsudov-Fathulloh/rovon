<x-backend.layouts.main title="{{ 'Бўлимлар' }}">

    <div class="row">
        <div class="card shadow w-100">
            <div class="card-header">
                <div class="row justify-content-start">
                    <div class="col-sm-12 col-md-auto text-start">
                        <a href="{{ route('section.create') }}" class="btn btn-primary w-100 w-md-auto">
                            {{ 'Яратиш' }}
                        </a>
                    </div>
                </div>
            </div>
            <div class="table-responsive card-body">
                <form id="sectionFilterForm" method="GET" action="{{ route('section.index') }}">
                    <div class="table-responsive d-none d-md-block">
                        <table class="table table-bordered table-hover">
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
                                           class="form-control form-control-sm w-100 filter-numeric"></th>
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
                                    <select name="filters[title]" class="form-control form-control-sm filter-select2 w-100">
                                        <option value="">Барчаси</option>
                                        @foreach($titles as $id => $title)
                                            <option value="{{ $title }}" {{ request('filters.title') == $title ? 'selected' : '' }}>
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

                                <th class="text-center">
                                    <button type="submit" class="btn btn-sm btn-primary w-50" title="Қидириш"><i
                                            class="fa fa-search"></i></button>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($sections as $section)
                                <tr class="text-center" id="row-{{ $section->id }}">
                                    <td class="col-id">{{ $section->id }}</td>
                                    <td class="col-title">{{ optional($section->organization)->title }}</td>
                                    <td class="col-title">{{ $section->title }}</td>
                                    <td class="col-date">{{ $section->created_at?->format('Y-m-d H:i') }}</td>
                                    <td class="col-title">
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
                    <div class="d-md-none">
                        <div class="d-flex mb-2">
                            <select name="filters[title]" class="form-control form-control-sm filter-select2 w-100" data-placeholder="Бўлимни танланг">
                                <option value="">Барчаси</option>
                                @foreach($titles as $id => $title)
                                    <option value="{{ $title }}" {{ request('filters.title') == $title ? 'selected' : '' }}>
                                        {{ $title }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-sm btn-outline-info" title="Қидириш">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                        @forelse($sections as $section)
                            <div class="card border">
                                <div class="card-body">
                                    <p class="card-text">
                                        <strong>{!! sortLink('id', 'ID:') !!}</strong>{{ $section->id }} </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('organization_title', 'Филиал:') !!}</strong>{{ optional($section->organization)->title }} </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('title', 'Бўлим:') !!}</strong>{{ $section->title }} </p>
                                     <x-backend.action
                                            route="section" listRoute="shift" :id="$section->id"
                                            :list="true" :view="true" :edit="true"
                                            listTitle="Сменаларни кўриш"
                                            viewClass="btn btn-secondary btn-sm"
                                        />
                                </div>
                            </div>
                        @empty
                            <p class="text-center">Маълумот топилмади</p>
                        @endforelse
                    </div>
                    {{-- Mobile version end --}}

                </form>

                {{-- Pagination --}}
                <div class="d-flex justify-content-center">
                    {{ $sections->links('pagination::bootstrap-4') }}
                </div>

                <style>
                    .card-stats {
                        border-radius: 12px;
                        padding: 20px;
                        color: #fff;
                        transition: 0.3s ease;
                        text-align: center;
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                    }
                    .card-stats:hover {
                        transform: translateY(-5px);
                        box-shadow: 0 12px 24px rgba(0,0,0,0.3);
                    }
                    .card-stats.count {
                        background: linear-gradient(135deg, #00b894 35%, #2ecc71 65%);
                        border-left: 5px solid #00d68f;
                    }

                    .card-stats h5 {
                        font-weight: 700;
                        margin-bottom: 8px;
                        font-size: 1.25rem;
                    }
                    .card-stats p {
                        margin: 2px 0;
                        font-size: 0.95rem;
                    }
                    .card-stats i {
                        font-size: 2.2rem;
                        opacity: 0.7;
                    }
                </style>
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
                        box-shadow: 0 12px 24px rgba(0,0,0,0.2);
                    }

                    .kanban-card {
                        background:#fff;
                        padding:6px;
                        margin-bottom:6px;
                        border-radius:4px;
                        box-shadow:0 1px 3px rgba(0,0,0,.1);
                        color: #000;
                        transition: 0.3s ease;
                    }
                    .kanban-card:hover {
                        transform: translateY(-3px);
                        box-shadow: 0 8px 16px rgba(0,0,0,0.15);
                    }

                    .badge {
                        float:right;
                        background:#0984e3;
                        color:#fff;
                        font-weight: bold;
                        padding:2px 6px;
                    }

                    .section-color-1 { background: linear-gradient(135deg,#00b894 30%,#2ecc71 90%); }
                    .section-color-2 { background: linear-gradient(135deg,#0984e3 30%,#0984e3 90%); }
                    .section-color-3 { background: linear-gradient(135deg,#6c5ce7 30%,#5a4fd4 90%); }
                    .section-color-4 { background: linear-gradient(135deg,#fd79a8 30%,#e84393 90%); }
                    .section-color-5 { background: linear-gradient(135deg,#fdcb6e 30%,#f6b93b 90%); }
                    .section-color-6 { background: linear-gradient(135deg,#e17055 30%,#d35400 90%); }
                    .section-color-7 { background: linear-gradient(135deg,#00cec9 30%,#00bcd4 90%); }
                    .section-color-8 { background: linear-gradient(135deg,#636e72 30%,#2d3436 90%); }
                    .section-color-9 { background: linear-gradient(135deg,#fab1a0 30%,#e17055 90%); }
                    .section-color-10{ background: linear-gradient(135deg,#81ecec 30%,#00cec9 90%); }

                    /* Card-stats hover efekti (avvalgi sizniki) */
                    .card-stats {
                        border-radius: 12px;
                        padding: 20px;
                        color: #fff;
                        transition: 0.3s ease;
                        text-align: center;
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                    }
                    .card-stats:hover {
                        transform: translateY(-5px);
                        box-shadow: 0 12px 24px rgba(0,0,0,0.3);
                    }
                    .card-stats.count {
                        background: linear-gradient(135deg, #00b894 35%, #2ecc71 65%);
                        border-left: 5px solid #00d68f;
                    }

                    .card-stats h5 {
                        font-weight: 700;
                        margin-bottom: 8px;
                        font-size: 1.25rem;
                    }
                    .card-stats p {
                        margin: 2px 0;
                        font-size: 0.95rem;
                    }
                    .card-stats i {
                        font-size: 2.2rem;
                        opacity: 0.7;
                    }
                </style>
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
        </div>
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


