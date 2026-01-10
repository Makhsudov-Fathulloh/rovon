<x-backend.layouts.main title="{{ 'Смена ходимлари' }}">

    <div class="row">
        <div class="card shadow w-100">
            <div class="card-header">
                <div class="row justify-content-start">
                    <div class="col-sm-12 col-md-auto text-start">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="table-responsive card-body">
                <form id="shiftOutputWorkerFilterForm" method="GET" action="{{ route('shift-output-worker.index') }}">
                    <div class="table-responsive d-none d-md-block">
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th class="col-id">{!! sortLink('id', 'ID') !!}</th>
                                <th>{!! sortLink('user_id', 'Ходим') !!}</th>
                                <th>{!! sortLink('shift_output_id', 'Смена') !!}</th>
                                <th>{!! sortLink('stage_id', 'Махсулот') !!}</th>
                                <th>{!! sortLink('stage_count', 'Микдори') !!}</th>
                                <th>{!! sortLink('defect_amount', 'Брак') !!}</th>
                                <th>{!! sortLink('price', 'Нархи') !!}</th>
                                <th class="col-date">{!! sortLink('created_at', 'Яратилди') !!}</th>
                            </tr>
                            {{-- Filter Inputs --}}
                            <tr>
                                <th><input type="text" name="filters[id]" value="{{ request('filters.id') }}"
                                           class="form-control form-control-sm w-100 filter-numeric"></th>
                                <th>
                                    <select name="filters[user_id]"
                                            class="form-control form-control-sm filter-select2 w-100">
                                        <option value="">Барчаси</option>
                                        @foreach($users as $id => $username)
                                            <option
                                                value="{{ $id }}" {{ request('filters.user_id') == $id ? 'selected' : '' }}>
                                                {{ $username }}
                                            </option>
                                        @endforeach
                                    </select>
                                </th>
                                <th>
                                    <select name="filters[shift_id]"
                                            class="form-control form-control-sm filter-select2 w-100">
                                        <option value="">Барчаси</option>
                                        @foreach($shifts as $id => $title)
                                            <option
                                                value="{{ $id }}" {{ request('filters.shift_id') == $id ? 'selected' : '' }}>
                                                {{ $title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </th>
                                <th>
                                    <select name="filters[stage_id]"
                                            class="form-control form-control-sm filter-select2 w-100">
                                        <option value="">Барчаси</option>
                                        @foreach($stages as $id => $title)
                                            <option
                                                value="{{ $id }}" {{ request('filters.stage_id') == $id ? 'selected' : '' }}>
                                                {{ $title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </th>
                                <th><input type="text" name="filters[stage_count]" value="{{ request('filters.stage_count') }}"
                                           class="form-control form-control-sm w-100"></th>
                                <th><input type="text" name="filters[defect_amount]" value="{{ request('filters.defect_amount') }}"
                                           class="form-control form-control-sm w-100 filter-numeric-decimal"></th>
                                <th><input type="text" name="filters[price]" value="{{ request('filters.price') }}"
                                           class="form-control form-control-sm w-100 filter-numeric"></th>
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
                            @forelse($shiftOutputWorkers as $shiftOutputWorker)
                                <tr class="text-center" id="row-{{ $shiftOutputWorker->id }}">
                                    <td class="col-id">{{ $shiftOutputWorker->id }}</td>
                                    <td class="col-title">{{ $shiftOutputWorker->user->username }}</td>
                                    <td>{{ optional($shiftOutputWorker->shiftOutput->shift)->title }}</td>
                                    <td>{{ optional($shiftOutputWorker->shiftOutput->stage)->title }}</td>
                                    <td class="text-success fw-bold">{{ number_format( $shiftOutputWorker->stage_count , 0, '', ' ') }}</td>
                                    <td class="text-danger fw-bold">{{ $shiftOutputWorker->defect_amount }} кг</td>
                                    <td class="text-info fw-bold">{{ number_format( $shiftOutputWorker->price , 0, '', ' ') }}</td>
                                    <td class="col-date">{{ $shiftOutputWorker->created_at?->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <x-backend.action
                                            route="shift-output-worker" :id="$shiftOutputWorker->id" :view="true"
                                        />
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">Маълумот топилмади</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobile version start --}}
                    <div class="d-md-none">
                        <div class="d-flex mb-2">
                            <select name="filters[user_id]"
                                    class="form-control form-control-sm filter-select2 w-100" data-placeholder="Ходимни киритинг">
                                <option value="">Барчаси</option>
                                @foreach($users as $id => $username)
                                    <option
                                        value="{{ $id }}" {{ request('filters.user_id') == $id ? 'selected' : '' }}>
                                        {{ $username }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-sm btn-outline-info" title="Қидириш">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                        @forelse($shiftOutputWorkers as $shiftOutputWorker)
                            <div class="card border">
                                <div class="card-body">
                                    <p>
                                        <strong>{!! sortLink('id', 'ID:') !!}</strong>{{ $shiftOutputWorker->id }} </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('user_id', 'Ходим') !!}</strong>{{ optional($shiftOutputWorker->user)->username }} </p>
                                    <p>
                                        <strong>{!! sortLink('shift_output_id', 'Смена:') !!}</strong>{{ optional($shiftOutputWorker->shiftOutput->shift)->title }} </p>
                                    <p>
                                        <strong>{!! sortLink('stage_id', 'Махсулот:') !!}</strong>{{ optional($shiftOutputWorker->shiftOutput->stage)->title }} </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('stage_count', 'Микдори:') !!}</strong><span class="text-success fw-bold">{{ number_format($shiftOutputWorker->stage_count, 0, '', ' ') }} та</span></p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('defect_amount', 'Брак:') !!}</strong><span class="text-danger fw-bold">{{ $shiftOutputWorker->defect_amount }} кг</span></p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('price', 'Нархи:') !!}</strong><span class="text-info fw-bold">{{ number_format( $shiftOutputWorker->price , 0, '', ' ') }} сўм</span></p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('created_at_exact', 'Яратилди:') !!}</strong> {{ $shiftOutputWorker->created_at?->format('Y-m-d H:i') }}</p>
                                    <x-backend.action
                                        route="shift-output-worker" :id="$shiftOutputWorker->id" :view="true"
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
                    {{ $shiftOutputWorkers->links('pagination::bootstrap-4') }}
                </div>

                <h3 class="text-center mt-3 mb-3">Махсулотлар</h3>
                @foreach($userStatistics as $statistics)
                    <div class="row text-center">
                        <div class="col-md-3 alert alert-info">
                            <div class="h-100 d-flex justify-content-center align-items-center">
                                <span class="fw-bold">{{ $statistics['username'] }}</span>
                            </div>
                        </div>
                        <div class="col-md-3 alert alert-warning">
                            <div class="fw-bold">Кунлик</div>
                            Маҳсулот: <strong class="h4">{{ number_format($statistics['daily_product'], 0, '', ' ') }}</strong> та<br>
                            Брак: <strong class="h4">{{ number_format($statistics['daily_defect'], 3, '.', ' ') }}</strong> кг<br>
                            Нархи: <strong class="h4">{{ number_format($statistics['daily_price'], 0, '', ' ') }}</strong> та
                        </div>
                        <div class="col-md-3 alert alert-success">
                            <div class="fw-bold">Ойлик</div>
                            Маҳсулот: <strong class="h4">{{ number_format($statistics['monthly_product'], 0, '', ' ') }}</strong> та<br>
                            Брак: <strong class="h4">{{ number_format($statistics['monthly_defect'], 3, '.', ' ') }}</strong> кг<br>
                            Нархи: <strong class="h4">{{ number_format($statistics['monthly_price'], 0, '', ' ') }}</strong> кг
                        </div>
                        <div class="col-md-3 alert alert-secondary">
                            <div class="fw-bold">Йиллик</div>
                            Маҳсулот: <strong class="h4">{{ number_format($statistics['yearly_product'], 0, '', ' ') }}</strong> та<br>
                            Брак: <strong class="h4">{{ number_format($statistics['yearly_defect'], 3, '.', ' ') }}</strong> кг<br>
                            Нархи: <strong class="h4">{{ number_format($statistics['yearly_price'], 0, '', ' ') }}</strong> кг
                        </div>
                    </div>
                @endforeach

            </div>
        </div>
    </div>

    <script>
        document.getElementById('shiftOutputWorkerFilterForm').addEventListener('submit', function (e) {
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
