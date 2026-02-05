@php
    use \App\Services\StatusService;
@endphp

<style>
    .debt-switch {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .debt-switch input {
        display: none;
    }

    .debt-switch label {
        cursor: pointer;
        user-select: none;
    }

    .debt-switch .switch {
        width: 44px;
        height: 24px;
        background: #dee2e6;
        border-radius: 20px;
        position: relative;
        transition: background .25s;
    }

    .debt-switch .switch::after {
        content: '';
        width: 18px;
        height: 18px;
        background: #fff;
        border-radius: 50%;
        position: absolute;
        top: 3px;
        left: 3px;
        transition: transform .25s;
        box-shadow: 0 2px 6px rgba(0,0,0,.2);
    }

    .debt-switch input:checked + label .switch {
        background: #198754;
    }

    .debt-switch input:checked + label .switch::after {
        transform: translateX(20px);
    }

    .debt-switch .text {
        font-weight: 600;
        color: #212529;
    }
</style>

<form id="order-form" action="{{ $action }}" method="POST">
    @csrf
    @if ($method === 'PUT')
        @method('PUT')
    @endif

    <div class="container-fluid mt-2">

        {{-- <div class="mb-2">
            <x-backend.action :back="true"/>
        </div> --}}

        <div class="card shadow">
            <div class="card-body">

                <div class="col-md-11 mb-3">
                    <label for="user_id">Клиент</label>
                    <div class="d-flex gap-2">
                        <select name="user_id" id="user_id"
                                class="form-control filter-select2" data-placeholder="Клиентни танланг" {{ Route::is('order.create') ? 'required' : '' }}>
                            <option value="">Клиентни танланг</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}"
                                    {{ old('user_id', $order->user_id ?? $defaultUserId) == $user->id ? 'selected' : '' }}>
                                    {{ $user->username }} ({{ \App\Services\PhoneFormatService::uzPhone($user->phone) }})
                                </option>
                            @endforeach
                        </select>

                        <button type="button"
                                class="btn btn-success d-inline-flex align-items-center justify-content-center"
                                style="height: 28px; white-space: nowrap; padding: 0 10px; font-size: 14px;"
                                data-bs-toggle="modal"
                                data-bs-target="#createClientModal">
                            <i class="fas fa-plus me-1" style="font-size: 10px;"></i> Клиент
                        </button>
                    </div>
                </div>

                <div class="col-md-11 mb-3">
                    <label for="status">Статус</label>
                    <select id="status" name="status" class="form-control" required>
                        @foreach(\App\Models\Order::getStatusList() as $key => $label)
                            <option
                                value="{{ $key }}" {{ old('status', $order->status ?? '') == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-11 mb-4">
                    <label for="currency">Валюта</label>
                    <select id="currency" name="currency" class="form-control" onchange="changeCurrency()" required>
                        @foreach(StatusService::getCurrency() as $key => $label)
                            <option value="{{ $key }}" {{ $currentCurrency == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <label class="mb-3">Маҳсулотлар</label>
                <div id="items">
                    @foreach($oldItems as $i => $item)
                        @php
                            $variationId = is_array($item) ? ($item['product_variation_id'] ?? null) : $item->product_variation_id;
                            $selectedVariation = $variations->firstWhere('id', $variationId);

                            // ✅ Narx qiymatini to‘g‘ri aniqlash (old > order item > model)
                            $priceValue = old("items.$i.price");

                            if (!$priceValue) {
                                if (is_array($item)) {
                                    $priceValue = $item['price'] ?? '';
                                } else {
                                    if ($currentCurrency == StatusService::CURRENCY_UZS) {
                                        $priceValue = number_format($item->price ?? 0, 0, '', ' ');
                                    } else {
                                        $priceValue = number_format($item->price ?? 0, 2, '.', ' ');
                                    }
                                }
                            }
                        @endphp

                        <div class="item row mb-3" id="item-{{ $i }}">
                            <div class="col-md-6">
                                <select name="items[{{ $i }}][product_variation_id]" class="form-control filter-select2" data-placeholder="Маҳсулотни танланг"
                                        onchange="updatePrice(this, {{ $i }})" required>
                                    <option value="">Маҳсулотни танланг</option>
                                    @foreach($variations as $variation)
                                        <option value="{{ $variation->id }}" data-price="{{ $variation->price }}"
                                            {{ (int)$variationId === (int)$variation->id ? 'selected' : '' }}>
                                            {!! $variation->title !!} — {!! $variation->product->title !!}
                                            ({{ number_format($variation->price, 0, '', ' ') }} сўм)
                                            [{{ \App\Helpers\CountHelper::format($variation->count, $variation->unit) }}]
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <input type="text" name="items[{{ $i }}][quantity]" class="form-control filter-numeric-decimal"
                                       placeholder="Сони"
                                       value="{{ old("items.$i.quantity", is_array($item) ? ($item['quantity'] ?? 1) : ($item->quantity ?? 1)) }}"
                                       oninput="calculateTotal()" required>
                            </div>

                            <div class="col-md-3">
                                <input type="text" name="items[{{ $i }}][price]"
                                       class="form-control price-input"
                                       placeholder="Сотиш нархи"
                                       value="{{ $priceValue }}"
                                       data-original-price="{{ $selectedVariation->price ?? 0 }}"
                                       oninput="validatePrice(this); calculateTotal()" required>
                            </div>

                            @if($i > 0)
                                <div class="col-md-1">
                                    <button type="button" class="btn btn-sm" onclick="removeItem({{ $i }})">
                                        ❌
                                    </button>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <button type="button" class="btn btn-sm btn-secondary mb-4" onclick="addItem()">+ Қўшиш</button>

                <div class="mb-3">
                    <div class="mb-2 debt-switch d-flex align-items-center gap-2">
                        <label for="total_price" class="mb-0">Жами сумма (<span id="currency-label">{{ $currencyLabel }}</span>)</label>
                        <div class="debt-switch">
                            <div class="d-flex align-items-center gap-2">
                                <input type="checkbox" id="allowZeroPayment">
                                <label for="allowZeroPayment" class="d-flex align-items-center gap-2 mb-0">
                                    <span class="switch"></span>
                                </label>
                            </div>
                            <span class="text">Қарздорлик</span>
                        </div>
                    </div>
                    <div>
                        <input type="text" id="total_price" name="total_price" class="form-control" value="{{ $totalPriceValue }}" readonly>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <label>Нақд</label>
                        <input type="text" id="cash_paid" name="cash_paid" class="form-control filter-numeric-decimal"
                               value="{{ $cashPaidValue }}" oninput="calculateRemainingDebt()">
                    </div>
                    <div class="col-md-3">
                        <label>Карта</label>
                        <input type="text" id="card_paid" name="card_paid" class="form-control filter-numeric-decimal"
                               value="{{ $cardPaidValue }}" oninput="calculateRemainingDebt()">
                    </div>
                    <div class="col-md-3">
                        <label>Ўтказма</label>
                        <input type="text" id="transfer_paid" name="transfer_paid"
                               class="form-control filter-numeric-decimal"
                               value="{{ $transferPaidValue }}" oninput="calculateRemainingDebt()">
                    </div>
                    <div class="col-md-3">
                        <label>Ҳисоб рақам</label>
                        <input type="text" id="bank_paid" name="bank_paid" class="form-control filter-numeric-decimal"
                               value="{{ $bankPaidValue }}" oninput="calculateRemainingDebt()">
                    </div>
                </div>

                <div class="mb-3">
                    <label>Жами тўланган (<span id="currency-label-paid">{{ $currencyLabel }}</span>)</label>
                    <input type="text" id="total_amount_paid" name="total_amount_paid" class="form-control"
                           value="{{ $totalPaidValue }}" readonly>
                </div>

                <div class="mb-3">
                    <label>Қолдиқ қарз (<span id="currency-label-debt">{{ $currencyLabel }}</span>)</label>
                    <input type="text" id="remaining_debt" name="remaining_debt" class="form-control"
                           value="{{ $remainingDebtValue }}" readonly>
                </div>

                <button type="submit" class="btn btn-primary">
                    {{ Route::currentRouteName() == 'order.edit' ? 'Янгилаш' : 'Сақлаш' }}
                </button>
            </div>
        </div>
    </div>
</form>

<script>
    let itemIndex = $('#items .item').length;

    function addItem() {
        const selectedIds = getSelectedVariationIds();
        let optionsHtml = `<option value="">Маҳсулотни танланг</option>`;

        @foreach($variations as $variation)
        optionsHtml += `<option value="{{ $variation->id }}" data-price="{{ $variation->price }}"
            ${selectedIds.includes('{{ $variation->id }}') ? 'display' : ''}>
        {!! $variation->title !!} — {!! $variation->product->title !!}
        ({{ number_format($variation->price, 0, '', ' ') }} сўм)
            [{{ \App\Helpers\CountHelper::format($variation->count, $variation->unit) }}]
            </option>`;
        @endforeach

        const itemHtml = `
        <div class="item row mb-3" id="item-${itemIndex}">
            <div class="col-md-6">
                <select name="items[${itemIndex}][product_variation_id]" class="form-control filter-select2"
                        onchange="updatePrice(this, ${itemIndex})" required>
                    ${optionsHtml}
                </select>
            </div>
            <div class="col-md-2">
                <input type="text" name="items[${itemIndex}][quantity]" class="form-control filter-numeric-decimal"
                       placeholder="Сони" value="1" oninput="calculateTotal()" required>
            </div>
            <div class="col-md-3">
                <input type="text" name="items[${itemIndex}][price]" class="form-control price-input"
                       placeholder="Сотиш нархи" data-original-price="" value=""
                       oninput="validatePrice(this); calculateTotal()" required>
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-sm" onclick="removeItem(${itemIndex})">❌</button>
            </div>
        </div>`;

        $('#items').append(itemHtml);
        $('#items .item:last .filter-select2').select2({width: '100%'});

        // Yangi select’dagi variantlarni yangilash
        updateVariationOptions();

        itemIndex++;
        calculateTotal();
    }

    function removeItem(index) {
        $(`#item-${index}`).remove();
        calculateTotal();
    }

    // 🔹 Variation takrorlamaslik
    function getSelectedVariationIds() {
        let ids = [];
        document.querySelectorAll('.item select[name*="[product_variation_id]"]').forEach(select => {
            const val = select.value;
            if (val) ids.push(val);
        });
        return ids;
    }

    // function updateVariationOptions() {
    //     const selectedIds = getSelectedVariationIds();
    //     document.querySelectorAll('.item select[name*="[product_variation_id]"]').forEach(select => {
    //         const currentVal = select.value;
    //         select.querySelectorAll('option').forEach(opt => {
    //             if (opt.value && opt.value !== currentVal) {
    //                 opt.disabled = selectedIds.includes(opt.value);
    //             } else {
    //                 opt.disabled = false;
    //             }
    //         });
    //         $(select).select2(); // select2 update
    //     });
    // }

    function updateVariationOptions() {
        const selectedIds = getSelectedVariationIds();

        document.querySelectorAll('.item select[name*="[product_variation_id]"]').forEach(select => {
            const currentVal = select.value;

            // Eski variantlarni tiklash uchun — barcha <option>ni qayta tiklaymiz
            const allOptions = [];

            @foreach($variations as $variation)
            allOptions.push({
                id: '{{ $variation->id }}',
                // code: '{{ $variation->code }}',
                title: '{{ $variation->product->title }} → {{ $variation->title }}',
                price: '{{ $variation->price }}',
                text: `{!! $variation->title !!} — {!! $variation->product->title !!}
                ({{ number_format($variation->price, 0, '', ' ') }} сўм)
                    @if ($variation->unit == StatusService::UNIT_PSC)
                [{{ number_format($variation->count, 0, '', ' ') }} та]
                    @elseIf ($variation->unit == StatusService::UNIT_KG)
                [{{ number_format($variation->count, 3, '.', ' ') }} кг]
                    @elseIf ($variation->unit == StatusService::UNIT_METER)
                [{{ number_format($variation->count, 2, '.', ' ') }} метр]
                    @endif`
            });
            @endforeach

            // Select ichini tozalaymiz
            select.innerHTML = '<option value="">Маҳсулотни танланг</option>';

            // Faqat hozirgi selectdagi tanlovni yoki boshqa tanlanmagan variantlarni qo‘shamiz
            allOptions.forEach(opt => {
                if (!selectedIds.includes(opt.id) || opt.id === currentVal) {
                    const option = document.createElement('option');
                    option.value = opt.id;
                    option.dataset.price = opt.price;
                    option.text = opt.text.replace(/\s+/g, ' ').trim();

                    if (opt.id === currentVal) {
                        option.selected = true;
                    }

                    select.appendChild(option);
                }
            });

            // select2 ni qayta yuklaymiz
            $(select).select2({
                width: '100%'
            });
        });
    }

    const usdRate = parseFloat('{{ \App\Models\ExchangeRates::where("currency", "USD")->value("rate") ?? 1 }}');
    let currentCurrency = '{{ old("currency", $order->currency ?? StatusService::CURRENCY_UZS) }}';

    // 🔹 So‘mdan dollarga
    function toUsd(uzs) {
        if (!usdRate || usdRate <= 0) return 0;
        return uzs / usdRate;
    }

    // 🔹 Dollardan so‘mga
    function toSom(usd) {
        if (!usdRate || usdRate <= 0) return 0;
        return usd * usdRate;
    }

    // 🔹 Raqam qiymatini olish
    function getNumericValue(el) {
        if (!el) return 0;
        let val = (typeof el === 'string' ? $(el).val() : $(el).value || el.value || '').toString().replace(/\s/g, '');
        const num = parseFloat(val);
        return isNaN(num) ? 0 : num;
    }

    // 🔹 Mahsulot narxini yangilash
    function updatePrice(selectElement, index) {
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        const priceSom = parseFloat(selectedOption.dataset.price) || 0;
        const priceInput = document.querySelector(`input[name="items[${index}][price]"]`);
        if (!priceInput) return;

        // faqat birinchi marta original narxni saqlaymiz
        priceInput.dataset.originalPrice = priceSom;

        if (currentCurrency === '{{ StatusService::CURRENCY_USD }}') {
            // priceInput.value = toUsd(priceSom).toFixed(10);
            priceInput.value = toUsd(priceSom).toFixed(3);
        } else {
            priceInput.value = Math.round(priceSom).toLocaleString('ru-RU');
        }
        console.log(priceInput.value);

        validatePrice(priceInput);
        updateVariationOptions();
        calculateTotal();
    }

    // 🔹 Minimal narxni tekshirish
    function validatePrice(input) {
        const minPrice = parseFloat(input.dataset.originalPrice);
        const currentPrice = getNumericValue(input);
        if (!isNaN(minPrice) && currentPrice < minPrice && currentCurrency === '{{ StatusService::CURRENCY_UZS }}') {
            input.style.borderColor = 'red';
        } else {
            input.style.borderColor = '';
        }
    }

    // 🔹 Jami summani hisoblash
    function calculateTotal() {
        let total = 0;
        document.querySelectorAll('.item').forEach(item => {
            const qty = getNumericValue(item.querySelector('input[name*="[quantity]"]'));
            const price = getNumericValue(item.querySelector('.price-input'));
            total += qty * price;
        });

        const totalInput = document.querySelector('#total_price');

        // 🔹 Hech qanday konvertatsiya qilmaymiz, valyutaga qarab faqat formatlaymiz
        if (currentCurrency === '{{ StatusService::CURRENCY_USD }}') {
            totalInput.value = total.toFixed(3);
        } else {
            totalInput.value = Math.round(total).toLocaleString('ru-RU');
        }

        calculateRemainingDebt();
    }

    // 🔹 To‘langan jami
    function calculatePaidTotal() {
        let paid = ['#cash_paid', '#card_paid', '#transfer_paid', '#bank_paid']
            .map(id => getNumericValue(id))
            .reduce((a, b) => a + b, 0);

        const totalPaidInput = document.querySelector('#total_amount_paid');
        if (currentCurrency === '{{ StatusService::CURRENCY_USD }}') {
            totalPaidInput.value = paid.toFixed(3);
        } else {
            totalPaidInput.value = Math.round(paid).toLocaleString('ru-RU');
        }
        console.log(totalPaidInput);
        return paid;
    }

    // 🔹 Qolgan qarzni hisoblash
    // function calculateRemainingDebt() {
    //     const total = getNumericValue('#total_price');
    //     const paid = ['#cash_paid', '#card_paid', '#transfer_paid', '#bank_paid']
    //         .map(id => getNumericValue(id))
    //         .reduce((a, b) => a + b, 0);

    //     const remaining = Math.max(total - paid, 0);
    //     const debtInput = document.querySelector('#remaining_debt');

    //     if (currentCurrency === '{{ StatusService::CURRENCY_USD }}') {
    //         debtInput.value = remaining.toFixed(3);
    //     } else {
    //         debtInput.value = Math.round(remaining).toLocaleString('ru-RU');
    //     }

    //     // Total paid ham shunchaki jami to‘langanlarni hisoblaydi
    //     const totalPaidInput = document.querySelector('#total_amount_paid');
    //     if (currentCurrency === '{{ StatusService::CURRENCY_USD }}') {
    //         totalPaidInput.value = paid.toFixed(3);
    //     } else {
    //         totalPaidInput.value = Math.round(paid).toLocaleString('ru-RU');
    //     }
    // }

    // 🔹 Qolgan qarzni hisoblash va to'lov summasini nazorat qilish
    function calculateRemainingDebt() {
        const total = getNumericValue('#total_price');

        // To'lov maydonlari ro'yxati
        const paymentFields = ['#cash_paid', '#card_paid', '#transfer_paid', '#bank_paid'];

        let paid = 0;

        paymentFields.forEach(id => {
            let currentFieldVal = getNumericValue(id);

            // Agar hozirgacha hisoblangan to'lov + joriy maydon > total bo'lsa
            if ((paid + currentFieldVal) > total) {
                // Ushbu maydonni shunday o'zgartiramizki, jami totaldan oshmasin
                currentFieldVal = Math.max(total - paid, 0);

                // Maydon qiymatini vizual yangilash (foydalanuvchi ko'rishi uchun)
                if (currentCurrency === '{{ StatusService::CURRENCY_USD }}') {
                    $(id).val(currentFieldVal.toFixed(3));
                } else {
                    $(id).val(Math.round(currentFieldVal).toLocaleString('ru-RU'));
                }

                // Foydalanuvchiga qisqa vaqt qizil chegara bilan signal berish (ixtiyoriy)
                $(id).css('border-color', 'red');
                setTimeout(() => { $(id).css('border-color', ''); }, 1000);
            }

            paid += currentFieldVal;
        });

        const remaining = Math.max(total - paid, 0);
        const debtInput = document.querySelector('#remaining_debt');
        const totalPaidInput = document.querySelector('#total_amount_paid');

        // Natijalarni chiqarish
        if (currentCurrency === '{{ StatusService::CURRENCY_USD }}') {
            debtInput.value = remaining.toFixed(3);
            totalPaidInput.value = paid.toFixed(3);
        } else {
            debtInput.value = Math.round(remaining).toLocaleString('ru-RU');
            totalPaidInput.value = Math.round(paid).toLocaleString('ru-RU');
        }
    }

    // 🔹 Valyutani almashtirish
    function changeCurrency() {
        const newCurrency = $('#currency').val();
        const label = $('#currency option:selected').text();
        $('#currency-label').text(label);
        $('#currency-label-paid').text(label);
        $('#currency-label-debt').text(label);

        // Faqat mahsulot narxlarini konvertatsiya qilamiz
        document.querySelectorAll('.price-input').forEach(input => {
            const origSom = parseFloat(input.dataset.originalPrice);
            if (isNaN(origSom) || origSom <= 0) return;

            if (newCurrency === '{{ StatusService::CURRENCY_USD }}') {
                // input.value = toUsd(origSom).toFixed(10);
                input.value = toUsd(origSom).toFixed(3);
            } else {
                input.value = Math.round(origSom).toLocaleString('ru-RU');
            }
        });

        // 🔹 Jami summani hisoblash (to‘lovlar o‘zgarmas)
        currentCurrency = newCurrency;
        calculateTotal();
    }

    document.addEventListener('DOMContentLoaded', function () {
        // 🔹 Faqat yangi yaratish sahifasida avtomatik hisoblash
        @if (Route::is('order.create'))
        calculateTotal();
        @endif

        $('#currency').on('change', changeCurrency);
        $('#cash_paid, #card_paid, #transfer_paid, #bank_paid').on('input keyup', calculateRemainingDebt);
    });
</script>

<script>
    $('#save-client-btn').on('click', function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        $.ajax({
            url: '{{ route('user.storeAjax') }}',
            method: 'POST',
            data: {
                username: $('input[name="username"]').val(),
                phone: $('input[name="phone"]').val(),
                role_id: '{{ $clientRoleId }}'
            },
            success: function (res) {

                const option = new Option(
                    res.username + ' (' + res.phone + ')',
                    res.id,
                    true,
                    true
                );

                $('#user_id')
                    .val(null)
                    .append(option)
                    .trigger('change');

                $('#createClientModal').modal('hide');
            },
            error: function () {
                showCustomAlert('Клиент яратилмади', 'error');
                console.error(xhr);
            }
        });
    });
</script>

<script>
    $('#order-form').on('submit', function (e) {
        const totalPaid =
            getNumericValue('#cash_paid') +
            getNumericValue('#card_paid') +
            getNumericValue('#transfer_paid') +
            getNumericValue('#bank_paid');

        const confirmed = $('#allowZeroPayment').is(':checked');

        if (totalPaid <= 0 && !confirmed) {
            e.preventDefault();
            showCustomAlert('Тўлов 0. Қарздорликни белгилан!', 'info');
            return false;
        }
    });

    function toggleZeroPaymentSwitch() {
        const totalPaid =
            getNumericValue('#cash_paid') +
            getNumericValue('#card_paid') +
            getNumericValue('#transfer_paid') +
            getNumericValue('#bank_paid');

        if (totalPaid > 0) {
            $('.debt-switch').hide();
            $('#allowZeroPayment').prop('checked', false);
        } else {
            $('.debt-switch').show();
        }
    }

    $('#cash_paid, #card_paid, #transfer_paid, #bank_paid')
        .on('input keyup', toggleZeroPaymentSwitch);

    toggleZeroPaymentSwitch();
</script>


{{--------------------------------------------------------------------------------------------------------------------}}


{{--<style>--}}
{{--    /* Card styling */--}}
{{--    .erp-card {--}}
{{--        border: none;--}}
{{--        border-radius: 12px;--}}
{{--        background: #ffffff;--}}
{{--        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);--}}
{{--    }--}}

{{--    .section-title {--}}
{{--        font-size: 0.85rem;--}}
{{--        font-weight: 700;--}}
{{--        text-transform: uppercase;--}}
{{--        letter-spacing: 0.5px;--}}
{{--        color: var(--primary-color);--}}
{{--        border-bottom: 2px solid var(--primary-color);--}}
{{--        display: inline-block;--}}
{{--        margin-bottom: 1rem;--}}
{{--    }--}}

{{--    /* Select2 va tugmani bir qatorda chiroyli chiqarish uchun */--}}
{{--    .input-group > .flex-grow-1 .select2-container {--}}
{{--        width: 100% !important;--}}
{{--    }--}}

{{--    .input-group > .flex-grow-1 .select2-selection {--}}
{{--        border-top-right-radius: 0 !important;--}}
{{--        border-bottom-right-radius: 0 !important;--}}
{{--    }--}}

{{--    .custom-radius-btn {--}}
{{--        border-top-left-radius: 0 !important;--}}
{{--        border-bottom-left-radius: 0 !important;--}}
{{--        border-top-right-radius: 5px !important;--}}
{{--        border-bottom-right-radius: 5px !important;--}}
{{--    }--}}

{{--    .input-group > .btn {--}}
{{--        border-top-left-radius: 0 !important;--}}
{{--        border-bottom-left-radius: 0 !important;--}}
{{--    }--}}

{{--    /* Input & Select2 overrides */--}}
{{--    .form-control, .select2-container--default .select2-selection--single {--}}
{{--        border-radius: 8px !important;--}}
{{--        border: 1px solid var(--border-color) !important;--}}
{{--        height: 42px !important;--}}
{{--        padding: 6px 12px !important;--}}
{{--        font-size: 14px;--}}
{{--    }--}}

{{--    .form-control:focus {--}}
{{--        border-color: var(--primary-color) !important;--}}
{{--        box-shadow: 0 0 0 0.2rem rgba(85, 68, 252, 0.1) !important;--}}
{{--    }--}}

{{--    /* Custom Switch Styling */--}}
{{--    .debt-switch-wrapper {--}}
{{--        background: #f1f0ff;--}}
{{--        padding: 10px 15px;--}}
{{--        border-radius: 10px;--}}
{{--        display: inline-flex;--}}
{{--        align-items: center;--}}
{{--        border: 1px solid rgba(85, 68, 252, 0.2);--}}
{{--    }--}}

{{--    .switch {--}}
{{--        position: relative;--}}
{{--        display: inline-block;--}}
{{--        width: 40px;--}}
{{--        height: 22px;--}}
{{--    }--}}

{{--    .switch input { opacity: 0; width: 0; height: 0; }--}}

{{--    .slider {--}}
{{--        position: absolute; cursor: pointer;--}}
{{--        top: 0; left: 0; right: 0; bottom: 0;--}}
{{--        background-color: #ccc;--}}
{{--        transition: .4s; border-radius: 34px;--}}
{{--    }--}}

{{--    .slider:before {--}}
{{--        position: absolute; content: "";--}}
{{--        height: 16px; width: 16px; left: 3px; bottom: 3px;--}}
{{--        background-color: white; transition: .4s; border-radius: 50%;--}}
{{--    }--}}

{{--    input:checked + .slider { background-color: var(--primary-color); }--}}
{{--    input:checked + .slider:before { transform: translateX(18px); }--}}

{{--    /* Items Table Styling */--}}
{{--    .order-item-row {--}}
{{--        background: var(--secondary-bg);--}}
{{--        border-radius: 10px;--}}
{{--        padding: 15px;--}}
{{--        margin-bottom: 10px;--}}
{{--        transition: all 0.3s;--}}
{{--    }--}}

{{--    .order-item-row:hover { background: #eeecff; }--}}

{{--    .btn-primary { background-color: var(--primary-color); border: none; padding: 10px 25px; border-radius: 8px; }--}}
{{--    .btn-primary:hover { background-color: #4335d1; }--}}

{{--    .payment-summary {--}}
{{--        background: #2c3e50;--}}
{{--        color: white;--}}
{{--        border-radius: 12px;--}}
{{--        padding: 20px;--}}
{{--    }--}}

{{--    .payment-summary label { color: #bdc3c7; font-size: 12px; }--}}
{{--    .payment-summary input.form-control {--}}
{{--        background: rgba(255,255,255,0.1);--}}
{{--        border: none !important;--}}
{{--        color: #fff !important;--}}
{{--        font-weight: bold;--}}
{{--        font-size: 1.1rem;--}}
{{--    }--}}
{{--</style>--}}

{{--<form id="order-form" action="{{ $action }}" method="POST">--}}
{{--    @csrf--}}
{{--    @if ($method === 'PUT') @method('PUT') @endif--}}

{{--    <div class="container-fluid py-4">--}}
{{--        <div class="row">--}}
{{--            <div class="col-lg-8">--}}
{{--                <div class="card erp-card mb-4">--}}
{{--                    <div class="card-body">--}}
{{--                        <div class="row g-3 mb-4">--}}
{{--                            <div class="col-md-7">--}}
{{--                                <label class="form-label fw-bold">Клиент</label>--}}
{{--                                <div class="input-group flex-nowrap"> <div class="flex-grow-1"> <select name="user_id" id="user_id" class="form-control filter-select2" required>--}}
{{--                                            <option value="">Klientni tanlang</option>--}}
{{--                                            @foreach($users as $user)--}}
{{--                                                <option value="{{ $user->id }}" {{ old('user_id', $order->user_id ?? $defaultUserId) == $user->id ? 'selected' : '' }}>--}}
{{--                                                    {{ $user->username }} ({{ \App\Services\PhoneFormatService::uzPhone($user->phone) }})--}}
{{--                                                </option>--}}
{{--                                            @endforeach--}}
{{--                                        </select>--}}
{{--                                    </div>--}}
{{--                                    <button type="button" class="btn btn-outline-primary custom-radius-btn" data-bs-toggle="modal" data-bs-target="#createClientModal">--}}
{{--                                        <i class="fas fa-plus"></i>--}}
{{--                                    </button>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                            <div class="col-md-3">--}}
{{--                                <label class="form-label fw-bold">Статус</label>--}}
{{--                                <select name="status" class="form-control shadow-sm" required>--}}
{{--                                    @foreach(\App\Models\Order::getStatusList() as $key => $label)--}}
{{--                                        <option value="{{ $key }}" {{ old('status', $order->status ?? '') == $key ? 'selected' : '' }}>{{ $label }}</option>--}}
{{--                                    @endforeach--}}
{{--                                </select>--}}
{{--                            </div>--}}
{{--                            <div class="col-md-2">--}}
{{--                                <label for="currency" class="form-label fw-bold">Валюта</label>--}}
{{--                                <select id="currency" name="currency" class="form-control shadow-sm" onchange="changeCurrency()" required>--}}
{{--                                    @foreach(StatusService::getCurrency() as $key => $label)--}}
{{--                                        <option value="{{ $key }}" {{ $currentCurrency == $key ? 'selected' : '' }}>{{ $label }}</option>--}}
{{--                                    @endforeach--}}
{{--                                </select>--}}
{{--                            </div>--}}
{{--                        </div>--}}

{{--                        <h5 class="section-title">Маҳсулотлар</h5>--}}
{{--                        <div id="items">--}}
{{--                            @foreach($oldItems as $i => $item)--}}
{{--                                @php--}}
{{--                                    $variationId = is_array($item) ? ($item['product_variation_id'] ?? null) : $item->product_variation_id;--}}
{{--                                    $selectedVariation = $variations->firstWhere('id', $variationId);--}}
{{--                                    $priceValue = old("items.$i.price") ?: (is_array($item) ? ($item['price'] ?? '') : ($currentCurrency == StatusService::CURRENCY_UZS ? number_format($item->price ?? 0, 0, '', ' ') : number_format($item->price ?? 0, 2, '.', ' ')));--}}
{{--                                @endphp--}}
{{--                                <div class="order-item-row row align-items-center" id="item-{{ $i }}">--}}
{{--                                    <div class="col-md-5">--}}
{{--                                        <label class="small text-muted">Маҳсулот</label>--}}
{{--                                        <select name="items[{{ $i }}][product_variation_id]" class="form-control filter-select2" onchange="updatePrice(this, {{ $i }})" required>--}}
{{--                                            <option value="">Tanlang...</option>--}}
{{--                                            @foreach($variations as $variation)--}}
{{--                                                <option value="{{ $variation->id }}" data-price="{{ $variation->price }}" {{ (int)$variationId === (int)$variation->id ? 'selected' : '' }}>--}}
{{--                                                    {!! $variation->product->title !!} ({!! $variation->title !!})--}}
{{--                                                </option>--}}
{{--                                            @endforeach--}}
{{--                                        </select>--}}
{{--                                    </div>--}}
{{--                                    <div class="col-md-2">--}}
{{--                                        <label class="small text-muted">Сони</label>--}}
{{--                                        <input type="text" name="items[{{ $i }}][quantity]" class="form-control filter-numeric-decimal" value="{{ old("items.$i.quantity", is_array($item) ? ($item['quantity'] ?? 1) : ($item->quantity ?? 1)) }}" oninput="calculateTotal()" required>--}}
{{--                                    </div>--}}
{{--                                    <div class="col-md-4">--}}
{{--                                        <label class="small text-muted">Нарх</label>--}}
{{--                                        <input type="text" name="items[{{ $i }}][price]" class="form-control price-input" value="{{ $priceValue }}" data-original-price="{{ $selectedVariation->price ?? 0 }}" oninput="validatePrice(this); calculateTotal()" required>--}}
{{--                                    </div>--}}
{{--                                    <div class="col-md-1 text-end">--}}
{{--                                        @if($i > 0)--}}
{{--                                            <button type="button" class="btn btn-link text-danger p-0 mt-4" onclick="removeItem({{ $i }})"><i class="fas fa-trash"></i></button>--}}
{{--                                        @endif--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            @endforeach--}}
{{--                        </div>--}}

{{--                        <button type="button" class="btn btn-link text-decoration-none fw-bold p-0 mt-2" style="color: var(--primary-color)" onclick="addItem()">--}}
{{--                            <i class="fas fa-plus-circle"></i> Маҳсулот қўшиш--}}
{{--                        </button>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}

{{--            <div class="col-lg-4">--}}
{{--                <div class="card erp-card payment-summary mb-4">--}}
{{--                    <div class="card-body">--}}
{{--                        <div class="d-flex justify-content-between align-items-center mb-4">--}}
{{--                            <h5 class="m-0 fw-bold">Тўлов ҳисоби</h5>--}}
{{--                            <div class="debt-switch-wrapper">--}}
{{--                                <label class="switch m-0">--}}
{{--                                    <input type="checkbox" id="allowZeroPayment">--}}
{{--                                    <span class="slider"></span>--}}
{{--                                </label>--}}
{{--                                <span class="ms-2 small fw-bold text-dark">Қарздорлик</span>--}}
{{--                            </div>--}}
{{--                        </div>--}}

{{--                        <div class="mb-3">--}}
{{--                            <label>Жами сумма</label>--}}
{{--                            <input type="text" id="total_price" name="total_price" class="form-control" value="{{ $totalPriceValue }}" readonly>--}}
{{--                        </div>--}}

{{--                        <hr style="border-color: rgba(255,255,255,0.1)">--}}

{{--                        <div class="row g-2 mb-3">--}}
{{--                            <div class="col-6">--}}
{{--                                <label>Нақд</label>--}}
{{--                                <input type="text" id="cash_paid" name="cash_paid" class="form-control" value="{{ $cashPaidValue }}">--}}
{{--                            </div>--}}
{{--                            <div class="col-6">--}}
{{--                                <label>Карта</label>--}}
{{--                                <input type="text" id="card_paid" name="card_paid" class="form-control" value="{{ $cardPaidValue }}">--}}
{{--                            </div>--}}
{{--                            <div class="col-6">--}}
{{--                                <label>Ўтказма</label>--}}
{{--                                <input type="text" id="transfer_paid" name="transfer_paid" class="form-control" value="{{ $transferPaidValue }}">--}}
{{--                            </div>--}}
{{--                            <div class="col-6">--}}
{{--                                <label>Ҳисоб рақам</label>--}}
{{--                                <input type="text" id="bank_paid" name="bank_paid" class="form-control" value="{{ $bankPaidValue }}">--}}
{{--                            </div>--}}
{{--                        </div>--}}

{{--                        <div class="mb-3 p-3 rounded" style="background: rgba(0,0,0,0.2)">--}}
{{--                            <div class="d-flex justify-content-between mb-1">--}}
{{--                                <span class="small">Тўланди:</span>--}}
{{--                                <input type="text" id="total_amount_paid" name="total_amount_paid" class="bg-transparent border-0 text-white text-end fw-bold w-50" value="{{ $totalPaidValue }}" readonly>--}}
{{--                            </div>--}}
{{--                            <div class="d-flex justify-content-between">--}}
{{--                                <span class="small text-warning">Қолдиқ:</span>--}}
{{--                                <input type="text" id="remaining_debt" name="remaining_debt" class="bg-transparent border-0 text-warning text-end fw-bold w-50" value="{{ $remainingDebtValue }}" readonly>--}}
{{--                            </div>--}}
{{--                        </div>--}}

{{--                        <button type="submit" class="btn btn-primary w-100 py-3 fw-bold shadow">--}}
{{--                            <i class="fas fa-save me-2"></i> {{ Route::currentRouteName() == 'order.edit' ? 'Буюртмани янгиалш' : 'Буюртмани якунлаш' }}--}}
{{--                        </button>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</form>--}}

{{--<script>--}}
{{--    let itemIndex = $('#items .item').length;--}}

{{--    function addItem() {--}}
{{--        const selectedIds = getSelectedVariationIds();--}}
{{--        let optionsHtml = `<option value="">Маҳсулотни танланг</option>`;--}}

{{--        @foreach($variations as $variation)--}}
{{--            optionsHtml += `<option value="{{ $variation->id }}" data-price="{{ $variation->price }}"--}}
{{--                ${selectedIds.includes('{{ $variation->id }}') ? 'display' : ''}>--}}
{{--        {!! $variation->title !!} — {!! $variation->product->title !!}--}}
{{--        ({{ number_format($variation->price, 0, '', ' ') }} сўм)--}}
{{--            [{{ \App\Helpers\CountHelper::format($variation->count, $variation->unit) }}]--}}
{{--            </option>`;--}}
{{--        @endforeach--}}

{{--        const itemHtml = `--}}
{{--        <div class="item row mb-3" id="item-${itemIndex}">--}}
{{--            <div class="col-md-6">--}}
{{--                <select name="items[${itemIndex}][product_variation_id]" class="form-control filter-select2"--}}
{{--                        onchange="updatePrice(this, ${itemIndex})" required>--}}
{{--                    ${optionsHtml}--}}
{{--                </select>--}}
{{--            </div>--}}
{{--            <div class="col-md-2">--}}
{{--                <input type="text" name="items[${itemIndex}][quantity]" class="form-control filter-numeric-decimal"--}}
{{--                       placeholder="Сони" value="1" oninput="calculateTotal()" required>--}}
{{--            </div>--}}
{{--            <div class="col-md-3">--}}
{{--                <input type="text" name="items[${itemIndex}][price]" class="form-control price-input"--}}
{{--                       placeholder="Сотиш нархи" data-original-price="" value=""--}}
{{--                       oninput="validatePrice(this); calculateTotal()" required>--}}
{{--            </div>--}}
{{--            <div class="col-md-1">--}}
{{--                <button type="button" class="btn btn-sm" onclick="removeItem(${itemIndex})">❌</button>--}}
{{--            </div>--}}
{{--        </div>`;--}}

{{--        $('#items').append(itemHtml);--}}
{{--        $('#items .item:last .filter-select2').select2({width: '100%'});--}}

{{--        // Yangi select’dagi variantlarni yangilash--}}
{{--        updateVariationOptions();--}}

{{--        itemIndex++;--}}
{{--        calculateTotal();--}}
{{--    }--}}

{{--    function removeItem(index) {--}}
{{--        $(`#item-${index}`).remove();--}}
{{--        calculateTotal();--}}
{{--    }--}}

{{--    // 🔹 Variation takrorlamaslik--}}
{{--    function getSelectedVariationIds() {--}}
{{--        let ids = [];--}}
{{--        document.querySelectorAll('.item select[name*="[product_variation_id]"]').forEach(select => {--}}
{{--            const val = select.value;--}}
{{--            if (val) ids.push(val);--}}
{{--        });--}}
{{--        return ids;--}}
{{--    }--}}

{{--    // function updateVariationOptions() {--}}
{{--    //     const selectedIds = getSelectedVariationIds();--}}
{{--    //     document.querySelectorAll('.item select[name*="[product_variation_id]"]').forEach(select => {--}}
{{--    //         const currentVal = select.value;--}}
{{--    //         select.querySelectorAll('option').forEach(opt => {--}}
{{--    //             if (opt.value && opt.value !== currentVal) {--}}
{{--    //                 opt.disabled = selectedIds.includes(opt.value);--}}
{{--    //             } else {--}}
{{--    //                 opt.disabled = false;--}}
{{--    //             }--}}
{{--    //         });--}}
{{--    //         $(select).select2(); // select2 update--}}
{{--    //     });--}}
{{--    // }--}}

{{--    function updateVariationOptions() {--}}
{{--        const selectedIds = getSelectedVariationIds();--}}

{{--        document.querySelectorAll('.item select[name*="[product_variation_id]"]').forEach(select => {--}}
{{--            const currentVal = select.value;--}}

{{--            // Eski variantlarni tiklash uchun — barcha <option>ni qayta tiklaymiz--}}
{{--            const allOptions = [];--}}

{{--            @foreach($variations as $variation)--}}
{{--            allOptions.push({--}}
{{--                id: '{{ $variation->id }}',--}}
{{--                --}}{{--code: '{{ $variation->code }}',--}}
{{--                title: '{{ $variation->product->title }} → {{ $variation->title }}',--}}
{{--                price: '{{ $variation->price }}',--}}
{{--                text: `{!! $variation->title !!} — {!! $variation->product->title !!}--}}
{{--                ({{ number_format($variation->price, 0, '', ' ') }} сўм)--}}
{{--                    @if ($variation->unit == StatusService::UNIT_PSC)--}}
{{--                [{{ number_format($variation->count, 0, '', ' ') }} та]--}}
{{--                    @elseIf ($variation->unit == StatusService::UNIT_KG)--}}
{{--                [{{ number_format($variation->count, 3, '.', ' ') }} кг]--}}
{{--                    @elseIf ($variation->unit == StatusService::UNIT_METER)--}}
{{--                [{{ number_format($variation->count, 2, '.', ' ') }} метр]--}}
{{--                    @endif`--}}
{{--            });--}}
{{--            @endforeach--}}

{{--            // Select ichini tozalaymiz--}}
{{--            select.innerHTML = '<option value="">Маҳсулотни танланг</option>';--}}

{{--            // Faqat hozirgi selectdagi tanlovni yoki boshqa tanlanmagan variantlarni qo‘shamiz--}}
{{--            allOptions.forEach(opt => {--}}
{{--                if (!selectedIds.includes(opt.id) || opt.id === currentVal) {--}}
{{--                    const option = document.createElement('option');--}}
{{--                    option.value = opt.id;--}}
{{--                    option.dataset.price = opt.price;--}}
{{--                    option.text = opt.text.replace(/\s+/g, ' ').trim();--}}

{{--                    if (opt.id === currentVal) {--}}
{{--                        option.selected = true;--}}
{{--                    }--}}

{{--                    select.appendChild(option);--}}
{{--                }--}}
{{--            });--}}

{{--            // select2 ni qayta yuklaymiz--}}
{{--            $(select).select2({--}}
{{--                width: '100%'--}}
{{--            });--}}
{{--        });--}}
{{--    }--}}

{{--    const usdRate = parseFloat('{{ \App\Models\ExchangeRates::where("currency", "USD")->value("rate") ?? 1 }}');--}}
{{--    let currentCurrency = '{{ old("currency", $order->currency ?? StatusService::CURRENCY_UZS) }}';--}}

{{--    // 🔹 So‘mdan dollarga--}}
{{--    function toUsd(uzs) {--}}
{{--        if (!usdRate || usdRate <= 0) return 0;--}}
{{--        return uzs / usdRate;--}}
{{--    }--}}

{{--    // 🔹 Dollardan so‘mga--}}
{{--    function toSom(usd) {--}}
{{--        if (!usdRate || usdRate <= 0) return 0;--}}
{{--        return usd * usdRate;--}}
{{--    }--}}

{{--    // 🔹 Raqam qiymatini olish--}}
{{--    function getNumericValue(el) {--}}
{{--        if (!el) return 0;--}}
{{--        let val = (typeof el === 'string' ? $(el).val() : $(el).value || el.value || '').toString().replace(/\s/g, '');--}}
{{--        const num = parseFloat(val);--}}
{{--        return isNaN(num) ? 0 : num;--}}
{{--    }--}}

{{--    // 🔹 Mahsulot narxini yangilash--}}
{{--    function updatePrice(selectElement, index) {--}}
{{--        const selectedOption = selectElement.options[selectElement.selectedIndex];--}}
{{--        const priceSom = parseFloat(selectedOption.dataset.price) || 0;--}}
{{--        const priceInput = document.querySelector(`input[name="items[${index}][price]"]`);--}}
{{--        if (!priceInput) return;--}}

{{--        // faqat birinchi marta original narxni saqlaymiz--}}
{{--        priceInput.dataset.originalPrice = priceSom;--}}

{{--        if (currentCurrency === '{{ StatusService::CURRENCY_USD }}') {--}}
{{--            // priceInput.value = toUsd(priceSom).toFixed(10);--}}
{{--            priceInput.value = toUsd(priceSom).toFixed(3);--}}
{{--        } else {--}}
{{--            priceInput.value = Math.round(priceSom).toLocaleString('ru-RU');--}}
{{--        }--}}
{{--        console.log(priceInput.value);--}}

{{--        validatePrice(priceInput);--}}
{{--        updateVariationOptions();--}}
{{--        calculateTotal();--}}
{{--    }--}}

{{--    // 🔹 Minimal narxni tekshirish--}}
{{--    function validatePrice(input) {--}}
{{--        const minPrice = parseFloat(input.dataset.originalPrice);--}}
{{--        const currentPrice = getNumericValue(input);--}}
{{--        if (!isNaN(minPrice) && currentPrice < minPrice && currentCurrency === '{{ StatusService::CURRENCY_UZS }}') {--}}
{{--            input.style.borderColor = 'red';--}}
{{--        } else {--}}
{{--            input.style.borderColor = '';--}}
{{--        }--}}
{{--    }--}}

{{--    // 🔹 Jami summani hisoblash--}}
{{--    function calculateTotal() {--}}
{{--        let total = 0;--}}
{{--        document.querySelectorAll('.item').forEach(item => {--}}
{{--            const qty = getNumericValue(item.querySelector('input[name*="[quantity]"]'));--}}
{{--            const price = getNumericValue(item.querySelector('.price-input'));--}}
{{--            total += qty * price;--}}
{{--        });--}}

{{--        const totalInput = document.querySelector('#total_price');--}}

{{--        // 🔹 Hech qanday konvertatsiya qilmaymiz, valyutaga qarab faqat formatlaymiz--}}
{{--        if (currentCurrency === '{{ StatusService::CURRENCY_USD }}') {--}}
{{--            totalInput.value = total.toFixed(3);--}}
{{--        } else {--}}
{{--            totalInput.value = Math.round(total).toLocaleString('ru-RU');--}}
{{--        }--}}

{{--        // UI dagi katta display uchun--}}
{{--        const displaySpan = document.querySelector('#total_price_display');--}}

{{--        // Qiymatni ekranga chiqarish--}}
{{--        displaySpan.innerText = totalInput.value;--}}

{{--        calculateRemainingDebt();--}}
{{--    }--}}

{{--    // 🔹 To‘langan jami--}}
{{--    function calculatePaidTotal() {--}}
{{--        let paid = ['#cash_paid', '#card_paid', '#transfer_paid', '#bank_paid']--}}
{{--            .map(id => getNumericValue(id))--}}
{{--            .reduce((a, b) => a + b, 0);--}}

{{--        const totalPaidInput = document.querySelector('#total_amount_paid');--}}
{{--        if (currentCurrency === '{{ StatusService::CURRENCY_USD }}') {--}}
{{--            totalPaidInput.value = paid.toFixed(3);--}}
{{--        } else {--}}
{{--            totalPaidInput.value = Math.round(paid).toLocaleString('ru-RU');--}}
{{--        }--}}
{{--        console.log(totalPaidInput);--}}
{{--        return paid;--}}
{{--    }--}}

{{--    // 🔹 Qolgan qarzni hisoblash--}}
{{--    // function calculateRemainingDebt() {--}}
{{--    //     const total = getNumericValue('#total_price');--}}
{{--    //     const paid = ['#cash_paid', '#card_paid', '#transfer_paid', '#bank_paid']--}}
{{--    //         .map(id => getNumericValue(id))--}}
{{--    //         .reduce((a, b) => a + b, 0);--}}

{{--    //     const remaining = Math.max(total - paid, 0);--}}
{{--    //     const debtInput = document.querySelector('#remaining_debt');--}}

{{--    //     if (currentCurrency === '{{ StatusService::CURRENCY_USD }}') {--}}
{{--    //         debtInput.value = remaining.toFixed(3);--}}
{{--    //     } else {--}}
{{--    //         debtInput.value = Math.round(remaining).toLocaleString('ru-RU');--}}
{{--    //     }--}}

{{--    //     // Total paid ham shunchaki jami to‘langanlarni hisoblaydi--}}
{{--    //     const totalPaidInput = document.querySelector('#total_amount_paid');--}}
{{--    //     if (currentCurrency === '{{ StatusService::CURRENCY_USD }}') {--}}
{{--    //         totalPaidInput.value = paid.toFixed(3);--}}
{{--    //     } else {--}}
{{--    //         totalPaidInput.value = Math.round(paid).toLocaleString('ru-RU');--}}
{{--    //     }--}}
{{--    // }--}}

{{--    // 🔹 Qolgan qarzni hisoblash va to'lov summasini nazorat qilish--}}
{{--    function calculateRemainingDebt() {--}}
{{--        const total = getNumericValue('#total_price');--}}

{{--        // To'lov maydonlari ro'yxati--}}
{{--        const paymentFields = ['#cash_paid', '#card_paid', '#transfer_paid', '#bank_paid'];--}}

{{--        let paid = 0;--}}

{{--        paymentFields.forEach(id => {--}}
{{--            let currentFieldVal = getNumericValue(id);--}}

{{--            // Agar hozirgacha hisoblangan to'lov + joriy maydon > total bo'lsa--}}
{{--            if ((paid + currentFieldVal) > total) {--}}
{{--                // Ushbu maydonni shunday o'zgartiramizki, jami totaldan oshmasin--}}
{{--                currentFieldVal = Math.max(total - paid, 0);--}}

{{--                // Maydon qiymatini vizual yangilash (foydalanuvchi ko'rishi uchun)--}}
{{--                if (currentCurrency === '{{ StatusService::CURRENCY_USD }}') {--}}
{{--                    $(id).val(currentFieldVal.toFixed(3));--}}
{{--                } else {--}}
{{--                    $(id).val(Math.round(currentFieldVal).toLocaleString('ru-RU'));--}}
{{--                }--}}

{{--                // Foydalanuvchiga qisqa vaqt qizil chegara bilan signal berish (ixtiyoriy)--}}
{{--                $(id).css('border-color', 'red');--}}
{{--                setTimeout(() => { $(id).css('border-color', ''); }, 1000);--}}
{{--            }--}}

{{--            paid += currentFieldVal;--}}
{{--        });--}}

{{--        const remaining = Math.max(total - paid, 0);--}}
{{--        const debtInput = document.querySelector('#remaining_debt');--}}
{{--        const totalPaidInput = document.querySelector('#total_amount_paid');--}}

{{--        // Natijalarni chiqarish--}}
{{--        if (currentCurrency === '{{ StatusService::CURRENCY_USD }}') {--}}
{{--            debtInput.value = remaining.toFixed(3);--}}
{{--            totalPaidInput.value = paid.toFixed(3);--}}
{{--        } else {--}}
{{--            debtInput.value = Math.round(remaining).toLocaleString('ru-RU');--}}
{{--            totalPaidInput.value = Math.round(paid).toLocaleString('ru-RU');--}}
{{--        }--}}
{{--    }--}}

{{--    // 🔹 Valyutani almashtirish--}}
{{--    function changeCurrency() {--}}
{{--        const newCurrency = $('#currency').val();--}}
{{--        const label = $('#currency option:selected').text();--}}
{{--        $('#currency-label').text(label);--}}
{{--        $('#currency-label-paid').text(label);--}}
{{--        $('#currency-label-debt').text(label);--}}

{{--        // Faqat mahsulot narxlarini konvertatsiya qilamiz--}}
{{--        document.querySelectorAll('.price-input').forEach(input => {--}}
{{--            const origSom = parseFloat(input.dataset.originalPrice);--}}
{{--            if (isNaN(origSom) || origSom <= 0) return;--}}

{{--            if (newCurrency === '{{ StatusService::CURRENCY_USD }}') {--}}
{{--                // input.value = toUsd(origSom).toFixed(10);--}}
{{--                input.value = toUsd(origSom).toFixed(3);--}}
{{--            } else {--}}
{{--                input.value = Math.round(origSom).toLocaleString('ru-RU');--}}
{{--            }--}}
{{--        });--}}

{{--        // 🔹 Jami summani hisoblash (to‘lovlar o‘zgarmas)--}}
{{--        currentCurrency = newCurrency;--}}
{{--        calculateTotal();--}}
{{--    }--}}

{{--    document.addEventListener('DOMContentLoaded', function () {--}}
{{--        // 🔹 Faqat yangi yaratish sahifasida avtomatik hisoblash--}}
{{--        @if (Route::is('order.create'))--}}
{{--        calculateTotal();--}}
{{--        @endif--}}

{{--        $('#currency').on('change', changeCurrency);--}}
{{--        $('#cash_paid, #card_paid, #transfer_paid, #bank_paid').on('input keyup', calculateRemainingDebt);--}}
{{--    });--}}
{{--</script>--}}

{{--<script>--}}
{{--    $('#save-client-btn').on('click', function () {--}}
{{--        $.ajaxSetup({--}}
{{--            headers: {--}}
{{--                'X-CSRF-TOKEN': '{{ csrf_token() }}'--}}
{{--            }--}}
{{--        });--}}
{{--        $.ajax({--}}
{{--            url: '{{ route('user.storeAjax') }}',--}}
{{--            method: 'POST',--}}
{{--            data: {--}}
{{--                username: $('input[name="username"]').val(),--}}
{{--                phone: $('input[name="phone"]').val(),--}}
{{--                role_id: '{{ $clientRoleId }}'--}}
{{--            },--}}
{{--            success: function (res) {--}}

{{--                const option = new Option(--}}
{{--                    res.username + ' (' + res.phone + ')',--}}
{{--                    res.id,--}}
{{--                    true,--}}
{{--                    true--}}
{{--                );--}}

{{--                $('#user_id')--}}
{{--                    .val(null)--}}
{{--                    .append(option)--}}
{{--                    .trigger('change');--}}

{{--                $('#createClientModal').modal('hide');--}}
{{--            },--}}
{{--            error: function () {--}}
{{--                showCustomAlert('Клиент яратилмади', 'error');--}}
{{--                console.error(xhr);--}}
{{--            }--}}
{{--        });--}}
{{--    });--}}
{{--</script>--}}

{{--<script>--}}
{{--    $('#order-form').on('submit', function (e) {--}}
{{--        const totalPaid =--}}
{{--            getNumericValue('#cash_paid') +--}}
{{--            getNumericValue('#card_paid') +--}}
{{--            getNumericValue('#transfer_paid') +--}}
{{--            getNumericValue('#bank_paid');--}}

{{--        const confirmed = $('#allowZeroPayment').is(':checked');--}}

{{--        if (totalPaid <= 0 && !confirmed) {--}}
{{--            e.preventDefault();--}}
{{--            showCustomAlert('Тўлов 0. Қарздорликни белгилан!', 'info');--}}
{{--            return false;--}}
{{--        }--}}
{{--    });--}}

{{--    function toggleZeroPaymentSwitch() {--}}
{{--        const totalPaid =--}}
{{--            getNumericValue('#cash_paid') +--}}
{{--            getNumericValue('#card_paid') +--}}
{{--            getNumericValue('#transfer_paid') +--}}
{{--            getNumericValue('#bank_paid');--}}

{{--        if (totalPaid > 0) {--}}
{{--            $('.debt-switch').hide();--}}
{{--            $('#allowZeroPayment').prop('checked', false);--}}
{{--        } else {--}}
{{--            $('.debt-switch').show();--}}
{{--        }--}}
{{--    }--}}

{{--    $('#cash_paid, #card_paid, #transfer_paid, #bank_paid')--}}
{{--        .on('input keyup', toggleZeroPaymentSwitch);--}}

{{--    toggleZeroPaymentSwitch();--}}
{{--</script>--}}





<div class="modal fade" id="createClientModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius: 0.5rem">
            <div class="modal-header">
                <h5><i class="fas fa-user-plus me-2" style="color: var(--primary-color)"></i> Янги клиент қўшиш</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div id="client-create-form">
                    @csrf
                    <input type="hidden" name="role_id" value="{{ $clientRoleId }}">

                    <div class="mb-3">
                        <label>Фойдаланувчи номи (Username)</label>
                        <input name="username" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label">Телефон</label>
                        <input id="phone" type="text" name="phone" class="form-control">
                    </div>

                    <button type="button" id="save-client-btn" class="btn btn-primary">
                        Сақлаш
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
