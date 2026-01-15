<x-backend.layouts.main title="{{ 'Омбор (' . $warehouse->title . ') элементлари:' }}">

    <div class="container-fluid mt-4">
        <form id="warehouseListFilterForm" method="GET" action="{{ route('warehouse.list', $warehouse) }}" class="mb-3">
            <div class="row g-2">
                <div class="col-md-2">
                    <input type="text" name="filters[code]" class="form-control" placeholder="Код"
                           value="{{ request('filters.code') }}">
                </div>
                <div class="col-md-2">
                    <input type="text" name="filters[title]" class="form-control" placeholder="Номи"
                           value="{{ request('filters.title') }}">
                </div>
                <div class="col-md-2">
                    <select name="filters[status]" class="form-control">
                        <option value="">Барчаси</option>
                        @foreach(\App\Services\StatusService::getList() as $key => $label)
                            <option
                                value="{{ $key }}" {{ strval(request('filters.status')) === strval($key) ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                  <input type="date" name="filters[created_from]"
                         value="{{ request('filters.created_from') }}"
                         class="form-control form-control-sm me-1" placeholder="From">
                </div>
                <div class="col-md-2">
                  <input type="date" name="filters[created_to]"
                         value="{{ request('filters.created_to') }}"
                         class="form-control form-control-sm" placeholder="To">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100"><i class="fa fa-search"></i> Қидириш</button>
                </div>
            </div>
        </form>

        <div class="row ">
            <div class="card shadow mb-4">
                <div class="card-header bg-secondary text-white fw-bold">
                    📤 Маҳсулотлар:
                </div>
                <div class="card-body">
                    <div class="table-responsive d-none d-md-block">
                        <table class="table table-bordered table-hover text-center">
                            <thead class="table-light">
                            <tr>
                                <th class="fw-bold">ID</th>
                                <th class="fw-bold">Код</th>
                                <th class="fw-bold">Номи</th>
                                <th class="fw-bold">Нархи</th>
                                <th class="fw-bold">Микдори</th>
                                <th class="fw-bold">Умумий (сўм)</th>
                                <th class="fw-bold">Статус</th>
                                <th class="fw-bold">Яратилган сана</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($productVariations as $item)
                                <tr class="text-center" id="row-desktop-{{ $item->id }}">
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->code }}</td>
                                    <td>{{ $item->title }}</td>
                                    <td class="fw-bold text-primary text-nowrap">{{ \App\Helpers\PriceHelper::format($item->total_price, $item->currency) }}</td>
                                    <td class="count fw-bold text-success">{{ \App\Helpers\CountHelper::format($item->count, $item->unit) }}</td>
                                    <td class="total_price fw-bold text-info">{{ number_format($item->total_price, 0, '', ' ') }}</td>
                                    <td>{{ \App\Services\StatusService::getList()[$item->status] ?? '-' }}</td>
                                    <td>{{ $item->created_at?->format('d-m-Y H:i') }}</td>
                                    <td>
                                        <div class="btn-group w-100">
                                            <x-backend.action
                                                route="product-variation"
                                                :id="$item->id"
                                                :variation="$item"
                                                :addCount="true"
                                                addCountTitle="Махсулот микдорини ошириш"
                                                :view="true"
                                                data-model="product"
                                                data-id="{{ $item->id }}"
                                                data-title="{{ $item->title }}"
                                                data-count="{{ $item->count }}"
                                                class="add-count-btn"
                                            />
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9">Маълумот топилмади</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobile version start --}}
                    <div class="d-md-none">
                        @forelse($productVariations as $item)
                            <div class="card border" id="row-mobile-{{ $item->id }}">
                                <div class="card-body">
                                    <p class="card-text"><strong>ID:</strong> {{ $item->id }}</p>
                                    <p class="card-text"><strong>Код: </strong> {{ $item->code }}</p>
                                    <p class="card-text"><strong>Номи: </strong> {{ $item->title }}</p>
                                    <p class="card-text">
                                        <strong>Микдори:</strong>
                                        <span class="count fw-bold text-success">{{ \App\Helpers\CountHelper::format($item->count, $item->unit) }}</span>
                                    </p>
                                    <p class="card-text"><strong>Статус: </strong> {{ \App\Services\StatusService::getList()[$item->status] ?? '-' }}</p>
                                    <div class="btn-group w-100">
                                        <x-backend.action
                                            route="product-variation"
                                            :id="$item->id"
                                            :variation="$item"
                                            :addCount="true"
                                            addCountTitle="Махсулот микдорини ошириш"
                                            :view="true"
                                            data-model="product"
                                            data-id="{{ $item->id }}"
                                            data-title="{{ $item->title }}"
                                            data-count="{{ $item->count }}"
                                            class="add-count-btn"
                                        />
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-center">Маълумот топилмади</p>
                        @endforelse
                    </div>
                    {{-- Mobile version end --}}

                    {{ $productVariations->links('pagination::bootstrap-4') }}

                    <div class="mt-3 text-end">
                        <strong>Жами: <span
                                class="fw-bold text-primary">{{ number_format($productAllCount, 0, '', ' ') }}</span>
                            та. Умумий нарх: <span
                                class="fw-bold text-info">{{ number_format($productTotalPrice, 0, '', ' ') }}</span> сўм
                        </strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="card shadow">
                <div class="card-header bg-secondary text-white fw-bold">
                    📥 Хомашёлар:
                </div>
                <div class="card-body">
                    <div class="table-responsive d-none d-md-block">
                        <table class="table table-bordered table-hover text-center">
                            <thead class="table-light">
                            <tr>
                                <th class="fw-bold">ID</th>
                                <th class="fw-bold">Код</th>
                                <th class="fw-bold">Номи</th>
                                <th class="fw-bold">Нархи</th>
                                <th class="fw-bold">Микдори</th>
                                <th class="fw-bold">Умумий (сўм)</th>
                                <th class="fw-bold">Статус</th>
                                <th class="fw-bold">Яратилган сана</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($rawMaterialVariations as $item)
                                <tr class="text-center" id="product-row-desktop-{{ $item->id }}">
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->code }}</td>
                                    <td>{{ $item->title }}</td>
                                    <td class="fw-bold text-primary">{{ \App\Helpers\PriceHelper::format($item->price, $item->currency) }}</td>
                                    <td class="count fw-bold text-success">{{ \App\Helpers\CountHelper::format($item->count, $item->unit) }}</td>
                                    <td class="total_price fw-bold text-info">{{ number_format($item->total_price, 0, '', ' ') }}</td>
                                    <td>{{ \App\Services\StatusService::getList()[$item->status] ?? '-' }}</td>
                                    <td>{{ $item->created_at?->format('d-m-Y H:i') }}</td>
                                    <td>
                                        <x-backend.action
                                            route="raw-material-variation"
                                            :id="$item->id"
                                            :variation="$item"
                                            :addCount="true"
                                            addCountTitle="Хомашё микдорини ошириш"
                                            :view="true"
                                            data-model="raw_material"
                                            data-id="{{ $item->id }}"
                                            data-title="{{ $item->title }}"
                                            data-count="{{ $item->count }}"
                                            data-unit="{{ $item->unit }}"
                                            class="add-count-btn"
                                        />
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9">Маълумот топилмади</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{-- Mobile version start --}}
                    <div class="d-md-none">
                        @forelse($rawMaterialVariations as $item)
                            <div class="card border" id="row-mobile-{{ $item->id }}">
                                <div class="card-body">
                                    <p class="card-text"><strong>ID:</strong> {{ $item->id }}</p>
                                    <p class="card-text"><strong>Код: </strong> {{ $item->code }}</p>
                                    <p class="card-text"><strong>Номи: </strong> {{ $item->title }}</p>
                                    <p class="card-text">
                                        <strong>Микдори:</strong>
                                        <span class="count fw-bold text-success">{{ \App\Helpers\CountHelper::format($item->count, $item->unit) }}</span>
                                    </p>
                                    <p class="card-text"><strong>Статус: </strong> {{ \App\Services\StatusService::getList()[$item->status] ?? '-' }}</p>
                                    <div class="btn-group w-100">
                                        <x-backend.action
                                            route="raw-material-variation"
                                            :id="$item->id"
                                            :variation="$item"
                                            :addCount="true"
                                            addCountTitle="Хомашё микдорини ошириш"
                                            :view="true"
                                            data-model="raw_material"
                                            data-id="{{ $item->id }}"
                                            data-title="{{ $item->title }}"
                                            data-count="{{ $item->count }}"
                                            class="add-count-btn"
                                        />
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-center">Маълумот топилмади</p>
                        @endforelse
                    </div>
                    {{-- Mobile version end --}}

                    {{ $rawMaterialVariations->links('pagination::bootstrap-4') }}

                    <div class="mt-3 text-end">
                        <strong>Жами: <span
                                class="fw-bold text-primary">{{ number_format($rawAllCount, 0, '', ' ') }}</span> та,
                            Умумий нарх: <span
                                class="fw-bold text-info">{{ number_format($rawTotalPrice, 0, '', ' ') }}</span>
                            сўм</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 📦 Product Modal -->
    <div class="modal fade" id="addProductCountModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-top">
            <div class="modal-content">
                <form id="addProductCountForm">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Маҳсулот микдорини ошириш</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="product_id">
                        <input type="hidden" id="product_unit">
                        <div class="mb-3">
                            <label>Маҳсулот номи:</label>
                            <input type="text" id="product_title" class="form-control" disabled>
                        </div>
                        <div class="mb-3">
                            <label>Ҳозирги микдори:</label>
                            <input type="text" id="product_current_count" class="form-control" disabled>
                        </div>
                        <div class="mb-3">
                            <label>Қўшиладиган маҳсулот микдори:</label>
                            <input type="number" id="product_add_count" name="add_count" class="form-control" step="1"
                                   required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Бекор қилиш</button>
                        <button type="submit" class="btn btn-info">Сақлаш</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- 🧱 Raw Material Modal -->
    <div class="modal fade" id="addRawMaterialCountModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-top">
            <div class="modal-content">
                <form id="addRawMaterialCountForm">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Хомашё микдорини ошириш</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="raw_id">
                        <input type="hidden" id="raw_unit">
                        <div class="mb-3">
                            <label>Хомашё номи:</label>
                            <input type="text" id="raw_title" class="form-control" disabled>
                        </div>
                        <div class="mb-3">
                            <label>Ҳозирги микдори:</label>
                            <input type="text" id="raw_current_count" class="form-control" disabled>
                        </div>
                        <div class="mb-3">
                            <label>Қўшиладиган хомашё микдори:</label>
                            <input type="number" id="raw_add_count" name="add_count" class="form-control" step="0.001"
                                   required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Бекор қилиш</button>
                        <button type="submit" class="btn btn-info">Сақлаш</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // 🔹 Modal ochish tugmalari
            document.querySelectorAll('.add-count-btn').forEach(btn => {
                btn.addEventListener('click', handleAddCountClick);
            });

            function handleAddCountClick() {
                const model = this.dataset.model;
                const id = this.dataset.id;
                const title = this.dataset.title;
                const countRaw = this.dataset.count || 0;
                const typeCount = this.dataset.unit ?? '1';

                const parsedCount = parseFloat(countRaw);
                const count = isNaN(parsedCount)
                    ? 0
                    : (typeCount == '1' ? parsedCount.toFixed(3) : parsedCount.toFixed(0));

                if (model === 'raw_material') {
                    document.getElementById('raw_id').value = id;
                    document.getElementById('raw_title').value = title;
                    document.getElementById('raw_current_count').value = count;
                    document.getElementById('raw_add_count').value = '';
                    document.getElementById('raw_unit').value = typeCount;

                    const input = document.getElementById('raw_add_count');
                    if (typeCount == '1') {
                        input.step = '0.001';
                    } else {
                        input.step = '1';
                    }

                    new bootstrap.Modal(document.getElementById('addRawMaterialCountModal')).show();
                } else {
                    document.getElementById('product_id').value = id;
                    document.getElementById('product_title').value = title;
                    document.getElementById('product_current_count').value = count;
                    document.getElementById('product_add_count').value = '';
                    document.getElementById('product_unit').value = typeCount;

                    const input = document.getElementById('product_add_count');
                    input.step = '1';

                    new bootstrap.Modal(document.getElementById('addProductCountModal')).show();
                }
            }

            // 🔹 Formlarni bog‘lash
            handleFormSubmit('addRawMaterialCountForm', 'raw_material');
            handleFormSubmit('addProductCountForm', 'product');

            function handleFormSubmit(formId, model) {
                const form = document.getElementById(formId);

                form.addEventListener('submit', async function (e) {
                    e.preventDefault();

                    const id = document.getElementById(model === 'raw_material' ? 'raw_id' : 'product_id').value;
                    const addCountInput = document.getElementById(model === 'raw_material' ? 'raw_add_count' : 'product_add_count');
                    const addCount = parseFloat(addCountInput.value);
                    const typeCount = document.getElementById(model === 'raw_material' ? 'raw_unit' : 'product_unit').value;

                    if (isNaN(addCount) || addCount <= 0) {
                        showCustomConfirm('Илтимос, миқдорни киритинг!', 'warning');
                        return;
                    }

                    try {
                        const res = await fetch(`/admin/${model.replace('_', '-')}-variation/${id}/add-count`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({add_count: addCount})
                        });

                        const data = await res.json();
                        if (!res.ok || !data.success) {
                            throw new Error(data?.message || 'Сервер хатолиги');
                        }

                        // 🔹 Modalni yopish
                        const modalEl = document.getElementById(model === 'raw_material'
                            ? 'addRawMaterialCountModal'
                            : 'addProductCountModal');
                        bootstrap.Modal.getInstance(modalEl)?.hide();

                        // 🔹 Jadvalni yangilash
                        updateRowData(model, id, data.new_count, data.new_total_price, typeCount);

                        // 🔹 Tugma data-count yangilash
                        updateButtonDataCount(model, id, data.new_count);

                        // 🔹 Birlik (kg yoki dona)
                        const unit = (model === 'raw_material')
                            ? (typeCount == 1 ? 'кг' : 'дона')
                            : 'дона';

                        // 🔹 Alert chiqarish
                        showCustomConfirm(`
                    ${model === 'raw_material' ? 'Хомашё' : 'Маҳсулот'} муваффақиятли қўшилди!<br>
                    <b>${data.title || ''}</b><br>
                    Қўшилди: <b>${addCount}</b> ${unit}.<br>
                    Жами: <b>${data.new_count}</b> ${unit}.
                `, 'success');

                    } catch (err) {
                        console.error(err);
                        showCustomConfirm('Хатолик: ' + err.message, 'error');
                    }
                });
            }

            // 🔹 Jadvaldagi qatorni yangilash
            function updateRowData(model, id, newCount, newTotal, typeCount) {
                const rowDesktop = document.getElementById(model === 'raw_material'
                    ? `product-row-desktop-${id}`
                    : `row-desktop-${id}`);
                const rowMobile = document.getElementById(`row-mobile-${id}`);

                const formattedCount = (typeCount == 1)
                    ? parseFloat(newCount).toFixed(3)
                    : parseFloat(newCount).toFixed(0);

                const formattedTotal = Number(newTotal).toLocaleString('ru-RU');

                [rowDesktop, rowMobile].forEach(row => {
                    if (!row) return;
                    const countEl = row.querySelector('.count');
                    const totalEl = row.querySelector('.total_price');
                    if (countEl) countEl.innerText = formattedCount;
                    if (totalEl) totalEl.innerText = formattedTotal;
                });
            }

            // 🔹 Tugmaning data-count qiymatini yangilash
            function updateButtonDataCount(model, id, newCount) {
                document.querySelectorAll(`.add-count-btn[data-model="${model}"][data-id="${id}"]`)
                    .forEach(btn => btn.dataset.count = newCount);
            }

            // 🔹 Alert UI
            function showCustomConfirm(message, type = 'info') {
                const container = document.body;
                const box = document.createElement('div');

                // 🔹 Ekran kengligini aniqlash
                const isMobile = window.innerWidth <= 768;

                box.className = 'custom-confirm';
                box.style.cssText = `
            position: fixed; top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.45);
            display: flex;
            justify-content: center;
              align-items: ${isMobile ? 'flex-start' : 'center'};
            ${isMobile ? 'padding-top: 60px;' : ''}
            z-index: 9999;
        `;
                box.innerHTML = `
            <div style="background:#fff; padding:25px 35px; border-radius:14px;
                        max-width:420px; width:100%; text-align:center;
                        box-shadow:0 8px 25px rgba(0,0,0,0.25);">
                <div style="margin-bottom:15px; font-size:1.2rem; font-weight:600;
                            background:${getAlertGradient(type)};
                            -webkit-background-clip:text; -webkit-text-fill-color:transparent;">
                    ${getIcon(type)} ${type === 'success' ? 'Муваффақиятли!' : ''}
                </div>
                <p style="margin-bottom:25px;">${message}</p>
                <button id="confirm-ok" style="
                    background:${getAlertGradient(type)};
                    color:#fff; padding:10px 24px; border:none;
                    border-radius:6px; cursor:pointer;">OK</button>
            </div>
        `;
                container.appendChild(box);
                box.querySelector('#confirm-ok').addEventListener('click', () => box.remove());
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

    <script>
        document.getElementById('warehouseListFilterForm').addEventListener('submit', function (e) {
            // Bo‘sh input/selectlarni olib tashlaymiz
            this.querySelectorAll('input[name^="filters"], select[name^="filters"]').forEach(input => {
                if (!input.value || !input.value.trim()) {
                    input.removeAttribute('name'); // name olib tashlanadi
                }
            });
        });
    </script>

</x-backend.layouts.main>
