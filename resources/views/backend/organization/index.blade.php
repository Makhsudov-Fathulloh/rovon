<x-backend.layouts.main title="{{ 'Филиаллар' }}">

    <div class="row">
        <div class="card shadow w-100">
            <div class="card-header">
                @can('coreAccess')
                    <div class="row justify-content-start">
                        <div class="col-sm-12 col-md-auto text-start">
                            <a href="{{ route('organization.create') }}" class="btn btn-primary w-100 w-md-auto">
                                {{ 'Яратиш' }}
                            </a>
                        </div>
                    </div>
                @endcan
            </div>
            <div class="table-responsive card-body">
                <form id="organizationFilterForm" method="GET" action="{{ route('organization.index') }}">
                    <div class="table-responsive d-none d-md-block">
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th class="fw-bold">ID</th>
                                <th class="fw-bold">Номи</th>
                                <th class="fw-bold">Тавсифи</th>
                                <th class="fw-bold">Жавобгар ҳодим</th>
                                <th class="fw-bold">Яратилди</th>
                                <th class="fw-bold">Янгиланди</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($organizations as $organization)
                                <tr class="text-center" id="row-{{ $organization->id }}">
                                    <td class="col-id">{{ $organization->id }}</td>
                                    <td class="col-title">{{ $organization->title }}</td>
                                    <td>{{ $organization->description }}</td>
                                    <td class="col-title">{{ optional($organization->user)->username }}</td>
                                    <td class="col-date">{{ $organization->created_at?->format('Y-m-d H:i') }}</td>
                                    <td class="col-date">{{ $organization->updated_at?->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <x-backend.action
                                            route="organization" :id="$organization->id" :view="true" :edit="true"
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
                        @forelse($organizations as $organization)
                            <div class="card border">
                                <div class="card-body">
                                    <p class="card-text">
                                        <strong>ID: </strong>{{ $organization->id }} </p>
                                    <p class="card-text">
                                        <strong>Номи: </strong>{{ $organization->title }} </p>
                                    <p class="card-text">
                                        <strong>Тавсифи: </strong>{{ $organization->description }} </p>
                                    <p class="card-text">
                                        <strong>Жавобгар ходим: </strong>{{ optional($organization->user)->username }}
                                    <p class="card-text">
                                        <strong>Яратилди: </strong> {{ $organization->created_at?->format('Y-m-d H:i') }}
                                    </p>
                                    <p class="card-text">
                                        <strong>Яратилди: </strong> {{ $organization->updated_at?->format('Y-m-d H:i') }}
                                    </p>
                                    <x-backend.action
                                        route="organization" :id="$organization->id" :view="true" :edit="true"
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
                    {{ $organizations->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>

</x-backend.layouts.main>



