<x-backend.layouts.main title="{!! 'Смена ( ' . $shift->title . ' ) маҳсулотлари:' !!}">

    <div class="row">
        <div class="card shadow w-100">
            <div class="card-header">
                <div class="row justify-content-start">
                    <div class="col-sm-12 col-md-auto text-start">
                        <x-backend.action :back="true"/>
                    </div>
                </div>
            </div>
            <div class="table-responsive card-body">
                <form id="shiftOutputListFilterForm" method="GET" action="{{ route('shift-output.list', $shift->id) }}">
                    <div class="table-responsive d-none d-md-block">
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr class="text-center">
                                <th class="col-id">{!! sortLink('id', 'ID') !!}</th>
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
                                    <select name="filters[stage_id_desktop]" class="form-control form-control-sm filter-select2 w-100">
                                        <option value="">Барчаси</option>
                                        @foreach($stages as $id => $title)
                                            <option value="{{ $id }}" {{ request('filters.stage_id') == $id ? 'selected' : '' }}>
                                                {{ $title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </th>
                                <th><input type="text" name="filters[stage_count]" value="{{ request('filters.stage_count') }}"
                                           class="form-control form-control-sm w-100"></th>
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
                            @forelse($outputs as $output)
                                <tr class="text-center" id="row-{{ $output->id }}">
                                    <td class="col-id">{{ $output->id }}</td>
                                    <td class="col-title">{{ optional($output->stage)->title }}</td>
                                    <td class="col-title text-success fw-bold">{{ number_format($output->stage_count, 0, '', ' ') }} </td>
                                    <td class="col-title text-danger fw-bold">{{ $output->defect_amount }} кг</td>
                                    <td class="col-date">{{ $output->created_at?->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <x-backend.action
                                            route="shift-output" listRoute="shift-output-worker" :id="$output->id"
                                            :list="true" :view="true" :edit="true" :delete="true"
                                            subRoute="workers"
                                            listTitle="Смена ходимлари кўриш"
                                            viewClass="btn btn-secondary btn-sm"
                                        />
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">Маълумот топилмади</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobile version start --}}
                    <div class="d-md-none">
                        <div class="d-flex mb-2">
                            <select name="filters[stage_id_mobile]" class="form-control form-control-sm filter-select2 w-100" data-placeholder="Смена махсулотини танланг">
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
                        @forelse($outputs as $output)
                            <div class="card border">
                                <div class="card-body">
                                    <p class="card-text">
                                        <strong>{!! sortLink('id', 'ID:') !!}</strong>{{ $output->id }} </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('stage_id', 'Махсулот:') !!}</strong>{{ optional($output->stage)->title }} </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('stage_count', 'Микдори:') !!}</strong><span class="text-success fw-bold">{{ number_format($output->stage_count, 0, '', ' ') }} та</span></p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('defect_amount', 'Брак:') !!}</strong><span class="text-danger fw-bold">{{ $output->defect_amount }} кг</span></p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('created_at_exact', 'Яратилди:') !!}</strong> {{ $output->created_at?->format('Y-m-d H:i') }}</p>
                                    <x-backend.action
                                        route="shift-output" listRoute="shift-output-worker" :id="$output->id"
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

                <div class="d-flex mt-3 justify-content-center">
                    {{ $outputs->links('pagination::bootstrap-4') }}
                </div>

                <h3 class="text-center mt-3 mb-3">Махсулотлар</h3>
                @foreach($productStatistics as $statistics)
                    <div class="row text-center">
                        <div class="col-md-3 alert alert-info">
                            <div class="h-100 d-flex justify-content-center align-items-center">
                                <span class="fw-bold">{{ $statistics['title'] . ' (' . $statistics['section_title'] . ')' }}</span>
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

    <script>
        // Index dagi bilan ikkalasi bitta vazifa bajaradi
        document.getElementById('shiftOutputListFilterForm').addEventListener('submit', function () {
            let isMobile = window.innerWidth < 768;

            let desktopStage = this.querySelector('select[name="filters[stage_id_desktop]"]');
            let mobileStage  = this.querySelector('select[name="filters[stage_id_mobile]"]');

            // Avval ikkalasidan ham "filters[stage_id]" ni olib tashlaymiz
            if (desktopStage) desktopStage.removeAttribute('name');
            if (mobileStage)  mobileStage.removeAttribute('name');

            // So‘ngra ekranga mosini "filters[stage_id]" qilib qo‘yamiz
            if (isMobile && mobileStage) {
                mobileStage.setAttribute('name', 'filters[stage_id]');
            } else if (!isMobile && desktopStage) {
                desktopStage.setAttribute('name', 'filters[stage_id]');
            }

            // Bo‘sh input/selectlarni name’siz qilamiz
            this.querySelectorAll('input[name^="filters"], select[name^="filters"]').forEach(input => {
                if (!input.value || !input.value.trim()) {
                    input.removeAttribute('name'); // <-- remove emas, name olib tashlanadi
                }
            });
        });
    </script>

</x-backend.layouts.main>
