<x-backend.layouts.main title="{{ 'Смена махсулотлари' }}">

    <div class="row">
        <div class="card shadow w-100">
            <div class="table-responsive card-body">
                <form id="shiftOutputFilterForm" method="GET" action="{{ route('shift-output.index') }}">
                    <div class="table-responsive d-none d-md-block">
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th class="col-id">{!! sortLink('id', 'ID') !!}</th>
                                <th>{!! sortLink('organization_title', 'Филиал') !!}</th>
                                <th>{!! sortLink('section_title', 'Бўлим') !!}</th>
                                <th>{!! sortLink('shift_id', 'Смена') !!}</th>
                                <th>{!! sortLink('stage_id', 'Махсулот') !!}</th>
                                <th>{!! sortLink('stage_count', 'Микдори') !!}</th>
                                <th>{!! sortLink('defect_amount', 'Брак') !!}</th>
                                <th class="col-date">{!! sortLink('created_at', 'Яратилди') !!}</th>
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
                                    <select name="filters[shift_id]" class="form-control form-control-sm filter-select2 w-100">
                                        <option value="">Барчаси</option>
                                        @foreach($shifts as $id => $title)
                                            <option value="{{ $id }}" {{ request('filters.shift_id') == $id ? 'selected' : '' }}>
                                                {{ $title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </th>
                                <th>
{{--                                    <select name="filters[stage_id_desktop]" class="form-control form-control-sm filter-select2 w-100">--}}
                                    <select name="filters[stage_id]" class="form-control form-control-sm filter-select2 w-100">
                                        <option value="">Барчаси</option>
                                        @foreach($stages as $id => $title)
                                            <option value="{{ $id }}" {{ request('filters.stage_id') == $id ? 'selected' : '' }}>
                                                {{ $title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </th>
                                <th><input type="text" name="filters[stage_count]" value="{{ request('filters.stage_count') }}"
                                           class="form-control form-control-sm w-100 filter-numeric"></th>
                                <th><input type="text" name="filters[defect_amount]" value="{{ request('filters.defect_amount') }}"
                                           class="form-control form-control-sm w-100 filter-numeric-decimal"></th>
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
                            @forelse($shiftOutputs as $shiftOutput)
                                <tr class="text-center" id="row-{{ $shiftOutput->id }}">
                                    <td class="col-id">{{ $shiftOutput->id }}</td>
                                    <td >{{ optional($shiftOutput->shift->section->organization)->title }}</td>
                                    <td>{{ optional($shiftOutput->shift->section)->title }}</td>
                                    <td>{{ optional($shiftOutput->shift)->title }}</td>
                                    <td>{{ optional($shiftOutput->stage)->title }}</td>
                                    <td class="text-success fw-bold">{{ $shiftOutput->stage_count }} </td>
                                    <td class="text-danger fw-bold">{{ $shiftOutput->defect_amount }} кг</td>
                                    <td>{{ $shiftOutput->created_at?->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <x-backend.action
                                            route="shift-output" listRoute="shift-output-worker" :id="$shiftOutput->id"
                                            :list="true" :view="true" :edit="true" :delete="true"
                                            subRoute="workers"
                                            listTitle="Смена ходимлари кўриш"
                                            viewClass="btn btn-secondary btn-sm"
                                        />
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="text-center">Маълумот топилмади</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobile version start --}}
                    <div class="d-md-none">
                        <div class="d-flex mb-2">
{{--                            <select name="filters[stage_id_mobile]" class="form-control form-control-sm filter-select2 w-100" data-placeholder="Смена махсулотини танланг">--}}
                            <select name="filters[stage_id]" class="form-control form-control-sm filter-select2 w-100" data-placeholder="Смена махсулотини танланг">
                                <option value="">Барчаси</option>
                                @foreach($stages as $id => $title)
                                    <option value="{{ $id }}" {{ request('filters.stage_id') == $id ? 'selected' : '' }}>
                                        {{ $title }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-sm btn-outline-info" title="Қидириш">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                        @forelse($shiftOutputs as $shiftOutput)
                            <div class="card border">
                                <div class="card-body">
                                    <p class="card-text">
                                        <strong>{!! sortLink('id', 'ID:') !!}</strong>{{ $shiftOutput->id }} </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('organization_title', 'Филиал:') !!}</strong>{{ optional($shiftOutput->shift->section->organization)->title }} </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('section_title', 'Бўлим:') !!}</strong>{{ optional($shiftOutput->shift->section)->title }} </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('shift_id', 'Смена:') !!}</strong>{{ optional($shiftOutput->shift)->title }} </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('stage_id', 'Махсулот:') !!}</strong>{{ optional($shiftOutput->stage)->title }} </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('stage_count', 'Микдори:') !!}</strong><span class="text-success fw-bold">{{ number_format($shiftOutput->stage_count, 0, '', ' ') }} та</span></p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('defect_amount', 'Брак:') !!}</strong><span class="text-danger fw-bold">{{ $shiftOutput->defect_amount }} кг</span></p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('created_at_exact', 'Яратилди:') !!}</strong> {{ $shiftOutput->created_at?->format('Y-m-d H:i') }}</p>
                                    <x-backend.action
                                        route="shift-output" listRoute="shift-output-worker" :id="$shiftOutput->id"
                                        :list="true" :view="true" :edit="true" :delete="true"
                                        subRoute="workers"
                                        listTitle="Смена ходимлари кўриш"
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
                <div class="d-flex mt-3 justify-content-center">
                    {{ $shiftOutputs->links('pagination::bootstrap-4') }}
                </div>

                <h3 class="text-center mt-3 mb-3">Махсулотлар</h3>
                @foreach($productStatistics as $statistics)
                    <div class="row text-center">
                        <div class="col-md-3 alert alert-info">
                            <div class="h-100 d-flex justify-content-center align-items-center">
                                <span class="fw-bold">{{ $statistics['title'] }}</span>
                            </div>
                        </div>
                        <div class="col-md-3 alert alert-warning">
                            <div class="fw-bold">Кунлик</div>
                            Маҳсулот: <strong class="h4">{{ number_format($statistics['daily_product'], 0, '', ' ') }}</strong> та<br>
                            Брак: <strong class="h4">{{ number_format($statistics['daily_defect'], 3, '.', ' ') }}</strong> кг
                        </div>
                        <div class="col-md-3 alert alert-success">
                            <div class="fw-bold">Ойлик</div>
                            Маҳсулот: <strong class="h4">{{ number_format($statistics['monthly_product'], 0, '', ' ') }}</strong> та<br>
                            Брак: <strong class="h4">{{ number_format($statistics['monthly_defect'], 3, '.', ' ') }}</strong> кг
                        </div>
                        <div class="col-md-3 alert alert-secondary">
                            <div class="fw-bold">Йиллик</div>
                            Маҳсулот: <strong class="h4">{{ number_format($statistics['yearly_product'], 0, '', ' ') }}</strong> та<br>
                            Брак: <strong class="h4">{{ number_format($statistics['yearly_defect'], 3, '.', ' ') }}</strong> кг
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

{{--    <script>--}}
{{--        document.getElementById('shiftOutputFilterForm').addEventListener('submit', function () {--}}
{{--            let isMobile = window.innerWidth < 768;--}}

{{--            let desktopStage = this.querySelector('select[name="filters[stage_id_desktop]"]');--}}
{{--            let mobileStage  = this.querySelector('select[name="filters[stage_id_mobile]"]');--}}

{{--            // Mobile bo‘lsa → desktopni o‘chirib tashlaymiz--}}
{{--            if (isMobile) {--}}
{{--                if (desktopStage) desktopStage.removeAttribute('name');--}}
{{--                if (mobileStage) mobileStage.setAttribute('name', 'filters[stage_id]');--}}
{{--            }--}}
{{--            // Desktop bo‘lsa → mobileni o‘chirib tashlaymiz--}}
{{--            else {--}}
{{--                if (mobileStage) mobileStage.removeAttribute('name');--}}
{{--                if (desktopStage) desktopStage.setAttribute('name', 'filters[stage_id]');--}}
{{--            }--}}

{{--            // Bo‘sh input/selectlarni olib tashlaymiz--}}
{{--            this.querySelectorAll('input[name^="filters"], select[name^="filters"]').forEach(input => {--}}
{{--                if (!input.value || !input.value.trim()) {--}}
{{--                    input.removeAttribute('name'); // name olib tashlanadi--}}
{{--                }--}}
{{--            });--}}
{{--        });--}}
{{--    </script>--}}

<script>
    document.getElementById('shiftOutputFilterForm').addEventListener('submit', function () {
        // Bo‘sh input/selectlarni olib tashlaymiz
        this.querySelectorAll('input[name="filters[stage_id]"], select[name="filters[stage_id]"]').forEach(select => {
            if (select.offsetParent === null) {
                select.disabled = true;
            }
        });

        // Bo‘sh input/selectlarni name’siz qilamiz
        this.querySelectorAll('input[name^="filters"], select[name^="filters"]').forEach(input => {
            if (!input.value || !input.value.trim()) {
                input.removeAttribute('name'); // name olib tashlanadi
            }
        });
    });
</script>

</x-backend.layouts.main>
