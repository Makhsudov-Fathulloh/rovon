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
                <div id="items">

                    <label for="product_variation_id" class="mb-3">Маҳсулотлар</label>
                    @foreach($items as $i => $item)
                        <div class="item row mb-3" id="item-{{ $i }}">
                            <div class="col-md-6">
                                <select name="items[{{ $i }}][product_variation_id]" class="form-control filter-select2"
                                        onchange="updatePrice(this, {{ $i }})" required>
                                    <option value="">Маҳсулотни танланг</option>
                                    @foreach($variations as $variation)
                                        <option value="{{ $variation->id }}"
                                                data-price="{{ $variation->price }}"
                                                data-code="{{ $variation->code }}"
                                                data-title="{{ $variation->product->title }}">
                                            {{ $variation->code }} — {{ $variation->product->title }}
                                            → {{ $variation->title }}
                                            ({{ number_format($variation->price, 0, '', ' ') }} {{ $currency }})
                                            [{{ number_format($variation->count, 0, '', ' ') }} та ]
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="text" name="items[{{ $i }}][quantity]" class="form-control filter-numeric"
                                       placeholder="Сони" min="1" value="1"
                                       oninput="updateOrderTotals()" required>
                            </div>
                            <div class="col-md-3">
                                <input type="text" name="items[{{ $i }}][price]"
                                       class="form-control price-input filter-numeric-decimal"
                                       placeholder="Сотиш нархи" step="100" value=""
                                       data-original-price=""
                                       oninput="validatePrice(this); updateOrderTotals()" required>
                            </div>
                        </div>
                    @endforeach
                </div>

                <button type="button" class="btn btn-sm btn-secondary mb-5" onclick="addItem()">+ Қўшиш</button>

                {{-- Order umumiy summalari --}}
                <div class="mb-3">
                    <label>Жами сумма ({{ $currency }})</label>
                    <input type="text" id="total_price" name="total_price" class="form-control" value="0" readonly>
                </div>

                <div class="row mb-3">
                    <div class="col-3">
                        <label>Нақд</label>
                        <input type="text" id="cash_paid" name="cash_paid" class="form-control filter-numeric-decimal" oninput="updateOrderTotals()">
                    </div>
                    <div class="col-3">
                        <label>Карта (терминал)</label>
                        <input type="text" id="card_paid" name="card_paid" class="form-control filter-numeric-decimal" oninput="updateOrderTotals()">
                    </div>
                    <div class="col-3">
                        <label>Ўтказма</label>
                        <input type="text" id="transfer_paid" name="transfer_paid" class="form-control filter-numeric-decimal" oninput="updateOrderTotals()">
                    </div>
                    <div class="col-3">
                        <label>Ҳисоб рақам</label>
                        <input type="text" id="bank_paid" name="bank_paid" class="form-control filter-numeric-decimal" oninput="updateOrderTotals()">
                    </div>
                </div>

                <div class="mb-3">
                    <label>Жами тўланган ({{ $currency }})</label>
                    <input type="text" id="total_amount_paid" name="total_amount_paid" class="form-control" value="0" readonly>
                </div>

                <div class="mb-3">
                    <label>Қолдиқ қарз ({{ $currency }})</label>
                    <input type="text" id="remaining_debt" name="remaining_debt" class="form-control" value="0" readonly>
                </div>

                <button type="submit" class="btn btn-primary">Сақлаш</button>
            </div>
        </div>
    </div>
</form>

<script>
    let itemIndex = {{ count($items) }};
    let currentCurrency = "{{ $currency }}"; // "$" yoki "UZS"
    const exchangeRate = parseFloat("{{ $exchangeRate }}") || 1;
    const isUSD = currentCurrency.includes('$') || currentCurrency === '{{ StatusService::CURRENCY_USD }}';

    function getSelectedVariationIds() {
        return Array.from(document.querySelectorAll('.item select[name*="[product_variation_id]"]'))
            .map(select => select.value)
            .filter(Boolean);
    }

    function addItem() {
        const selectedIds = getSelectedVariationIds();
        let optionsHtml = `<option value="">Маҳсулотни танланг</option>`;
        @foreach($variations as $variation)
            optionsHtml += `<option value="{{ $variation->id }}"
            data-price="{{ $variation->price }}"
            ${selectedIds.includes('{{ $variation->id }}') ? 'disabled' : ''}>
            {{ $variation->code }} — {{ $variation->product->title }} → {{ $variation->title }}
        ({{ number_format($variation->price, 0, '', ' ') }} {{ $currency }})
        </option>`;
        @endforeach

        const html = `
    <div class="item row mb-3" id="item-${itemIndex}">
        <div class="col-md-6">
            <select name="items[${itemIndex}][product_variation_id]" class="form-control filter-select2"
                    onchange="updatePrice(this, ${itemIndex}); updateVariationOptions();" required>
                ${optionsHtml}
            </select>
        </div>
        <div class="col-md-2">
            <input type="text" name="items[${itemIndex}][quantity]" class="form-control filter-numeric"
                   value="1" oninput="updateOrderTotals()" required>
        </div>
        <div class="col-md-3">
            <input type="text" name="items[${itemIndex}][price]" class="form-control price-input filter-numeric-decimal"
                   placeholder="Сотиш нархи" data-original-price=""
                   oninput="validatePrice(this); updateOrderTotals()" required>
        </div>
        <div class="col-md-1">
            <button type="button" class="btn btn-sm" onclick="removeItem(${itemIndex})">❌</button>
        </div>
    </div>`;

        $('#items').append(html);
        $(document).find(`#item-${itemIndex} .filter-select2`).select2({ width: '100%' });
        itemIndex++;

        updateVariationOptions();
        updateOrderTotals();
    }

    function removeItem(index) {
        $(`#item-${index}`).remove();
        updateVariationOptions();
        updateOrderTotals();
    }

    function updateVariationOptions() {
        const selectedIds = getSelectedVariationIds();
        document.querySelectorAll('.filter-select2').forEach(select => {
            const currentVal = select.value;
            select.querySelectorAll('option').forEach(opt => {
                opt.disabled = opt.value && opt.value !== currentVal && selectedIds.includes(opt.value);
            });
        });
    }

    function updatePrice(select, index) {
        const price = parseFloat(select.selectedOptions[0].dataset.price || 0);
        const priceInput = document.querySelector(`input[name="items[${index}][price]"]`);
        if (!priceInput) return;

        let finalPrice = price;

        // 💱 Agar order valyutasi USD bo‘lsa — so‘mdan dollarga o‘tkazamiz
        if (isUSD) {
            finalPrice = price / exchangeRate;
        }

        // 🔹 Formatlash
        priceInput.value = isUSD
            ? finalPrice.toFixed(3) // dollar uchun kasr bilan
            : Math.round(finalPrice).toLocaleString('ru-RU'); // so‘m uchun butun son

        priceInput.dataset.originalPrice = price;

        validatePrice(priceInput);
        updateOrderTotals();
    }

    function validatePrice(input) {
        const minPrice = parseFloat(input.dataset.originalPrice || 0);
        const val = parseFloat((input.value || '').replace(/\s/g, '')) || 0;
        input.style.borderColor = val < minPrice ? 'red' : '';
    }

    function getNumericValue(el) {
        let val = typeof el === 'string' ? $(el).val() : el.value;
        return parseFloat((val || '').replace(/\s/g, '').replace(',', '.')) || 0;
    }

    function updateOrderTotals() {
        let total = 0;
        document.querySelectorAll('.item').forEach(item => {
            const qty = getNumericValue(item.querySelector('[name*="[quantity]"]'));
            const price = getNumericValue(item.querySelector('[name*="[price]"]'));
            total += qty * price;
        });

        const paid = ['#cash_paid', '#card_paid', '#transfer_paid', '#bank_paid']
            .map(id => getNumericValue(id))
            .reduce((a, b) => a + b, 0);

        const remaining = Math.max(total - paid, 0);

        $('#total_price').val(formatValue(total));
        $('#total_amount_paid').val(formatValue(paid));
        $('#remaining_debt').val(formatValue(remaining));
    }

    // int va frac qismni kerakli formatga keltiradi:
    // - thousands separator = space
    // - decimal separator = dot
    function formatNumberWithDot(number, fractionDigits) {
        if (!isFinite(number)) number = 0;

        const fixed = Number(number).toFixed(fractionDigits);
        const parts = fixed.split('.');
        const intPart = parts[0];
        const fracPart = parts[1] || '';
        const intWithSpaces = intPart.replace(/\B(?=(\d{3})+(?!\d))/g, ' ');

        return fractionDigits > 0 ? intWithSpaces + '.' + fracPart : intWithSpaces;
    }

    // currentCurrency global o'zgaruvchiga asoslanib formatlaymiz
    function formatValue(val) {
        const isUSD = (currentCurrency && (currentCurrency.includes('$') || currentCurrency === 'USD'));
        if (isUSD) {
            return formatNumberWithDot(val, 3); // 3 kasr: 1 234.567
        } else {
            return formatNumberWithDot(Math.round(val), 0); // butun son: 1 234
        }
    }
</script>

