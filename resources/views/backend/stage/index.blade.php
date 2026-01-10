<x-backend.layouts.main title="{{ 'Бўлим махсулотлари' }}">

    <div class="row">
        <div class="card shadow w-100">
            <div class="card-header">
                <div class="row justify-content-start">
                    <div class="col-sm-12 col-md-auto text-start">
                        <a href="{{ route('stage.create') }}" class="btn btn-primary w-100 w-md-auto">
                            {{ 'Яратиш' }}
                        </a>
                    </div>
                </div>
            </div>
            <div class="table-responsive card-body">
                <form id="stageFilterForm" method="GET" action="{{ route('stage.index') }}">
                    <div class="table-responsive d-none d-md-block">
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th class="col-id">{!! sortLink('id', 'ID') !!}</th>
                                <th>{!! sortLink('organization_title', 'Филиал') !!}</th>
                                <th>{!! sortLink('section_id', 'Бўлим') !!}</th>
                                <th>{!! sortLink('title', 'Номи') !!}</th>
                                <th style="color: #2962ff">Таркиби</th>
                                <th>{!! sortLink('price', 'Нархи') !!}</th>
                                <th>{!! sortLink('status', 'Статус') !!}</th>
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
                                    <select name="filters[section_id]"
                                            class="form-control form-control-sm filter-select2 w-100">
                                        <option value="">Барчаси</option>
                                        @foreach($sections as $id => $title)
                                            <option
                                                value="{{ $id }}" {{ request('filters.section_id') == $id ? 'selected' : '' }}>
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
                                <th><input type="text" name="filters[stageMaterials]"
                                           value="{{ request('filters.stageMaterials') }}"
                                           class="form-control form-control-sm w-100" style="display: none;"></th>
                                <th><input type="text" name="filters[price]" value="{{ request('filters.price') }}"
                                           class="form-control form-control-sm w-100 filter-numeric"></th>
                                <th>
                                    <select name="filters[status]" class="form-control form-control-sm w-100">
                                        <option value="">Барчаси</option>
                                        @foreach(\App\Services\StatusService::getList() as $key => $label)
                                            <option
                                                value="{{ $key }}" {{ (string) request('filters.status') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
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

                                <th>
                                    <button type="submit" class="btn btn-sm btn-primary w-100" title="Қидириш"><i
                                            class="fa fa-search"></i></button>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($stages as $stage)
                                <tr class="text-center" id="row-{{ $stage->id }}">
                                    <td class="col-id">{{ $stage->id }}</td>
                                    <td>{{ optional($stage->section->organization)->title }}</td>
                                    <td>{{ optional($stage->section)->title }}</td>
                                    <td class="col-title">{{ $stage->title }}</td>
                                    <td>
                                        @foreach($stage->stageMaterials as $material)
                                            <span class="badge bg-info">{{ $material->rawMaterialVariation->code . ' (' . $material->rawMaterialVariation->title . ')' }}</span>
                                        @endforeach
                                    </td>
                                    <td class="col-title text-success fw-bold">{{ number_format($stage->price, 0, '', ' ') }} </td>
                                    <td style="width: 100px">{{ \App\Services\StatusService::getList()[$stage->status] ?? '-' }}</td>
                                    <td class="col-date">{{ $stage->created_at?->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <x-backend.action
                                            route="stage" :id="$stage->id"
                                            subRoute="outputs"
                                            :view="true" :edit="true" :delete="true"
                                            createTitle="Бўлим махсулотини яратиш"
                                        />
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="14" class="text-center">Маълумот топилмади</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobile version start --}}
                    <div class="d-md-none">
                        <div class="d-flex mb-2">
                            <select name="filters[title]" class="form-control form-control-sm filter-select2 w-100"
                                    data-placeholder="Бўлим махсулоти номини киритинг">
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
                        @forelse($stages as $stage)
                            <div class="card border">
                                <div class="card-body">
                                    <p class="card-text">
                                        <strong>{!! sortLink('id', 'ID') !!}</strong>{{ $stage->id }} </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('organization_title', 'Филиал:') !!}</strong>{{ optional($stage->section->organization)->title }}
                                    </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('section_id', 'Бўлим:') !!}</strong>{{ optional($stage->section)->title }}
                                    </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('title', 'Номи:') !!}</strong>{{ $stage->title }} </p>
                                    @if($stage->stageMaterials->isNotEmpty())                                      <p class="card-text">
                                          <strong style="color: #2962ff">Таркиби:</strong>
                                          @foreach($stage->stageMaterials as $material)
                                              <span class="badge bg-info">{{ $material->rawMaterialVariation->code . ' (' . $material->rawMaterialVariation->title . ')' }}</span>
                                          @endforeach
                                      </p>
                                    @endif
                                    <p class="card-text">
                                        <strong>{!! sortLink('price', 'Нархи:') !!}</strong><span
                                            class="text-success fw-bold">{{ number_format($stage->price, 0, '', ' ') }} сўм</span>
                                    </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('status', 'Статус:')  !!} </strong> {{ \App\Services\StatusService::getList()[$stage->status] ?? '-' }}
                                    </p>
                                    <x-backend.action
                                        route="stage" :id="$stage->id"
                                        :view="true" :edit="true" :delete="true"
                                        subRoute="outputs"
                                        createTitle="Бўлим махсулотини яратиш"
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
                    {{ $stages->links('pagination::bootstrap-4') }}
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
                                <p>Бўлим махсулотлари</p>
                                <h5><strong>{{ number_format($stageCount, 0, '', ' ') }} та</strong></h5>
                            </div>
                            <div>
                                <i class="bi bi-wallet2"></i>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        document.getElementById('stageFilterForm').addEventListener('submit', function (e) {
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
