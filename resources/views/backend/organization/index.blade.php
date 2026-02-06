<x-backend.layouts.main title="{{ 'Филиаллар' }}">

    <div class="container-fluid">

        <div class="card-custom shadow-sm">
            <div class="card-header-custom action-btns">
                @can('coreAccess')
                    <div class="row justify-content-start">
                        <div class="col-sm-12 col-md-auto text-start">
                            <x-backend.action route="organization" :back="true" :create="true"/>
                        </div>
                    </div>
                @endcan
            </div>
            <div class="card-body p-0">
                <form id="organizationFilterForm" method="GET" action="{{ route('organization.index') }}">
                    <div class="table-responsive d-none d-md-block">
                        <table class="table mb-0">
                            <thead>
                            <tr class="text-center">
                                <th class="fw-bold">ID</th>
                                <th class="fw-bold" style="width: 30%">Номи</th>
                                <th class="fw-bold" style="width: 25%">Жавобгар ҳодим</th>
                                <th class="fw-bold" style="width: 25%">Яратилди</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($organizations as $organization)
                                <tr class="text-center" id="row-{{ $organization->id }}">
                                    <td class="col-id">{{ $organization->id }}</td>
                                    <td class="col-title">{{ $organization->title }}</td>
                                    <td class="col-title">
                                        @foreach($organization->users as $user)
                                            <span class="badge bg-info">{{ $user->username }}</span>
                                        @endforeach
                                    </td>
                                    <td class="col-date">{{ $organization->created_at?->format('Y-m-d H:i') }}</td>
                                    <td class="text-center action-btns">
                                        <x-backend.action
                                            route="organization" :id="$organization->id" :view="true" :edit="true"
                                        />
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-5 text-center">
                                        <img src="{{ asset('images/systems/reference-not-found.png') }}" width="60"
                                             class="mb-3 opacity-20" alt="">
                                        <p class="text-muted">Маълумот топилмади</p>
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobile version start --}}
                    <div class="d-md-none p-3">
                        @forelse($organizations as $organization)
                            <div class="mobile-card shadow-sm">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="d-flex align-items-center">
                                        <div class="img-wrapper me-3" style="width: 55px; height: 55px;">
                                            @if(optional($organization->file)->path)
                                                <img src="{{ asset('storage/' . $organization->file->path) }}" alt="">
                                            @else
                                                <div
                                                    class="w-100 h-100 d-flex align-items-center justify-content-center bg-light">
                                                    <i class="bi bi-image"></i></div>
                                            @endif
                                        </div>
                                        <div>
                                            <div
                                                class="fw-bold mb-0 text-dark">{{ $organization->title }}</div>
                                            <span class="text-muted small">ID: {{ $organization->id }}</span>
                                        </div>
                                    </div>
                                    <span class="badge-custom {{ \App\Services\StatusService::getTypeClass()[4] }}">
                                        <div class="fw-bold text-center" style="line-height: 1;">
                                            <div class="text-success"></div>
                                            <div style="height: 2px; background-color: #000; margin: 3px 0;"></div>
                                            <div class="text-danger"></div>
                                        </div>
                                    </span>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <small class="text-muted d-block text-uppercase" style="font-size: 0.65rem;">Тавсифи:</small>
                                        <span
                                            class="small fw-medium">{{ $organization->description }}</span>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block text-uppercase" style="font-size: 0.65rem;">Яратилди</small>
                                        <span
                                            class="small fw-medium">{{ $organization->created_at?->format('d.m.Y H:i') }}</span>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                    <span class="small text-muted"><i class="bi bi-person me-1"></i>
                                        @foreach($organization->users as $user)
                                            <span class="badge bg-info">{{ $user->username }}</span>
                                        @endforeach
                                    </span>
                                    <div class="action-btns">
                                        <x-backend.action route="shift-output" :id="$organization->id" :view="true" :edit="true"/>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="py-5 text-center">
                                <img src="{{ asset('images/systems/reference-not-found.png') }}" width="45"
                                     class="mb-3 opacity-20" alt="">
                                <div class="py-4">Маълумот топилмади</div>
                            </div>
                        @endforelse
                    </div>
                    {{-- Mobile version end --}}
                </form>
            </div>

            <div class="card-footer bg-white border-top-0 p-4">
                <div class="d-flex justify-content-center">
                    {{ $organizations->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>

</x-backend.layouts.main>



