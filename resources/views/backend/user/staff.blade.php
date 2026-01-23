@php
    use App\Services\StatusService;
    use App\Services\PhoneFormatService;
    use App\Helpers\PriceHelper;
@endphp

<x-backend.layouts.main title="{{ 'Ҳодимлар' }}">

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
            min-width: 180px; /* minimal kenglik */
            flex: 1 1 200px; /* responsive */
        }
        .card-stats:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(0,0,0,0.3);
        }

        .card-stats.client { background: linear-gradient(135deg, #00b894 30%, #2ecc71 90%); border-left: 5px solid #00d68f; }
        .card-stats.uzs { background: linear-gradient(135deg, #0984e3 30%, #0984e3 90%); border-left: 5px solid #00a8ff; }
        .card-stats.usd { background: linear-gradient(135deg, #6c5ce7 30%, #5a4fd4 90%); border-left: 5px solid #8e76ff; }

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

    <div class="row">
        <div class="card shadow w-100">
            <div class="card-header">
                @can('hasAccess')
                    <div class="row justify-content-start">
                        <div class="col-sm-12 col-md-auto text-start">
                            <x-backend.action route="user" :back="true" :create="true"/>
                        </div>
                    </div>
                @endcan
            </div>
            <div class="table-responsive card-body">
                <form id="staffFilterForm" method="GET" action="{{ route('user.staff') }}">
                    <div class="table-responsive d-none d-md-block">
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr class="text-center">
                                <th class="col-id">{!! sortLink('id', 'Id') !!}</th>
                                {{--<th>{!! sortLink('first_name', 'Исм') !!}</th>--}}
                                {{--<th>{!! sortLink('last_name', 'Фамилия') !!}</th>--}}
                                <th class="col-title">{!! sortLink('username', 'Ҳодим') !!}</th>
                                <th>{!! sortLink('address', 'Адрес') !!}</th>
                                {{--<th>{!! sortLink('email', 'Email') !!}</th>--}}
                                {{--<th>{!! sortLink('email_verified_at', 'Еmail тасдиқланди') !!}</th>--}}
                                <th>{!! sortLink('photo', 'Расм') !!}</th>
                                <th>{!! sortLink('phone', 'Телефон') !!}</th>
                                <th>{!! sortLink('role_id', 'Даража') !!}</th>
                                <th>{!! sortLink('status', 'Статус') !!}</th>
                                <th class="col-debt">{!! sortLink('debt', 'Қарздорлик') !!}</th>
                                <th>{!! sortLink('created_at', 'Яратилди') !!}</th>
                                <th></th> {{-- Search btn --}}
                            </tr>
                            {{-- Filter Inputs --}}
                            <tr>
                                <th><input type="text" name="filters[id]" value="{{ request('filters.id') }}"
                                           class="form-control form-control-sm w-100 filter-numeric"></th>
                                {{--<th><input type="text" name="filters[first_name]" value="{{ request('filters.first_name') }}" class="form-control form-control-sm w-100"></th>--}}
                                {{--<th><input type="text" name="filters[last_name]" value="{{ request('filters.last_name') }}" class="form-control form-control-sm w-100"></th>--}}
                                <th>
                                    <select name="filters[username]"
                                            class="form-control form-control-sm filter-select2 w-100">
                                        <option value="">Барчаси</option>
                                        @foreach($staffs as $id => $username)
                                            <option
                                                value="{{ $username }}" {{ request('filters.username') == $username ? 'selected' : '' }}>
                                                {{ $username }}
                                            </option>
                                        @endforeach
                                    </select>
                                </th>
                                <th><input type="text" name="filters[address]" value="{{ request('filters.address') }}"
                                           class="form-control form-control-sm w-100"></th>
                                {{--<th><input type="text" name="filters[email]" value="{{ request('filters.email') }}" class="form-control form-control-sm w-100"></th>--}}
                                {{--<th><input type="text" name="filters[email_verified_at]" value="{{ request('filters.email_verified_at') }}" class="form-control form-control-sm w-100"></th>--}}
                                <th><input type="text" name="filters[photo]" value="{{ request('filters.photo') }}"
                                           class="form-control form-control-sm w-100" style="display: none;"></th>
                                <th><input id="phone" type="text" name="filters[phone]"
                                           value="{{ request('filters.phone') }}"
                                           class="form-control form-control-sm w-100"></th>
                                <th>
                                    <select name="filters[role_id]" class="form-control form-control-sm me-2 w-100">
                                        <option value="">Барчаси</option>
                                        @foreach($roles as $id => $title)
                                            <option
                                                value="{{ $id }}" {{ request('filters.role_id') == $id ? 'selected' : '' }}>
                                                {{ $title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </th>
                                <th>
                                    <select name="filters[status]" class="form-control form-control-sm w-100">
                                        <option value="">Барчаси</option>
                                        @foreach(StatusService::getList() as $key => $label)
                                            <option
                                                value="{{ $key }}" {{ (string) request('filters.status') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </th>
                                <th><input type="text" name="filters[debt]" value="{{ request('filters.debt') }}"
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
                                            class="fa fa-search"></i>
                                    </button>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($users as $user)
                                <tr class="text-center" id="row-{{ $user->id }}">
                                    <td class="col-id">{{ $user->id }}</td>
                                    {{--<td>{{ $user->first_name }}</td>--}}
                                    {{--<td>{{ $user->last_name }}</td>--}}
                                    <td class="col-title">{{ $user->username }}</td>
                                    <td>{{ $user->address }}</td>
                                    {{--                                    <td>{{ $user->email }}</td>--}}
                                    {{--<td>{{ $user->email_verified_at }}</td>--}}
                                    <td>
                                        @if($user->photo)
                                            <img src="{{ asset('storage/' . $user->avatar->path) }}" alt="Photo"
                                                 style="width: 50px; height: auto;">
                                        @endif
                                    </td>
                                    <td>{{ PhoneFormatService::uzPhone($user->phone) }}</td>
                                    <td>{{ $user->role->title }}</td>
                                    <td style="width: 100px">{{ StatusService::getList()[$user->status] }}</td>
                                    @php
                                        $debtUzs = $user->userDebt->where('currency', StatusService::CURRENCY_UZS)->sum('amount');
                                        $debtUsd = $user->userDebt->where('currency', StatusService::CURRENCY_USD)->sum('amount');
                                    @endphp
                                    <td class="col-debt">
                                        @if($debtUzs > 0)
                                            <div class="text-danger fw-bold">
                                                {{ PriceHelper::format($debtUzs, StatusService::CURRENCY_UZS) }}
                                            </div>
                                        @endif
                                        @if($debtUsd > 0)
                                            <div class="text-danger fw-bold">
                                                {{ PriceHelper::format($debtUsd, StatusService::CURRENCY_USD) }}
                                            </div>
                                        @endif

                                        @if($debtUzs == 0 && $debtUsd == 0)
                                            <span class="text-success fw-bold">0</span>
                                        @endif
                                    </td>
                                    <td>{{ $user->created_at?->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <x-backend.action route="user" :id="$user->id" :view="true" :edit="true"
                                                          :delete="true"/>
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
                            <select name="filters[username]" class="form-control form-control-sm filter-select2 w-100">
                                <option value="">Барчаси</option>
                                @foreach($staffs as $id => $username)
                                    <option
                                        value="{{ $username }}" {{ request('filters.username') == $username ? 'selected' : '' }}>
                                        {{ $username }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-sm btn-outline-info" title="Қидириш">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                        @forelse($users as $user)
                            <div class="card border">
                                <div class="card-body">
                                    @if($user->photo)
                                        <div class="text-center mb-2">
                                            <img src="{{ asset('storage/' . $user->avatar->path) }}" alt="Photo"
                                                 class="img-fluid" style="max-width: 256px;">
                                        </div>
                                    @endif
                                    <p class="card-text">
                                        <strong>{!! sortLink('id', 'ID:') !!} </strong>{{ $user->id }} </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('username', 'Ҳодим:') !!} </strong>{{ $user->username }}
                                    </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('address', 'Адрес:') !!} </strong>{{ $user->address }}</p>
                                    {{--<p class="card-text"><strong>{!! sortLink('email', 'Email:') !!} </strong>{{ $user->email }}</p>--}}
                                    <p class="card-text">
                                        <strong>{!! sortLink('phone', 'Телефон:') !!} </strong> {{ PhoneFormatService::uzPhone($user->phone) }}
                                    </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('role_id', 'Даража:') !!} </strong>{{ $user->role->title }}
                                    </p>
                                    @php
                                        $debtUzs = $user->userDebt->where('currency', StatusService::CURRENCY_UZS)->sum('amount');
                                        $debtUsd = $user->userDebt->where('currency', StatusService::CURRENCY_USD)->sum('amount');
                                    @endphp
                                    <p class="card-text">
                                        <strong>{!! sortLink('debt', 'Қарздорлик:') !!}</strong>
                                        @if($debtUzs > 0)
                                            <span class="text-danger fw-bold">
                                            {{ PriceHelper::format($debtUzs, StatusService::CURRENCY_UZS) }}
                                        </span>
                                        @endif
                                        @if($debtUsd > 0)
                                            <span class="text-danger fw-bold">
                                            {{ PriceHelper::format($debtUsd, StatusService::CURRENCY_USD) }}
                                        </span>
                                        @endif
                                        @if($debtUzs == 0 && $debtUsd == 0)
                                            <span class="text-success fw-bold">0</span>
                                        @endif
                                    </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('created_at_exact', 'Яратилди(сана):') !!} </strong> {{ $user->created_at?->format('Y-m-d H:i') }}
                                    </p>
                                    <div class="btn-group w-100">
                                        <x-backend.action route="user" :id="$user->id" :view="true" :edit="true"
                                                          :delete="true"/>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-center">Маълумот топилмади</p>
                        @endforelse
                    </div>
                    {{-- Mobile version end --}}
                </form>

                <div class="d-flex justify-content-center">
                    {{ $users->links('pagination::bootstrap-4') }}
                </div>

                <div class="d-flex flex-wrap gap-3 mt-4">
                    <!-- Client -->
                    <div class="card-stats client">
                        <div class="w-100">
                           <p>Ҳодимлар</p>
                            <h5>{{ number_format($staffCount, 0, '', ' ') }} та</h5>
                        </div>
                        <div>
                            <i class="bi bi-wallet2"></i>
                        </div>
                    </div>

                    <!-- UZS -->
                    <div class="card-stats uzs">
                        <div class="w-100">
                           <p>Қарздорлик(сўм)</p>
                            <h5>{{ number_format($totalDebtUzs, 0, '', ' ') }} сўм</h5>
                        </div>
                        <div>
                            <i class="bi bi-currency-exchange"></i>
                        </div>
                    </div>

                    <!-- USD -->
                    <div class="card-stats usd">
                        <div class="w-100">
                            <p>Қарздорлик($)</p>
                            <h5>{{ number_format($totalDebtUsd, 2, '.', ' ') }} $</h5>
                        </div>
                        <div>
                            <i class="bi bi-currency-euro"></i>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $('.filter-select2').select2({
                placeholder: "Барчаси",
                allowClear: true,
                minimumInputLength: 2,
                language: {
                    inputTooShort: function (args) {
                        return "Камида 2 та белги киритинг";
                    },
                    noResults: function () {
                        return "Ҳеч қандай натижа топилмади";
                    }
                },
                width: '100%'
            });
        });
    </script>

    <script>
        document.getElementById('staffFilterForm').addEventListener('submit', function (e) {
            // Faqat ko‘rinmayotgan input/selectlarni disable qilamiz
            this.querySelectorAll('[name^="filters"]').forEach(el => {
                if (el.offsetParent === null) {
                    el.disabled = true;
                }
            });

            // Bo‘sh input/selectlarni olib tashlaymiz
            this.querySelectorAll('input[name^="filters"], select[name^="filters"]').forEach(el => {
                if (!el.value.trim()) {
                    el.remove();
                }
            });
        });
    </script>

</x-backend.layouts.main>
