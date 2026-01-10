<x-backend.layouts.main title="{{ 'Роллар' }}">

    <div class="row">
        <div class="card shadow w-100">
            <div class="card-header">
                <div class="row justify-content-start">
                    <div class="col-sm-12 col-md-auto text-start">
                        <a href="{{ route('role.create') }}" class="btn btn-primary w-100 w-md-auto">
                            {{ 'Яратиш' }}
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('role.index') }}">
                    <div class="table-responsive d-none d-md-block">
                        <table class="table table-bordered table-hover table-striped">
                            <thead>
                            <tr class="thead-dark">
                                <th class="fw-bold">Id</th>
                                <th class="fw-bold">Номи</th>
                                <th class="fw-bold">Тавсифи</th>
                                <th class="fw-bold">Яратилди</th>
                                <th class="fw-bold">Янгиланди</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($roles as $role)
                                <tr>
                                    <td>{{ $role->id }}</td>
                                    <td>{{ $role->title }}</td>
                                    <td>{!! $role->description !!}</td>
                                    <td>{{ $role->created_at?->format('Y-m-d H:i') }}</td>
                                    <td>{{ $role->updated_at?->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <x-backend.action route="role" :id="$role->id"  :edit="true" :delete="true"/>
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
                        @forelse($roles as $role)
                            <div class="card border">
                                <div class="card-body">
                                    <p class="card-text">
                                        <strong>ID:</strong>{{ $role->id }}
                                    </p>
                                    <p class="card-text">
                                        <strong>Номи:</strong>{{ $role->title }}
                                    </p>
                                    <p class="card-text">
                                        <strong>Тавсифи:</strong> {!! $role->description !!}
                                    </p>
                                    <p class="card-text">
                                        <strong>Янгиланди(сана):</strong> {{ $role->updated_at?->format('Y-m-d H:i') }}
                                    </p>
                                    <div class="btn-group w-100">
                                        <x-backend.action route="role" :id="$role->id" :edit="true" :delete="true" />
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-center">Маълумот топилмади</p>
                        @endforelse
                    </div>
                    {{-- Mobile version end --}}
                </form>
            </div>
        </div>
    </div>

</x-backend.layouts.main>



