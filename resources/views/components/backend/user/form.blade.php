@php
    use App\Services\StatusService;
@endphp

<form action="{{ $action }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if ($method === 'PUT')
        @method('PUT')
    @endif
    <div class="container-fluid mt-4">
        <div class="row">

            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-body">
                        <div class="tab-content" style="margin: 0">
                            <div class="tab-pane fade show active">
                                <div class="pt-4">
                                    {{--<div class="mb-3">--}}
                                    {{--<label for="first_name" class="form-label">Исм</label>--}}
                                    {{--<input type="text" id="first_name" name="first_name" class="form-control"--}}
                                    {{--value="{{ old('first_name', $user->first_name ?? '') }}">--}}
                                    {{--</div>--}}

                                    {{--<div class="mb-3">--}}
                                    {{--<label for="last_name" class="form-label">Фамилия</label>--}}
                                    {{--<input type="text" id="last_name" name="last_name" class="form-control"--}}
                                    {{--value="{{ old('last_name', $user->last_name ?? '') }}">--}}
                                    {{--</div>--}}

                                    <div class="mb-3">
                                        <label for="username" class="form-label">Фойдаланувчи номи (Username)</label>
                                        <input type="text" id="username" name="username" class="form-control"
                                               value="{{ old('username', $user->username ?? '') }}">
                                        @error('username')
                                        <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    @if (Route::currentRouteName() == 'user.edit')
                                        <div class="mb-3">
                                            <label for="email" class="form-label">E-mail</label>
                                            <input type="text" id="email" name="email" class="form-control"
                                                   value="{{ old('email', $user->email ?? '') }}">
                                            @error('email')
                                            <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="current_password">Эски пароль</label>
                                            <input type="password" id="current_password" name="current_password"
                                                   class="form-control">
                                            @error('current_password')
                                            <div style="color:red">{{ $message }}</div> @enderror
                                        </div>
                                    @endif

                                    <div class="mb-3">
                                        <label for="password_hash">Янги пароль</label>
                                        <input type="password" id="password_hash" name="password_hash"
                                               class="form-control">
                                        @error('password_hash')
                                        <div style="color:red">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="password_hash_confirmation">Паролни тасдиқланг</label>
                                        <input type="password" id="password_hash_confirmation"
                                               name="password_hash_confirmation" class="form-control">
                                    </div>

                                    {{--<div class="mb-3">--}}
                                    {{--<label for="email" class="form-label">Email</label>--}}
                                    {{--<input type="email" id="email" name="email" class="form-control"--}}
                                    {{--value="{{ old('email', $user->email ?? '') }}">--}}
                                    {{--</div>--}}

                                    <div class="mb-3">
                                        <label for="address" class="form-label">Адрес</label>
                                        <input type="text" id="address" name="address" class="form-control"
                                               value="{{ old('address', $user->address ?? '') }}">
                                    </div>

                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Телефон</label>
                                        <input id="phone" type="text" name="phone" class="form-control"
                                               value="{{ old('phone', $user->phone ?? '') }}">
                                        @error('phone')
                                        <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow">
                    <div class="card-body">
                        <h3 class="panel-heading">Расм</h3>

                        <div class="panel-body">
                            <div class="form-group" style="margin: 0;">
                                <div class="input-group">
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-primary open-file-manager">
                                        <i class="fa fa-picture-o"></i>Файл юклаш
                                    </button>
                                </span>
                                    <input id="thumbnail_1" class="form-control" type="text" name="filepath"
                                           value="{{ $user->avatar ? asset('storage/' . $user->avatar->path) : '' }}"
                                           readonly>
                                    <input type="file" name="photo" id="real-file" style="display: none;" multiple>
                                </div>
                                @if ($user->avatar)
                                    <div id="holder_1" style="margin-top:15px; max-height:100px;">
                                        <img src="{{ asset('storage/' . $user->avatar->path) }}"
                                             style="height: 80px;">
                                    </div>
                                @else
                                    <div id="holder_1" style="margin-top:15px; max-height:100px;"></div>
                                @endif
                            </div>
                        </div>

                        @if(\Illuminate\Support\Facades\Auth::user()->role->title == 'Developer')
                            <div class="mb-3 mt-3">
                                <label for="role_id">Даража</label>
                                <select class="form-control select2" name="role_id">
                                    <option value="{{ $clientRoleId }}">
                                        {{ $clientRole ?? 'Client' }}
                                    </option>
                                    @foreach($rolesRoot as $id => $name)
                                        @continue($id == $clientRoleId)
                                        <option value="{{ $id }}"
                                            {{ old('role_id', $user->role_id ?? $clientRoleId) == $id ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        @if(Auth::user()->role->title == 'Admin')
                            <div class="mb-3 mt-3">
                                <label>Даража</label>
                                <select class="form-control select2" name="role_id">

                                    {{-- AGAR EDIT QILINAYOTGAN USER ADMIN BO'LSA --}}
                                    @if($user->exists && $user->role->title === 'Admin')
                                        <option value="{{ $user->role_id }}" selected>
                                            {{ $user->role->title }}
                                        </option>
                                    @endif

                                    <option value="{{ $clientRoleId }}">Client</option>

                                    @foreach($rolesAdmin as $id => $name)
                                        @continue($id == $clientRoleId)
                                        <option value="{{ $id }}"
                                            {{ old('role_id', $user->role_id) == $id ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        @if(Auth::user()->role->title == 'Manager')
                            <div class="mb-3 mt-3">
                                <label>Даража</label>
                                <select class="form-control select2" name="role_id">

                                    {{-- AGAR EDIT QILINAYOTGAN USER MANAGER BO'LSA --}}
                                    @if($user->exists && $user->role->title === 'Manager')
                                        <option value="{{ $user->role_id }}" selected>
                                            {{ $user->role->title }}
                                        </option>
                                    @endif

                                    <option value="{{ $clientRoleId }}">Client</option>

                                    @foreach($roles as $id => $name)
                                        @continue($id == $clientRoleId)
                                        <option value="{{ $id }}"
                                            {{ old('role_id', $user->role_id) == $id ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        @can('hasAccess')
                            <div class="mb-3 mt-3">
                                <label for="telegram_chat_id" class="form-label">Telegram ID</label>
                                <input type="text" id="telegram_chat_id" name="telegram_chat_id" class="form-control"
                                       value="{{ old('telegram_chat_id', $user->telegram_chat_id ?? '') }}"
                                       oninput="this.value = this.value.replace(/(?!^-)[^0-9]/g, '')">
                            </div>
                        @endcan

                        <div class="mb-3">
                            <label for="status" class="form-label">Статус</label>
                            <select name="status" id="status" class="form-control">
                                @foreach (StatusService::getList() as $key => $label)
                                    <option
                                        value="{{ $key }}" {{ old('status', $user->status ?? '') == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        @if (Route::currentRouteName() == 'user.create')
                            <div class="row">
                                <div class="col-md-9">
                                    <div class="mb-3">
                                        <label for="debt" class="form-label">Қарздорлик</label>
                                        <input type="text" id="debt" name="debt" class="form-control filter-numeric-decimal" value="0">
                                        @error('debt')
                                        <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="currency">Валюта</label>
                                        <select name="currency" id="currency" class="form-control">
                                            @foreach (StatusService::getCurrency() as $key => $label)
                                                <option value="{{ $key }}" {{ $key == StatusService::CURRENCY_UZS ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                        @elseif (Route::currentRouteName() == 'user.edit')
                            <div class="mb-3">
                                <div class="mb-3">
                                    <label for="debt_uzs" class="form-label">Қарздорлик <span class="text-danger fw-bold"> {{ number_format(old('debt_uzs', $debtUzs), 0, '', ' ') }} </span> (UZS)</label>
                                    <input type="text" id="debt_uzs" name="debt_uzs" class="form-control filter-numeric-decimal">
                                </div>
                                @error('debt_uzs')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                                <div class="mb-3">
                                    <label for="debt_usd" class="form-label">Қарздорлик <span class="text-danger fw-bold"> {{ number_format(old('debt_usd', $debtUsd), 2, '.', ' ') }} </span> (USD)</label>
                                    <input type="text" id="debt_usd" name="debt_usd" class="form-control filter-numeric-decimal">
                                </div>
                                @error('debt_usd')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif

                        @if (Route::currentRouteName() == 'user.create')
                            <button type="submit" class="btn btn-success">{{ 'Сақлаш' }}</button>
                        @elseif (Route::currentRouteName() == 'user.edit')
                            <button type="submit" class="btn btn-success">{{ 'Янгилаш' }}</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
