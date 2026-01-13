<x-backend.layouts.main title="{{ 'Омборлар' }}">

    <div class="row">
        <div class="card shadow w-100">
            <div class="card-header">
                @can('coreAccess')
                    <div class="row justify-content-start">
                        <div class="col-sm-12 col-md-auto text-start">
                            <a href="{{ route('warehouse.create') }}" class="btn btn-primary w-100 w-md-auto">
                                {{ 'Яратиш' }}
                            </a>
                        </div>
                    </div>
                @endcan
            </div>
            <div class="table-responsive card-body">
                <form id="warehouseFilterForm" method="GET" action="{{ route('warehouse.index') }}">
                    <div class="table-responsive d-none d-md-block">
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th class="fw-bold">ID</th>
                                <th class="fw-bold">Филиал</th>
                                <th class="fw-bold">Номи</th>
                                <th class="fw-bold">Тавсифи</th>
                                <th class="fw-bold">Статус</th>
                                <th class="fw-bold">Яратилди</th>
                                <th class="fw-bold">Янгиланди</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($warehouses as $warehouse)
                                <tr class="text-center" id="row-{{ $warehouse->id }}">
                                    <td class="col-id">{{ $warehouse->id }}</td>
                                    <td class="col-title">
                                        @foreach($warehouse->organization as $organization)
                                            <span class="badge bg-info">{{ $organization->title }}</span>
                                        @endforeach
                                    </td>
                                    <td class="col-title">{{ $warehouse->title }}</td>
                                    <td>{{ $warehouse->description }}</td>
                                    <td class="col-title">{{ \App\Services\StatusService::getList()[$warehouse->status] ?? '-' }}</td>
                                    <td class="col-date">{{ $warehouse->created_at?->format('Y-m-d H:i') }}</td>
                                    <td class="col-date">{{ $warehouse->updated_at?->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <x-backend.action
                                            route="warehouse" listRoute="warehouse" :id="$warehouse->id"
                                            subRoute="elements"
                                            :list="true" :view="true" :edit="true" listTitle="Омбор элементларини кўриш"
                                            viewClass="btn btn-secondary btn-sm"
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
                            <input type="text" name="filters[title]" value="{{ request('filters.title') }}"
                                   class="form-control form-control-sm me-1" placeholder="Маҳсулот номини киритинг">
                            <button type="submit" class="btn btn-sm btn-outline-info" title="Қидириш">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                        @forelse($warehouses as $warehouse)
                            <div class="card border">
                                <div class="card-body">
                                    <p class="card-text">
                                        <strong>ID: </strong>{{ $warehouse->id }} </p>
                                    <p class="card-text">
                                        <strong>Филиал: </strong>
                                        @foreach($warehouse->organization as $organization)
                                            <span class="badge bg-info">{{ $organization->title }}</span>
                                        @endforeach
                                    </p>
                                    <p class="card-text">
                                        <strong>Номи: </strong>{{ $warehouse->title }} </p>
                                    <p class="card-text">
                                        <strong>Тавсифи: </strong>{{ $warehouse->description }} </p>
                                    <p class="card-text">
                                        <strong>Статус: </strong> {{ \App\Services\StatusService::getList()[$warehouse->status] ?? '-' }}
                                    </p>
                                    <p class="card-text">
                                        <strong>Яратилди: </strong> {{ $warehouse->created_at?->format('Y-m-d H:i') }}
                                    </p>
                                    <p class="card-text">
                                        <strong>Яратилди: </strong> {{ $warehouse->updated_at?->format('Y-m-d H:i') }}
                                    </p>
                                    <x-backend.action
                                        route="warehouse" :id="$warehouse->id"
                                        :view="true" :edit="true" createTitle="Филиал яратиш"
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
                    {{ $warehouses->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>

</x-backend.layouts.main>



