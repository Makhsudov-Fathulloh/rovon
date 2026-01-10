<x-backend.layouts.main :title="'Хомашё трансфер (' . ucfirst($rawMaterialTransfer->title) . ') элементлари.'">

    <div class="row">
        <div class="card shadow w-100">
            <div class="table-responsive card-body">
                <form id="rawMaterialTransferListFilterForm" method="GET"
                      action="{{ route('raw-material-transfer-item.list', $rawMaterialTransfer) }}">
                    <div class="table-responsive d-none d-md-block">
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr class="text-center">
                                <th class="col-id">{!! sortLink('id', 'Id') !!}</th>
                                <th>{!! sortLink('raw_material_variation_id', 'Хомашё') !!}</th>
                                <th>{!! sortLink('price', 'Нархи') !!}</th>
                                <th>{!! sortLink('count', 'Микдори') !!}</th>
                                <th>{!! sortLink('total_price', 'Умумий') !!}</th>
                                <th>{!! sortLink('created_at', 'Яратилди') !!}</th>
                                <th></th> {{-- Search btn --}}
                            </tr>
                            {{-- Filter Inputs --}}
                            <tr>
                                <th><input type="text" name="filters[id]" value="{{ request('filters.id') }}"
                                           class="form-control form-control-sm w-100 filter-numeric"></th>
                                <th>
                                    <select name="filters[raw_material_variation_id]"
                                            class="form-control form-control-sm w-100 filter-select2">
                                        <option value="">Барчаси</option>
                                        @foreach($rawMaterialVariation as $id => $title)
                                            <option
                                                value="{{ $id }}" {{ request('filters.raw_material_variation_id') == $id ? 'selected' : '' }}>{{ $title }}</option>
                                        @endforeach
                                    </select>
                                </th>
                                <th><input type="text" name="filters[price]" value="{{ request('filters.price') }}"
                                           class="form-control form-control-sm w-100 filter-numeric"></th>
                                <th><input type="text" name="filters[count]" value="{{ request('filters.count') }}"
                                           class="form-control form-control-sm w-100 filter-numeric filter-numeric-decimal">
                                </th>
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
                                    <button type="submit" class="btn btn-sm btn-primary w-100" title="Қидириш"><i
                                            class="fa fa-search"></i></button>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($rawMaterialTransferItems as $item)
                                <tr class="text-center" id="row-desktop-{{ $item->id }}">
                                    <td class="col-id">{{ $item->id }}</td>
                                    <td>{{ optional($item->rawMaterialVariation)->title }}</td>
                                    <td class="price fw-bold text-success tex">
                                        {{ \App\Helpers\PriceHelper::format($item->price, $item->rawMaterialVariation->currency) }}
                                    </td>
                                    <td class="count fw-bold text-primary">
                                        {{ \App\Helpers\CountHelper::format($item->count, $item->unit) }}
                                    </td>
                                    <td class="total_price fw-bold text-info text-nowrap">
                                        {{ \App\Helpers\PriceHelper::format($item->total_price, $item->rawMaterialVariation->currency) }}
                                    </td>
                                    <td>{{ $item->created_at?->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <a href="{{ route('raw-material-transfer-item.show', $item->id) }}"
                                           class="btn btn-info btn-sm" title="Кўриш">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a href="{{ route('raw-material-transfer.edit', $item->rawMaterialTransfer->id) }}"
                                           class="btn btn-warning btn-sm" title="Таҳрирлаш"> <i class="fa fa-edit"></i>
                                        </a>
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
                            <select name="filters[raw_material_variation_id]"
                                    class="form-control form-control-sm w-100 filter-select2" data-placeholder="Трансфер номини киритинг">>
                                <option value="">Хомашё номини киритинг</option>
                                @foreach($rawMaterialVariation as $id => $title)
                                    <option
                                        value="{{ $id }}" {{ request('filters.raw_material_variation_id') == $id ? 'selected' : '' }}>{{ $title }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-sm btn-outline-info" title="Қидириш">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                        @forelse($rawMaterialTransferItems as $item)
                            <div class="card border" id="row-mobile-{{ $item->id }}">
                                <div class="card-body">
                                    @if(optional($item->file)->path)
                                        <div class="text-center mb-2">
                                            <img src="{{ asset('storage/' . $item->file->path) }}"
                                                 alt="Image" class="img-fluid" style="max-width: 256px;">
                                        </div>
                                    @endif
                                    <p class="card-text">
                                        <strong>{!! sortLink('id', 'ID:') !!}</strong> {{ $item->id }}</p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('raw_material_variation_id', 'Хомашё:') !!} </strong>{{ optional($item->rawMaterialVariation)->title }}
                                    </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('price', 'Нархи:') !!}</strong>
                                        <span
                                            class="price fw-bold text-success">{{ \App\Helpers\PriceHelper::format($item->price, $item->rawMaterialVariation->currency) }}</span>
                                    </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('count', 'Сони:') !!}</strong>
                                        <span
                                            class="count fw-bold text-primary">{{ \App\Helpers\CountHelper::format($item->count, $item->unit) }}</span>
                                    </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('total_price', 'Умумий:') !!}</strong>
                                        <span
                                            class="total_price fw-bold text-info">{{ \App\Helpers\PriceHelper::format($item->total_price, $item->rawMaterialVariation->currency) }}</span>
                                    </p>

                                    <div class="btn-group w-100">
                                        <a href="{{ route('raw-material-transfer-item.show', $item->id) }}"
                                           class="btn btn-info btn-sm" title="Кўриш">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a href="{{ route('raw-material-transfer.edit', $item->rawMaterialTransfer->id) }}"
                                           class="btn btn-warning btn-sm" title="Таҳрирлаш"> <i class="fa fa-edit"></i>
                                        </a>
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
                    {{ $rawMaterialTransferItems->links('pagination::bootstrap-4') }}
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
                   }
                   .card-stats:hover {
                       transform: translateY(-5px);
                       box-shadow: 0 12px 24px rgba(0,0,0,0.3);
                   }
                  .card-stats.uzs {
                       background: linear-gradient(135deg, #00b894 35%, #2ecc71 65%);
                       border-left: 5px solid #00d68f;
                   }

                   .card-stats.usd {
                       background: linear-gradient(135deg, #0984e3 35%, #0984e3 65%);
                       border-left: 5px solid #00a8ff;
                    }

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
               <div class="row mt-4">
                   <div class="col-md-6 mb-3">
                       <div class="card-stats uzs">
                           <div class="w-100">
                               <h5>🇺🇿 UZS</h5>
                               <p>Хомашёлар: <strong>{{ number_format($allCountUzs, 0, '', ' ') }} та</strong></p>
                               <p>Умумий сумма: <strong>{{ number_format($totalPriceUzs, 0, '', ' ') }} сўм</strong></p>
                           </div>
                           <div>
                               <i class="bi bi-wallet2"></i>
                           </div>
                       </div>
                   </div>
                   <div class="col-md-6 mb-3">
                       <div class="card-stats usd">
                           <div class="w-100">
                               <h5>🇺🇸 USD</h5>
                               <p>Хомашёлар: <strong>{{ number_format($allCountUsd, 0, '', ' ') }} та</strong></p>
                               <p>Умумий сумма: <strong>{{ number_format($totalPriceUsd, 2, '.', ' ') }} $</strong></p>
                           </div>
                           <div>
                               <i class="bi bi-currency-exchange"></i>
                           </div>
                       </div>
                   </div>
               </div>

            </div>
        </div>
    </div>

    <script>
        document.getElementById('rawMaterialTransferListFilterForm').addEventListener('submit', function (e) {
            // Faqat ko‘rinib turgan selectni qoldiramiz
            this.querySelectorAll('select[name="filters[raw_material_variation_id]"]').forEach(select => {
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

                    document.getElementById('variation_id').value = id;
                    document.getElementById('variation_title').value = title;
                    document.getElementById('current_count').value = count;
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

                if (isNaN(addCount) || addCount < 1) {
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
                                if (countEl) countEl.innerText = Number(data.new_count).toLocaleString('ru-RU');
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
