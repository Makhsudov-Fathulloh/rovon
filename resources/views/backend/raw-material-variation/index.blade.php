@php
    use App\Services\StatusService;
    use App\Helpers\PriceHelper;
    use App\Helpers\CountHelper;
@endphp

<x-backend.layouts.main title="{{ 'Хомашёлар' }}">

    <div class="container-fluid">

        <div class="card-custom shadow-sm">
            <div class="card-header-custom action-btns">
                <x-backend.action :back="true"/>
            </div>

            <div class="card-body p-0">
                <form id="rawMaterialVariationFilterForm" method="GET"
                      action="{{ route('raw-material-variation.index') }}">
                    <div class="table-responsive d-none d-md-block">
                        <table class="table mb-0">
                            <thead>
                            <tr class="text-center">
                                <th class="col-id">{!! sortLink('id', 'Id') !!}</th>
                                <th>{!! sortLink('code_title', 'Код/Номи') !!}</th>
                                <th>{!! sortLink('raw_material_code_title', 'Нархи/Миқдори') !!}</th>
                                {{-- <th>{!! sortLink('type', 'Тури') !!}</th> --}}
                                <th>{!! sortLink('status', 'Статус') !!}</th>
                                <th>{!! sortLink('created_at', 'Яратилди') !!}</th>
                                <th></th> {{-- Search btn --}}
                            </tr>
                            {{-- Filter Inputs --}}
                            <tr>
                                <th><input type="text" name="filters[id]" value="{{ request('filters.id') }}"
                                           class="form-control form-control-sm w-100 filter-numeric" placeholder="№...">
                                </th>
                                <th><input type="text" name="filters[raw_material_code_title]"
                                           value="{{ request('filters.raw_material_code_title') }}"
                                           class="form-control form-control-sm w-100" placeholder="Қидирув..."></th>
                                <th><input type="text" name="filters[raw_material_count_total_price]"
                                           value="{{ request('filters.raw_material_count_total_price') }}"
                                           class="form-control form-control-sm w-100 filter-numeric-decimal"></th>
                                <th>
                                    <select name="filters[status]" class="form-control form-control-sm w-100">
                                        <option value="">Барчаси</option>
                                        @foreach(StatusService::getList() as $key => $label)
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

                                <th class="p-0">
                                    <div class="d-flex justify-content-center align-items-center"
                                         style="min-height: 75px;">
                                        <button type="submit" class="btn btn-custom-search" title="Филтрлаш">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </div>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($rawMaterialVariations as $rawMaterialVariation)
                                <tr class="text-center" id="row-desktop-{{ $rawMaterialVariation->id }}">
                                    <td class="col-id">{{ $rawMaterialVariation->id }}</td>
                                    <td>{{ '(' . $rawMaterialVariation->rawMaterial->title . ') ' . $rawMaterialVariation->title  }}</td>
                                    <td class="fw-bold text-center" style="line-height: 1;">
                                        <div
                                            class="text-success">{{ PriceHelper::format($rawMaterialVariation->price, $rawMaterialVariation->currency) }}</div>
                                        <div class="line"></div>
                                        <div
                                            class="text-primary">{{ CountHelper::format($rawMaterialVariation->count, $rawMaterialVariation->unit) }}</div>
                                        <div class="line"></div>
                                        <div
                                            class="text-info">{{ PriceHelper::format($rawMaterialVariation->total_price, $rawMaterialVariation->currency, false) }}
                                            сўм
                                        </div>
                                    </td>
                                    <td>
                                        <span
                                            class="badge-custom {{ StatusService::getListClass()[$rawMaterialVariation->status] }}">{{ StatusService::getList()[$rawMaterialVariation->status] }}</span>
                                    </td>
                                    <td>{{ $rawMaterialVariation->created_at?->format('Y-m-d H:i') }}</td>
                                    <td class="text-center action-btns">
                                        <button type="button" class="btn btn-sm btn-success add-count-btn"
                                                data-id="{{ $rawMaterialVariation->id }}"
                                                data-title="{{ $rawMaterialVariation->title }}"
                                                data-count="{{ $rawMaterialVariation->count }}"
                                                data-unit="{{ $rawMaterialVariation->unit }}"
                                                title="Хомашё микдорини ошириш">
                                            <i class="fa fa-plus"></i> Қўшиш
                                        </button>
                                        <x-backend.action route="raw-material-variation" :id="$rawMaterialVariation->id"
                                                          :view="true" :edit="true" :delete="true"/>
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
                        <div class="search-box-mobile mb-4">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"
                                      style="border-radius: 12px 0 0 12px;"><i
                                        class="fa fa-search text-muted"></i></span>
                                <input type="text" name="filters[raw_material_code_title]"
                                       value="{{ request('filters.raw_material_code_title') }}"
                                       class="form-control border-start-0 ps-0" placeholder="     Қидирув..."
                                       style="border-radius: 0 12px 12px 0; height: 48px;">
                                <button type="submit" class="btn btn-primary ms-2"
                                        style="border-radius: 12px; width: 48px;"><i class="fa fa-arrow-right"></i>
                                </button>
                            </div>
                        </div>
                        @forelse($rawMaterialVariations as $rawMaterialVariation)
                            <div class="mobile-card shadow-sm">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="d-flex align-items-center">
                                        <div class="img-wrapper me-3" style="width: 55px; height: 55px;">
                                            @if(optional($rawMaterialVariation->file)->path)
                                                <img src="{{ asset('storage/' . $rawMaterialVariation->file->path) }}"
                                                     alt="">
                                            @else
                                                <div
                                                    class="w-100 h-100 d-flex align-items-center justify-content-center bg-light">
                                                    <i class="bi bi-image"></i></div>
                                            @endif
                                        </div>
                                        <div>
                                            <div
                                                class="fw-bold mb-0 text-dark">{{ $rawMaterialVariation->title }}</div>
                                            <span class="text-muted small">ID: {{ $rawMaterialVariation->id }}</span>
                                        </div>
                                    </div>
                                    <span class="badge-custom {{ StatusService::getTypeClass()[4] }}">
                                        <div class="fw-bold text-center" style="line-height: 1;">
                                            <div
                                                class="text-success">{{ PriceHelper::format($rawMaterialVariation->price, $rawMaterialVariation->currency) }}</div>
                                            <div style="height: 2px; background-color: #000; margin: 3px 0;"></div>
                                            <div
                                                class="text-primary">{{ CountHelper::format($rawMaterialVariation->count, $rawMaterialVariation->unit) }}</div>
                                                <div style="height: 2px; background-color: #000; margin: 3px 0;"></div>
                                            <div
                                                class="text-info">{{ PriceHelper::format($rawMaterialVariation->total_price, $rawMaterialVariation->currency, false) }}
                                                сўм
                                            </div>
                                        </div>
                                    </span>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <small class="text-muted d-block text-uppercase"
                                               style="font-size: 0.65rem;">Махсулот тури</small>
                                        <span
                                            class="small fw-medium">{{ $rawMaterialVariation->rawMaterial->title }}</span>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block text-uppercase"
                                               style="font-size: 0.65rem;">Яратилди</small>
                                        <span
                                            class="small fw-medium">{{ $rawMaterialVariation->created_at?->format('d.m.Y H:i') }}</span>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                        <span class="small text-muted"><i class="bi bi-person me-1"></i><span
                                                class="badge bg-info">{{ optional($rawMaterialVariation->rawMaterial->warehouse)->title }}</span></span>
                                    <div class="action-btns">
                                        <x-backend.action route="raw-material-variation" :id="$rawMaterialVariation->id"
                                                          :variation="$rawMaterialVariation"
                                                          addCountTitle="Махсулот миқдорини ошириш" :addCount="true"
                                                          :view="true" :edit="true" :delete="true"/>
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

            <!-- Add Count Modal -->
            <div class="modal fade" id="addCountModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-top">
                    <div class="modal-content">
                        <form id="addCountForm">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title">Хомашё микдорини ошириш.</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" id="variation_id">
                                <div class="mb-3">
                                    <label>Хомашё номи:</label>
                                    <input type="text" id="variation_title" class="form-control" disabled>
                                </div>
                                <div class="mb-3">
                                    <label>Ҳозирги микдори:</label>
                                    <input type="text" id="current_count" class="form-control" disabled>
                                </div>
                                <div class="mb-3">
                                    <label>Қўшиладиган хомашё микдори:</label>
                                    <input type="number" id="add_count" name="add_count" class="form-control"
                                           min="0.001" step="0.001" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Бекор қилиш
                                </button>
                                <button type="submit" class="btn btn-info">Сақлаш</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="card-footer bg-white border-top-0 p-4">
                <div class="d-flex justify-content-center">
                    {{ $rawMaterialVariations->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>

        <div class="d-flex flex-wrap gap-3 mt-4">
            <!-- UZS -->
            <div class="card-stats uzs">
                <div class="w-100">
                    <h5>🇺🇿 UZS</h5>
                    <p>Хомашёлар (сўм):</p>
                    <h5><strong>{{ number_format($allCountUzs, 0, '', ' ') }} та</strong></h5>
                </div>
                <div>
                    <i class="bi bi-wallet2"></i>
                </div>
            </div>

            <!-- USD -->
            <div class="card-stats usd">
                <div class="w-100">
                    <h5>🇺🇸 USD</h5>
                    <p>Хомашёлар ($):</p>
                    <h5><strong>{{ number_format($allCountUsd, 0, '', ' ') }} та</strong></h5>
                </div>
                <div>
                    <i class="bi bi-currency-exchange"></i>
                </div>
            </div>

            <!-- TotalPrice -->
            <div class="card-stats total">
                <div class="w-100">
                    <p>Умумий сумма</p>
                    <h5><strong>{{ number_format($totalPrice ?? 0, 0, '', ' ') }} сўм</strong></h5>
                </div>
                <div>
                    <i class="bi bi-currency-euro"></i>
                </div>
            </div>
        </div>

    </div>

    <script>
        document.getElementById('rawMaterialVariationFilterForm').addEventListener('submit', function () {
            // Faqat ko‘rinib turgan selectni qoldiramiz
            this.querySelectorAll('input[name="filters[raw_material_code_title]"]').forEach(select => {
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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const addCountModalEl = document.getElementById('addCountModal');
            if (!addCountModalEl) return;

            const addCountModal = new bootstrap.Modal(addCountModalEl);

            // 🔹 Button bosganda modalni ochish
            document.querySelectorAll('.add-count-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    const id = this.dataset.id;
                    const title = this.dataset.title;
                    const count = this.dataset.count;
                    const typeCount = this.dataset.unit || 1; // 1 = dona, 2 = kg

                    document.getElementById('variation_id').value = id;
                    document.getElementById('variation_title').value = title;
                    document.getElementById('current_count').value = formatNumberCustom(count, typeCount);
                    document.getElementById('add_count').value = '';

                    addCountModal.show();
                });
            });

            const form = document.getElementById('addCountForm');
            if (!form) return;

            // 🔹 Forma yuborilganda
            form.addEventListener('submit', async function (e) {
                e.preventDefault();

                const id = document.getElementById('variation_id').value;
                const addCount = parseFloat(document.getElementById('add_count').value);

                if (isNaN(addCount) || addCount < 0.001) {
                    showCustomConfirm('Илтимос, маҳсулот микдорини киритинг!', 'warning');
                    return;
                }

                try {
                    const res = await fetch(`/admin/raw-material-variation/${id}/add-count`, {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({add_count: addCount})
                    });

                    if (!res.ok) {
                        const json = await res.json().catch(() => null);
                        throw new Error(json?.message || `Server returned ${res.status}`);
                    }

                    const data = await res.json();

                    if (data.success) {
                        // 🔹 Jadvaldagi qiymatlarni yangilash
                        ['desktop', 'mobile'].forEach(prefix => {
                            const row = document.getElementById(`row-${prefix}-${id}`);
                            if (row) {
                                const countEl = row.querySelector('.count');
                                const totalEl = row.querySelector('.total_price');

                                if (countEl) countEl.innerText = formatNumberCustom(data.new_count, data.unit);
                                if (totalEl) totalEl.innerText = formatNumberCustom(data.new_total_price, 1);

                                row.querySelectorAll('.add-count-btn').forEach(btn => {
                                    btn.dataset.count = data.new_count;
                                });
                            }
                        });

                        addCountModal.hide();

                        // 🔹 Modal yopilgach alert chiqadi
                        setTimeout(() => {
                            showCustomConfirm(`
                        Маҳсулот муваффақиятли қўшилди!<br>
                        <b>${data.title || ''}</b><br>
                          <span>
                            Қўшилди: <b>${formatNumberCustom(addCount, data.unit)}</b> ${data.unit == 1 ? 'дона' : 'кг'}.<br>
                            Жами: <b>${formatNumberCustom(data.new_count, data.unit)}</b> ${data.unit == 1 ? 'дона' : 'кг'}.
                        </span>
                    `, 'success');
                        }, 300);

                        const somCountEl = document.getElementById('stat-som-count');
                        const dollarCountEl = document.getElementById('stat-dollar-count');
                        const totalPriceEl = document.getElementById('stat-total-price');

                        if (somCountEl) somCountEl.innerText = formatNumberCustom(data.all_som_count, 1) + ' та';
                        if (dollarCountEl) dollarCountEl.innerText = formatNumberCustom(data.all_dollar_count, 1) + ' та';
                        if (totalPriceEl) totalPriceEl.innerText = formatNumberCustom(data.total_price, 1) + ' сўм';

                    } else {
                        setTimeout(() => {
                            showCustomConfirm(data.message || 'Хатолик юз берди!', 'error');
                        }, 400);
                    }

                } catch (err) {
                    console.error(err);
                    setTimeout(() => {
                        showCustomConfirm('Сервер билан боғланишда хатолик: ' + err.message, 'error');
                    }, 400);
                }
            });

            // 🔹 Custom confirm (UI alert)
            function showCustomConfirm(message, type = 'info') {
                const container = document.body;
                const confirmBox = document.createElement('div');

                // 🔹 Ekran kengligini aniqlash
                const isMobile = window.innerWidth <= 768;

                confirmBox.className = 'custom-confirm';
                confirmBox.style.cssText = `
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.45);
            display: flex;
            justify-content: center;
            align-items: ${isMobile ? 'flex-start' : 'center'};
            ${isMobile ? 'padding-top: 60px;' : ''}
            z-index: 9999;
            animation: fadeIn 0.3s ease;
        `;
                confirmBox.innerHTML = `
            <div style="
                background: #fff;
                padding: 25px 35px;
                border-radius: 14px;
                max-width: 420px;
                width: 100%;
                text-align: center;
                box-shadow: 0 8px 25px rgba(0,0,0,0.25);
                font-size: 1.05rem;
                animation: slideUp 0.3s ease;
            ">
                <div style="
                    margin-bottom: 15px;
                    font-size: 1.2rem;
                    font-weight: 600;
                    background: ${getAlertGradient(type)};
                    -webkit-background-clip: text;
                    -webkit-text-fill-color: transparent;
                ">
                    ${getIcon(type)} ${type === 'success' ? 'Муваффақиятли!' : ''}
                </div>
                <p style="margin-bottom: 25px;">${message}</p>
                <button id="confirm-ok" style="
                    background: ${getAlertGradient(type)};
                    color: #fff;
                    padding: 10px 24px;
                    border: none;
                    border-radius: 6px;
                    cursor: pointer;
                    font-weight: 500;
                    box-shadow: 0 3px 8px rgba(0,0,0,0.2);
                    transition: transform 0.2s ease;
                " onmouseover="this.style.transform='scale(1.05)'"
                  onmouseout="this.style.transform='scale(1)'">
                    OK
                </button>
            </div>
        `;
                container.appendChild(confirmBox);

                confirmBox.querySelector('#confirm-ok').addEventListener('click', () => confirmBox.remove());
            }

            function getAlertGradient(type) {
                switch (type) {
                    case 'success':
                        return 'linear-gradient(135deg, #38b000, #70e000)';
                    case 'error':
                        return 'linear-gradient(135deg, #ff3c38, #f5656c)';
                    case 'warning':
                        return 'linear-gradient(135deg, #ffb703, #ffd60a)';
                    case 'info':
                        return 'linear-gradient(135deg, #0096c7, #00b4d8)';
                    default:
                        return '#555';
                }
            }

            function getIcon(type) {
                switch (type) {
                    case 'success':
                        return '✅';
                    case 'error':
                        return '❌';
                    case 'warning':
                        return '⚠️';
                    case 'info':
                        return 'ℹ️';
                    default:
                        return '';
                }
            }
        });
    </script>

</x-backend.layouts.main>
