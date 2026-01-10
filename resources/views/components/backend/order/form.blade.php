@php
    use \App\Services\StatusService;
@endphp

<form action="{{ $action }}" method="POST">
    @csrf
    @if ($method === 'PUT')
        @method('PUT')
    @endif

    <div class="container-fluid mt-4">
        <div class="card shadow">
            <div class="card-body">

                <div class="col-md-11 mb-3">
                    <label for="user_id">Клиент</label>
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
                                        $priceValue = number_format($item->price ?? 0, 3, '.', ' ');
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
                                            {{ $variation->code }} — {{ $variation->product->title }} → {{ $variation->title }}
                                            ({{ number_format($variation->price, 0, '', ' ') }} сўм)
                                            [{{ \App\Helpers\CountHelper::format($variation->count, $variation->unit) }}]
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <input type="text" name="items[{{ $i }}][quantity]" class="form-control filter-numeric"
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

                <button type="button" class="btn btn-sm btn-success mb-4" onclick="addItem()">+ Қўшиш</button>

                <div class="mb-3">
                    <label>Жами сумма (<span id="currency-label">{{ $currencyLabel }}</span>)</label>
                    <input type="text" id="total_price" name="total_price" class="form-control"
                           value="{{ $totalPriceValue }}" readonly>
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

                <button type="submit" class="btn btn-info">
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
            {{--    ${selectedIds.includes('{{ $variation->id }}') ? 'display' : ''}>--}}
                {{ $variation->code }} — {{ $variation->product->title }} → {{ $variation->title }}
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
                <input type="text" name="items[${itemIndex}][quantity]" class="form-control filter-numeric"
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
                code: '{{ $variation->code }}',
                title: '{{ $variation->product->title }} → {{ $variation->title }}',
                price: '{{ $variation->price }}',
                text: `{{ $variation->code }} — {{ $variation->product->title }} → {{ $variation->title }}
                ({{ number_format($variation->price, 0, '', ' ') }} сўм)
                @if ($variation->unit == StatusService::UNIT_PSC)
                [{{ number_format($variation->count, 0, '', ' ') }} та]
                @else
                [{{ number_format($variation->count, 3, '.', ' ') }} кг]
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
