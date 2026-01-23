<x-backend.layouts.main title="Навбатдаги буюртмалар">

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

        .card-stats.new { background: linear-gradient(135deg, #00b894 30%, #2ecc71 90%); border-left: 5px solid #00d68f; }
        .card-stats.inprogress { background: linear-gradient(135deg, #0984e3 30%, #0984e3 90%); border-left: 5px solid #00a8ff; }
        .card-stats.done { background: linear-gradient(135deg, #6c5ce7 30%, #5a4fd4 90%); border-left: 5px solid #8e76ff; }
        .card-stats.canceled { background: linear-gradient(135deg, #fd79a8 30%, #e84393 90%); border-left: 5px solid #ff6b81; }

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

    <style>
        .custom-badge {
            display: flex;
            justify-content: center;
            align-items: center;
            padding-left: 7.25rem !important;
            padding-right: 7.25rem !important;
            font-size: 0.75rem;
            font-weight: 500;
            text-align: center;
            margin: 0 auto;
        }

        @media (max-width: 768px) {
            .custom-badge {
                padding-left: 2rem;
                padding-right: 2rem;
                font-size: 0.75rem;
            }
        }
    </style>

    <div class="row">
        <div class="card shadow w-100">
            <div class="card-header">
                <div class="row justify-content-start">
                    <div class="col-sm-12 col-md-auto text-start">
                        <x-backend.action route="pre-order" :back="true" :create="true" createLabel="+ Янги буюртма"/>
                    </div>
                </div>
            </div>
            <div class="card-body table-responsive">
                <form id="preOrderFilterForm" method="GET" action="{{ route('pre-order.index') }}">
                    <div class="table-responsive d-none d-md-block">
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr class="text-center">
                                <th class="col-id">{!! sortLink('id', 'Id') !!}</th>
                                <th>{!! sortLink('user_id', 'Клиент') !!}</th>
                                <th class="col-title">{!! sortLink('title', 'Номи') !!}</th>
                                <th>{!! sortLink('count', 'Микдори') !!}</th>
                                <th>{!! sortLink('customer_id', 'Буйуртмачи') !!}</th>
                                <th>{!! sortLink('status', 'Статус') !!}</th>
                                <th>{!! sortLink('created_at', 'Яратилди') !!}</th>
                                <th></th>
                            </tr>
                            {{-- Filter Inputs --}}
                            <tr>
                                <th><input type="text" name="filters[id]" value="{{ request('filters.id') }}"
                                           class="form-control form-control-sm w-100 filter-numeric"></th>
                                <th>
                                    <select name="filters[user_id]"
                                            class="form-control form-control-sm filter-select2 w-100 mb-4">
                                        <option value="">Барчаси</option>
                                        @foreach($users as $id => $username)
                                            <option
                                                value="{{ $id }}" {{ request('filters.user_id') == $id ? 'selected' : '' }}>
                                                {{ $username }}
                                            </option>
                                        @endforeach
                                    </select>
                                </th>
                                <th><input type="text" name="filters[title]" value="{{ request('filters.title') }}"
                                           class="form-control form-control-sm w-100"></th>
                                <th><input type="text" name="filters[count]" value="{{ request('filters.count') }}"
                                           class="form-control form-control-sm w-100 filter-numeric-decimal"></th>
                                <th>
                                    <select name="filters[customer_id]"
                                            class="form-control form-control-sm filter-select2 w-100 mb-4">
                                        <option value="">Барчаси</option>
                                        @foreach($customers as $id => $customername)
                                            <option
                                                value="{{ $id }}" {{ request('filters.customer_id') == $id ? 'selected' : '' }}>
                                                {{ $customername }}
                                            </option>
                                        @endforeach
                                    </select>
                                </th>
                                <th>
                                    <select name="filters[status]" class="form-control form-control-sm w-100">
                                        <option value="">Барчаси</option>
                                        @foreach(\App\Models\PreOrder::getStatusList() as $key => $label)
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
                                <th>
                                    <button type="submit" class="btn btn-sm btn-primary w-100" title="Қидириш"><i
                                            class="fa fa-search"></i></button>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($pre_orders as $order)
                                <tr class="text-center" id="row-{{ $order->id }}">
                                    <td>{{ $order->id }}</td>
                                    <td class="col-title">{{ $order->user->username }}</td>
                                    <td>{{ $order->title }}</td>
                                    <td>{{ number_format($order->count, 0, '', ' ') }} хил</td>
                                    <td class="col-title">{{ $order->customer->username }}</td>
                                    <td class="status-cell">
                                        <span class="{{ $order->status_badge_class }}">
                                            {{ \App\Models\PreOrder::getStatusList()[$order->status] ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="col-date">{{ $order->created_at?->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <x-backend.action route="pre-order" listRoute="pre-order-item" subRoute="items"
                                                          :list="true" :id="$order->id" :view="true" :edit="true"
                                                          listTitle="Буюртма элементлнарини кўриш"
                                                          viewClass="btn btn-secondary btn-sm"
                                        />
                                        @if($order->status != \App\Models\PreOrder::STATUS_DONE)
                                            <button type="button" class="btn btn-success btn-sm complete-btn"
                                                    data-id="{{ $order->id }}">Якунлаш
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center p-4">Маълумот топилмади</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobile version --}}
                    <div class="d-md-none">
                        <div class="d-flex mb-2">
                            <select name="filters[user_id]"
                                    class="form-control form-control-sm filter-select2 w-100 mb-4" data-placeholder="Клиентни танланг">
                                <option value="">Барчаси</option>
                                @foreach($users as $id => $username)
                                    <option value="{{ $id }}" {{ request('filters.user_id') == $id ? 'selected' : '' }}>
                                        {{ $username }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-sm btn-outline-info" title="Қидириш">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>

                        @forelse($pre_orders as $order)
                            <div class="card border shadow-sm">
                                <div class="card-body">
                                    <p class="card-text"><strong>{!! sortLink('id', 'ID:') !!} </strong>{{ $order->id }}
                                    </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('user_id', 'Клиент:') !!} </strong>{{ $order->user->username }}
                                    </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('title', 'Номи:') !!}</strong>{{ $order->title }}
                                    </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('count', 'Микдори:') !!}</strong>{{ number_format($order->count, 0, '', ' ') }} хил
                                    </p>
                                    <p class="card-text">
                                        <strong>{!! sortLink('customer_id', 'Буйуртмачи:') !!} </strong>{{ $order->customer->username }}
                                    </p>
                                    <p class="card-text"><strong>{!! sortLink('status', 'Статус:') !!} </strong>
                                        <span class="{{ $order->status_badge_class }}">
                                            {{ \App\Models\PreOrder::getStatusList()[$order->status] ?? '-' }}
                                        </span>
                                    </p>
                                    <div class="d-flex gap-1">
                                        <x-backend.action route="pre-order" listRoute="pre-order-item" subRoute="items"
                                                          :list="true" :id="$order->id" :view="true" :edit="true"
                                                          listTitle="Буюртма элементлнарини кўриш"
                                                          viewClass="btn btn-secondary btn-sm"
                                        />
                                        @if($order->status != \App\Models\PreOrder::STATUS_DONE)
                                            <button type="button" class="btn btn-success btn-sm complete-btn w-75"
                                                    data-id="{{ $order->id }}">Якунлаш
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-muted py-4">Маълумот топилмади</p>
                        @endforelse
                    </div>
                    {{-- Mobile version end --}}
                </form>

                <div class="d-flex justify-content-center">
                    {{ $pre_orders->links('pagination::bootstrap-4') }}
                </div>

                <div class="d-flex flex-wrap gap-3 mt-4">
                    <!-- Count -->
                    <div class="card-stats new">
                        <div class="w-100">
                            <p>Янги буюртма:<strong></strong></p>
                            <h5>{{ number_format($orderNew, 0, '', ' ') }} та</h5>
                        </div>
                        <div>
                            <i class="bi bi-wallet2"></i>
                        </div>
                    </div>

                    <!-- Total -->
                    <div class="card-stats inprogress">
                        <div class="w-100">
                          <p>Жараёнда:<strong></strong></p>
                          <h5>{{ number_format($orderInprogress, 0, '', ' ') }} сўм</h5>
                        </div>
                        <div>
                            <i class="bi bi-currency-exchange"></i>
                        </div>
                    </div>

                    <!-- Paid -->
                    <div class="card-stats done">
                        <div class="w-100">
                          <p>Якунланган:<strong></strong></p>
                          <h5>{{ number_format($orderDone, 0, '', ' ') }} сўм</h5>
                        </div>
                        <div>
                            <i class="bi bi-currency-euro"></i>
                        </div>
                    </div>

                    <!-- Debt -->
                    <div class="card-stats canceled">
                        <div class="w-100">
                          <p>Бекор килинган:<strong></strong></p>
                          <h5>{{ number_format($orderCanceled, 0, '', ' ') }} сўм</h5>
                        </div>
                        <div>
                            <i class="bi bi-currency-pound"></i>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        document.getElementById('preOrderFilterForm').addEventListener('submit', function () {
            // Faqat ko‘rinib turgan selectni qoldiramiz
            this.querySelectorAll('input[name="filters[user_id]"], select[name="filters[user_id]"]').forEach(select => {
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

    <!-- Complete button JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // ================================
            // ✅ COMPLETE BUTTON INIT
            // ================================
            function initCompleteButtons() {
                document.querySelectorAll('.complete-btn').forEach(btn => {
                    if (btn.dataset.bound) return;
                    btn.dataset.bound = true;

                    btn.addEventListener('click', function (e) {
                        e.preventDefault();
                        const button = this;

                        // ✅ Custom Confirm UI
                        showCustomConfirm('Навбатдаги буюртма якунлансинми?', () => {
                            processCompleteOrder(button);
                        });
                    });
                });
            }

            // ================================
            // ✅ COMPLETE ORDER AJAX LOGIC
            // ================================
            function processCompleteOrder(button) {
                const preOrderId = button.dataset.id;

                fetch(`/admin/pre-order/${preOrderId}/complete`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({})
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {

                            // ✅ Desktop table row update
                            const row = document.querySelector(`#row-${preOrderId}`);
                            if (row) {
                                const statusCell = row.querySelector('.status-cell');
                                if (statusCell) {
                                    statusCell.innerHTML = '<span class="badge bg-success">Якунланди</span>';
                                }

                                const completeBtn = row.querySelector('.complete-btn');
                                if (completeBtn) completeBtn.remove();
                            }

                            // ✅ Mobile card update
                            const cardBody = button.closest('.card-body');
                            if (cardBody) {
                                const badge = document.createElement('span');
                                badge.className = "badge bg-success custom-badge";
                                badge.innerText = "Якунланди";
                                button.replaceWith(badge);
                            }

                            // ✅ Flash alert
                            showFlashAlert('success', 'Буюртма муваффақиятли якунланди!');

                        } else {
                            showFlashAlert('error', 'Хатолик юз берди!');
                        }
                    })
                    .catch(() => showFlashAlert('error', 'Хатолик юз берди!'));
            }

            initCompleteButtons();

            // ================================
            // ✅ CUSTOM CONFIRM MODAL
            // ================================
            function showCustomConfirm(message, onConfirm) {
                const container = document.getElementById('custom-confirm-container');
                const confirmBox = document.createElement('div');

                const isMobile = window.innerWidth <= 768;

                confirmBox.className = 'custom-confirm-overlay';
                confirmBox.style.cssText = `
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.5);
            display: flex;
            justify-content: center;
            align-items: ${isMobile ? 'flex-start' : 'center'};
            ${isMobile ? 'padding-top: 60px;' : ''}
            z-index: 9999;
        `;

                confirmBox.innerHTML = `
            <div style="
                background: #fff;
                padding: 22px 28px;
                border-radius: 12px;
                width: 90%;
                max-width: 380px;
                text-align: center;
                box-shadow: 0 8px 20px rgba(0,0,0,0.25);
                animation: fadeIn 0.25s ease-out;
            ">
                <p style="margin-bottom: 20px; font-size: 1.15rem; font-weight: 500;">
                    ${message}
                </p>
                <div style="display: flex; justify-content: center; gap: 12px;">
                    <button id="confirm-yes" style="
                        background: linear-gradient(135deg, #38b000, #70e000);
                        color: #fff;
                        padding: 8px 18px;
                        border: none;
                        border-radius: 8px;
                        cursor: pointer;
                        font-size: 1rem;
                        min-width: 80px;
                    ">Ҳа</button>

                    <button id="confirm-no" style="
                        background: linear-gradient(135deg, #ff3c38, #ff7b6a);
                        color: #fff;
                        padding: 8px 18px;
                        border: none;
                        border-radius: 8px;
                        cursor: pointer;
                        font-size: 1rem;
                        min-width: 80px;
                    ">Йўқ</button>
                </div>
            </div>
        `;

                container.appendChild(confirmBox);

                // ✅ Yes
                confirmBox.querySelector('#confirm-yes').addEventListener('click', () => {
                    confirmBox.remove();
                    onConfirm();
                });

                // ✅ No
                confirmBox.querySelector('#confirm-no').addEventListener('click', () => {
                    confirmBox.remove();
                });
            }

            // ================================
            // ✅ FLASH ALERT
            // ================================
            function showFlashAlert(type, message) {
                const container = document.getElementById('flash-messages');

                const alert = document.createElement('div');
                alert.className = 'flash-alert';
                alert.style.cssText = `
            min-width: 220px;
            max-width: 420px;
            padding: 0.85rem 1.25rem;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.25);
            transform: translateX(120%);
            color: #fff;
            font-weight: 500;
            letter-spacing: 0.3px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: ${getAlertGradient(type)};
            margin-top: 10px;
        `;

                alert.innerHTML = `
            <span style="flex: 1;">${message}</span>
            <button type="button" class="btn-close"
                style="width: 26px; height: 26px; opacity: .8;"></button>
        `;

                container.appendChild(alert);

                setTimeout(() => {
                    alert.style.transition = 'transform .6s ease, opacity .6s ease';
                    alert.style.transform = 'translateX(0)';
                }, 80);

                // Auto close
                setTimeout(() => {
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateX(120%)';
                    setTimeout(() => alert.remove(), 500);
                }, 2500);

                // Manual close
                alert.querySelector('.btn-close').addEventListener('click', () => alert.remove());
            }

            // ================================
            // ✅ FLASH GRADIENTS
            // ================================
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
                        return '#333';
                }
            }
        });
    </script>

</x-backend.layouts.main>
