@php
    use App\Services\StatusService;
    use App\Helpers\PriceHelper;
    use App\Helpers\CountHelper;
@endphp

<x-backend.layouts.main
    title="{!! 'Қайтиш ( ' . ucfirst($productReturn->title) . '):' !!}">

    <div class="row">
        <div class="card shadow w-100">
            <div class="table-responsive card-body">
                <form id="productVariationListFilterForm" method="GET"
                      action="{{ route('product-return-item.list', $productReturn) }}">
                    <div class="table-responsive d-none d-md-block">
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr class="text-center">
                                <th class="col-id">{!! sortLink('id', 'Id') !!}</th>
                                <th>{!! sortLink('product_variation_id', 'Номи') !!}</th>
                                <th>{!! sortLink('price', 'Нархи') !!}</th>
                                <th>{!! sortLink('count', 'Миқдори') !!}</th>
                                <th>{!! sortLink('total_price', 'Умумий(сўм)') !!}</th>
                                <th>{!! sortLink('created_at', 'Яратилди') !!}</th>
                                <th></th> {{-- Search btn --}}
                            </tr>
                            {{-- Filter Inputs --}}
                            <tr>
                                <th><input type="text" name="filters[id]" value="{{ request('filters.id') }}"
                                           class="form-control form-control-sm w-100 filter-numeric"></th>
                                <th>
                                    <select name="filters[product_variation_id]"
                                            class="form-control form-control-sm filter-select2 w-100">
                                        <option value="">Барчаси</option>
                                        @foreach($productVariations as $id => $title)
                                            <option
                                                value="{{ $id }}" {{ request('filters.product_variation_id') == $id ? 'selected' : '' }}>
                                                {{ $title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </th>
                                <th><input type="text" name="filters[price]" value="{{ request('filters.price') }}"
                                           class="form-control form-control-sm w-100 filter-numeric"></th>
                                <th><input type="text" name="filters[count]" value="{{ request('filters.count') }}"
                                           class="form-control form-control-sm w-100 filter-numeric"></th>
                                <th><input type="text" name="filters[total_price]"
                                           value="{{ request('filters.total_price') }}"
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
                                    <button type="submit" class="btn btn-sm btn-primary w-100" title="Қидириш"><i class="fa fa-search"></i></button>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($productReturnItems as $returnItem)
                                <tr class="text-center" id="row-desktop-{{ $returnItem->id }}">
                                    <td class="col-id">{{ $returnItem->id }}</td>
                                    <td>{{ optional($returnItem->variation)->title }}</td>
                                    <td class="price fw-bold text-success">
                                        {{ PriceHelper::format($returnItem->price, $returnItem->productReturn->currency, false) }}
                                    </td>
                                    <td class="count fw-bold text-primary">
                                        {{ CountHelper::format($returnItem->count, $returnItem->variation->unit, false) }}
                                    </td>
                                    <td class="total_price fw-bold text-info text-nowrap">
                                        {{ PriceHelper::format($returnItem->total_price, $returnItem->productReturn->currency, false) }}
                                    </td>
                                    <td>{{ $returnItem->created_at?->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <x-backend.action route="product-return-item" :id="$returnItem->id" :view="true"/>
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
                                   class="form-control form-control-sm me-1" placeholder="Элемент номини киритинг">
                            <button type="submit" class="btn btn-sm btn-outline-info" title="Қидириш">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                        @forelse($productReturnItems as $returnItem)
                            <div class="card border" id="row-mobile-{{ $returnItem->id }}">
                                <div class="card-body">
                                    <p class="card-text">
                                        <strong>{!! sortLink('id', 'ID:') !!}</strong> {{ $returnItem->id }}</p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('title', 'Номи:') !!}</strong> {{ optional($returnItem->variation)->title }}</p>
                                    <p class="card-text"><strong>{!! sortLink('price', 'Нархи:') !!} </strong><span
                                            class="price fw-bold text-info">{{ PriceHelper::format($returnItem->price, $returnItem->productReturn->currency, false) }}</span> {{ StatusService::getCurrency()[$returnItem->productReturn->currency] }}
                                    </p>
                                    <p class="card-text"><strong>{!! sortLink('count', 'Миқдори:') !!} </strong><span
                                            class="count fw-bold text-primary">{{ CountHelper::format($returnItem->count, $returnItem->variation->unit, false) }}</span> {{ StatusService::getTypeCount()[$returnItem->variation->unit] }}
                                    </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('total_price', 'Умумий:') !!} </strong><span
                                            class="total_price fw-bold text-info">{{ PriceHelper::format($returnItem->total_price, $returnItem->productReturn->currency, false) }}</span> {{ StatusService::getCurrency()[$returnItem->productReturn->currency] }}
                                        </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('created_at', 'Яратилди:') !!}</strong> {{ $returnItem->created_at?->format('Y-m-d H:i') }}
                                    </p>
                                    <div class="btn-group w-100">
                                        <x-backend.action route="product-return-item" :id="$returnItem->id" :view="true"/>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-center">Маълумот топилмади</p>
                        @endforelse
                    </div>
                    {{-- Mobile version end --}}
                </form>

                {{-- Pagination --}}
                <div class="d-flex justify-content-center mt-3">
                    {{ $productReturnItems->links('pagination::bootstrap-4') }}
                </div>

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

                    .card-stats.count { background: linear-gradient(135deg, #00b894 30%, #2ecc71 90%); border-left: 5px solid #00d68f; }
                    .card-stats.total { background: linear-gradient(135deg, #0984e3 30%, #0984e3 90%); border-left: 5px solid #00a8ff; }

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
                <div class="d-flex flex-wrap gap-3 mt-4">
                    <!-- Count -->
                    <div class="card-stats count">
                        <div class="w-100">
                            <p>Маҳсулотлар:</p>
                            <h5><strong>{{ number_format($allCount, 0, '', ' ') }} та</strong></h5>
                        </div>
                        <div>
                            <i class="bi bi-wallet2"></i>
                        </div>
                    </div>
                    <!-- TotalPrice -->
                    <div class="card-stats total">
                        <div class="w-100">
                            <p>Умумий сумма</p>
                            <h5><strong>{{ number_format($totalAmount ?? 0, 0, '', ' ') }} сўм</strong></h5>
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
        document.getElementById('productVariationListFilterForm').addEventListener('submit', function (e) {
            // Faqat ko‘rinib turgan selectni qoldiramiz
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
                    const count = parseFloat(this.dataset.count);
                    const typeCount = parseInt(this.dataset.unit || 1, 10);

                    document.getElementById('variation_id').value = id;
                    document.getElementById('variation_title').value = title;
                    document.getElementById('current_count').value = Number(count).toLocaleString('ru-RU', {
                        minimumFractionDigits: typeCount === 1 ? 0 : 3,
                        maximumFractionDigits: typeCount === 1 ? 0 : 3
                    });
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
                const addCount = parseInt(document.getElementById('add_count').value, 10);
                const typeCount = parseInt(document.querySelector('.add-count-btn[data-id="'+id+'"]').dataset.unit || 1, 10);

                if (isNaN(addCount) || addCount < 1) {
                    showCustomConfirm('Илтимос, маҳсулот миқдорини киритинг!', 'warning');
                    return;
                }

                try {
                    const res = await fetch(`/admin/product-variation/${id}/add-count`, {
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
                        // 🔹 Jadvaldagi qiymatlarni yangilash (desktop va mobile)
                        ['desktop', 'mobile'].forEach(prefix => {
                            const row = document.getElementById(`row-${prefix}-${id}`);
                            if (row) {
                                const countEl = row.querySelector('.count');
                                const totalEl = row.querySelector('.total_price');

                                if (countEl) {
                                    countEl.innerText = Number(data.new_count).toLocaleString('ru-RU', {
                                        minimumFractionDigits: typeCount === 1 ? 0 : 3,
                                        maximumFractionDigits: typeCount === 1 ? 0 : 3
                                    });
                                }
                                if (totalEl) totalEl.innerText = Number(data.new_total_price).toLocaleString('ru-RU');

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
                            Қўшилди: <b>${addCount}</b> дона.<br>
                            Жами: <b>${data.count || data.new_count || 0}</b> дона.
                        </span>
                    `, 'success');
                        }, 300);

                        const somCountEl = document.getElementById('stat-som-count');
                        const dollarCountEl = document.getElementById('stat-dollar-count');
                        const totalPriceEl = document.getElementById('stat-total-price');

                        if (somCountEl) somCountEl.innerText = Number(data.all_som_count).toLocaleString('ru-RU') + ' та';
                        if (dollarCountEl) dollarCountEl.innerText = Number(data.all_dollar_count).toLocaleString('ru-RU') + ' та';
                        if (totalPriceEl) totalPriceEl.innerText = Number(data.total_price).toLocaleString('ru-RU') + ' сўм';

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



