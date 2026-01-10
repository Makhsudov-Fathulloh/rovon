@if ($errors->has('general'))
    <div class="alert alert-danger">{{ $errors->first('general') }}</div>
@endif

@if ($errors->has('items'))
    <div class="alert alert-danger">{{ $errors->first('items') }}</div>
@endif

<form action="{{ $action }}" method="POST" id="transferForm">
    @csrf
    @if($method === 'PUT')
        @method('PUT')
    @endif
    <div class="container-fluid mt-4">
        <div class="card shadow">
            <div class="card-body">
                <div class="row">
                    <!-- ORGANIZATION -->
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Филиал</label>
                        <select name="organization_id" id="organization_id" class="form-select select2">
                            <option value="">Танланг...</option>
                            @foreach($organizations as $id => $title)
                                <option value="{{ $id }}"
                                    {{ old('organization_id', $rawMaterialTransfer->organization_id ?? '') == $id ? 'selected' : '' }}>
                                    {{ $title }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- WAREHOUSE -->
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Омбор</label>
                        <select name="warehouse_id" id="warehouse_id" class="form-select select2">
                            <option value="">Танланг...</option>
                            @foreach($warehouses as $id => $title)
                                <option value="{{ $id }}"
                                        data-org="{{ \App\Models\Warehouse::find($id)->organization_id ?? '' }}"
                                    {{ old('warehouse_id', $rawMaterialTransfer->warehouse_id ?? '') == $id ? 'selected' : '' }}>
                                    {{ $title }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- SECTION -->
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Бўлим</label>
                        <select name="section_id" id="section_id" class="form-select select2">
                            <option value="">Танланг...</option>
                            @foreach($sections as $id => $title)
                                <option value="{{ $id }}"
                                        data-org="{{ \App\Models\Section::find($id)->organization_id ?? '' }}"
                                    {{ old('section_id', $rawMaterialTransfer->section_id ?? '') == $id ? 'selected' : '' }}>
                                    {{ $title }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- SHIFT -->
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Смена</label>
                        <select name="shift_id" id="shift_id" class="form-select select2">
                            <option value="">Танланг...</option>
                            @foreach($shifts as $id => $title)
                                <option value="{{ $id }}"
                                        data-section="{{ \App\Models\Shift::find($id)->section_id ?? '' }}"
                                    {{ old('shift_id', $rawMaterialTransfer->shift_id ?? '') == $id ? 'selected' : '' }}>
                                    {{ $title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- TITLE / RECEIVER / STATUS -->
                <div class="row">
                    <div class="col-md-4">
                        <label>Трансфер номи</label>
                        <input type="text" name="title" class="form-control"
                               value="{{ old('title', $rawMaterialTransfer->title ?? '') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label>Олувчи</label>
                        <select name="receiver_id" class="form-select select2" required>
                            <option value="">Танланг...</option>
                            @foreach($users as $id => $name)
                                <option value="{{ $id }}"
                                    {{ old('receiver_id', $rawMaterialTransfer->receiver_id ?? '') == $id ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label>Статус</label>
                        <select name="status" class="form-select">
                            @foreach(\App\Services\StatusService::getList() as $key => $label)
                                <option value="{{ $key }}"
                                    {{ old('status', $rawMaterialTransfer->status ?? '') == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <hr>

                <!-- DYNAMIC RAW MATERIAL ITEMS -->
                <div class="mb-3">
                    <table class="table table-bordered" id="materialsTable">
                        <thead>
                        <tr>
                            <th class="col-md-5">Хомашё</th>
                            <th class="col-md-3">Микдори</th>
                            <th class="col-md-2">Нарх</th>
                            <th class="col-md-2">Умумий</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($rawMaterialTransfer->items as $i => $item)
                            <tr>
                                <td class="col-md-5">
                                    <select name="items[{{ $i }}][raw_material_variation_id]"
                                            class="form-select materialSelect select2" required>
                                        <option value="">Танланг...</option>
                                        @foreach($rawMaterials as $rm)
                                            <option value="{{ $rm->id }}"
                                                    data-price="{{ $rm->price }}"
                                                    data-currency="{{ $rm->currency }}"
                                                {{ $item->raw_material_variation_id == $rm->id ? 'selected' : '' }}>
                                                {{ $rm->code }} - {{ $rm->title }} -
                                                ({{ $rm->unit == 1 ? number_format((float)$rm->count, 3, '.', '') : (int)$rm->count }}
                                                {{ $rm->unit == 1 ? 'кг' : 'дона' }})
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    @php
                                        $rm = $rawMaterials->firstWhere('id', $item->raw_material_variation_id);

                                        $countValue = ($rm && $rm->unit == 1)
                                            ? number_format((float)$item->count, 3, '.', '')  // masalan: 0.500
                                            : (int)$item->count;                              // masalan: 2
                                    @endphp

                                    <input type="text"
                                           name="items[{{ $i }}][count]"
                                           class="form-control qty filter-numeric-decimal"
                                           min="0.001" step="0.001"
                                           value="{{ $countValue }}">
                                </td>

                                <td class="price fw-bold text-success text-center">{{ number_format($item->price, 0, ' ', ' ') }}</td>
                                <td class="total fw-bold text-info text-center">{{ number_format($item->total_price, 2, '.', ' ') }}</td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm removeItem">❌</button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    <button type="button" class="btn btn-sm btn-success" id="addItemBtn">
                        <i class="bi bi-plus-circle"></i> + Хомашё қўшиш
                    </button>
                </div>

                <div class="text-end mb-4">
                    <strong>Умумий сумма:<span id="totalSum"
                                               class="fw-bold text-info"> {{ number_format($rawMaterialTransfer->total_item_price, 2, '.', ' ') ?? 0 }}</span>
                        сўм</strong>
                    <input type="hidden" name="total_item_price" id="total_item_price"
                           value=" {{ $rawMaterialTransfer->total_item_price ?? 0 }}">
                </div>

                <button type="submit" class="btn btn-primary">Янгилаш</button>
            </div>
        </div>
    </div>
</form>

<script>
    $(document).ready(function () {
        $('.select2').select2();

        let rawMaterials = @json($rawMaterials ?? []);
        let itemIndex = {{ $rawMaterialTransfer->items->count() ?? 0 }};

        // Dastlabki filterlar
        const selectedOrg = $('#organization_id').val();
        const selectedSection = $('#section_id').val();
        filterWarehouses(selectedOrg);
        filterSections(selectedOrg);
        filterShifts(selectedSection);

        // Organization o‘zgarsa
        $('#organization_id').on('change', function () {
            const orgId = $(this).val();
            filterWarehouses(orgId);
            filterSections(orgId);
            $('#shift_id').html('<option value="">Аввал бўлимни танланг</option>');
        });

        // Section o‘zgarsa
        $('#section_id').on('change', function () {
            filterShifts($(this).val());
        });

        // Warehouse o‘zgarsa, xomashyolarni yuklash
        $('#warehouse_id').on('change', function () {
            const warehouseId = $(this).val();
            if (!warehouseId) return;
            $.ajax({
                url: "{{ route('raw-material-transfer.raw-materials') }}",
                type: "GET",
                data: {warehouse_id: warehouseId},
                success: function (data) {
                    rawMaterials = data;
                    toastr.success("Хомашёлар янгиланди (" + data.length + " ta)");
                    rebuildMaterialOptions();
                }
            });
        });

        // Sahifa yuklanganda warehouse tanlangan bo‘lsa xomashyolarni yuklash
        const initialWarehouseId = $('#warehouse_id').val();
        if (initialWarehouseId) {
            $.ajax({
                url: "{{ route('raw-material-transfer.raw-materials') }}",
                type: "GET",
                data: {warehouse_id: initialWarehouseId},
                success: function (data) {
                    rawMaterials = data;
                    console.log("✅ Дастлабки хомашёлар юкланди:", data.length);
                }
            });
        }

        function getSelectedMaterialIds() {
            let ids = [];
            $('#materialsTable tbody tr').each(function () {
                const val = $(this).find('.materialSelect').val();
                if (val) ids.push(val);
            });
            return ids;
        }

        // + Хомашё қўшиш
        $('#addItemBtn').on('click', function () {
            if (!rawMaterials || !rawMaterials.length) {
                toastr.warning('Аввал омборни танланг!');
                return;
            }

            const selectedIds = getSelectedMaterialIds(); // ✅ allaqachon tanlanganlar

            let availableMaterials = rawMaterials.filter(r => !selectedIds.includes(r.id.toString()));

            // if (!availableMaterials.length) {
            //     toastr.warning('Барча хомашёлар аллақачон танланган!');
            //     return;
            // }

            let row = `<tr>
        <td>
            <select name="items[${itemIndex}][raw_material_variation_id]" class="form-select materialSelect select2" required>
                <option value="">Танланг...</option>`;

            availableMaterials.forEach(r => {
                let unit = r.unit === 1 ? 'кг' : 'дона';
                let countDisplay = r.unit === 1 ? parseFloat(r.count).toFixed(3) : parseInt(r.count);
                let price = parseFloat(r.price);
                row += `<option value="${r.id}" data-price="${price}" data-currency="${r.currency}">
            ${r.code} - ${r.title} - (${countDisplay} ${unit})
        </option>`;
            });

            row += `</select></td>
            <td><input type="text" name="items[${itemIndex}][count]" class="form-control qty filter-numeric-decimal" min="0.001" step="0.001" required></td>
            <td class="price fw-bold text-success text-center">0</td>
            <td class="total fw-bold text-info text-center">0</td>
            <td><button type="button" class="btn btn-sm removeItem">❌</button></td>
        </tr>`;

            $('#materialsTable tbody').append(row);

            $('.materialSelect').select2({
                placeholder: "Танланг...",
                allowClear: true,
                minimumInputLength: 2,
                language: {
                    inputTooShort: () => "Камида 2 та белги киритинг",
                    noResults: () => "Ҳеч қандай натижа топилмади"
                },
                width: '100%',
            });

            filterNumericDecimal();
            itemIndex++;
        });

        function filterNumericDecimal() {
            $(".filter-numeric-decimal").inputmask({
                alias: "decimal",
                groupSeparator: " ",
                placeholder: "",
                autoGroup: true,
                rightAlign: false,
                allowMinus: false,
                digits: 3,
                digitsOptional: true,
                showMaskOnHover: false,
            });
        }

        // Valyuta & kurs
        const CURRENCY_UZS = @json(\App\Services\StatusService::CURRENCY_UZS);
        const CURRENCY_USD = @json(\App\Services\StatusService::CURRENCY_USD);
        const USD_RATE = {{ $usdRate ?? 0 }};

        // Sahifa yuklanganda mavjud satrlar uchun valyutani chiqarish
        $('#materialsTable tbody tr').each(function () {
            const row = $(this);
            const selected = row.find('.materialSelect :selected');
            const price = parseFloat(selected.data('price')) || 0;
            const currency = selected.data('currency') || CURRENCY_UZS;

            // 💰 USD -> 2 kasr bilan, so‘м -> butun son
            const formattedPrice = currency == CURRENCY_USD
                ? price.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ' ')
                : Math.round(price).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ');

            const currencySymbol = (currency === CURRENCY_UZS) ? 'сўм' : '$';

            // 🔒 Narxni faqat ko‘rsatishda formatlash, hisoblash uchun asl qiymatni data-price sifatida saqlaymiz
            row.find('.price')
                .html(formattedPrice + ' ' + currencySymbol)
                .data('price', price);
            row.data('currency', currency);
            updateRowTotal(row);
        });

        // Row total va umumiy summani hisoblash
        $(document).on('change', '.materialSelect', function () {
            const row = $(this).closest('tr');
            const selected = $(this).find(':selected');
            const price = parseFloat(selected.data('price')) || 0;
            const currency = selected.data('currency') || CURRENCY_UZS;

            const formattedPrice = currency == CURRENCY_USD
                ? price.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ' ')
                : Math.round(price).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ');

            const currencySymbol = currency === CURRENCY_UZS ? 'сўм' : '$';

            row.find('.price')
                .html(formattedPrice + ' ' + currencySymbol)
                .data('price', price);
            row.data('currency', currency);
            updateRowTotal(row);
        });

        // Miqdor o‘zgarganda
        $(document).on('input', '.qty', function () {
            updateRowTotal($(this).closest('tr'));
        });

        // ❌ item o‘chirish va deleted_items[] qo‘shish
        $(document).on('click', '.removeItem', function () {
            const row = $(this).closest('tr');
            const itemId = row.data('item-id');
            if (itemId) {
                $('#transferForm').append(`<input type="hidden" name="deleted_items[]" value="${itemId}">`);
            }
            row.remove();
            updateTotalSum();
        });

        function updateRowTotal(row) {
            let qty = parseFloat(row.find('.qty').val()) || 0;
            let price = parseFloat(row.find('.price').data('price')) || 0;
            let currency = row.data('currency') || CURRENCY_UZS;

            let total = qty * price;
            // Agar USD bo‘lsa so‘mga aylantiramiz
            if (currency === CURRENCY_USD) {
                total = total * USD_RATE;
                currency = CURRENCY_UZS;
            }

            const currencySymbol = currency === CURRENCY_UZS ? 'сўм' : '$';

            let totalText = (qty % 1 !== 0 ? total.toFixed(3) : total.toFixed(2));
            row.find('.total').html(
                totalText.replace(/\B(?=(\d{3})+(?!\d))/g, ' ') + ' ' + currencySymbol);

            // row.find('.total').html(
            //     total.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ' ') + ' ' + currencySymbol);

            updateTotalSum();
        }

        function updateTotalSum() {
            let sumUZS = 0;

            $('#materialsTable tbody tr').each(function () {
                const row = $(this);
                const totalText = row.find('.total').text().replace(/[^\d.]/g, '');
                const total = parseFloat(totalText) || 0;
                sumUZS += total; // row.total allaqachon so‘mga o‘tkazilgan
            });

            // Formatlab chiqarish
            let sumText = sumUZS.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
            $('#totalSum').text(sumText + ' сўм');
            $('#total_item_price').val(sumUZS);
        }

        function rebuildMaterialOptions() {
            $('.materialSelect').each(function () {
                const currentVal = $(this).val();
                $(this).empty().append('<option value="">Танланг...</option>');

                rawMaterials.forEach(r => {
                    let unit = r.unit === 1 ? 'кг' : 'дона';
                    let countDisplay = r.unit === 1 ? parseFloat(r.count).toFixed(3) : parseInt(r.count);
                    let price = parseFloat(r.price);
                    $(this).append(`<option value="${r.id}" data-price="${price}">${r.title} - (${countDisplay} ${unit})</option>`);
                });

                $(this).val(currentVal).trigger('change');
            });
        }

        // Filter functions
        function filterWarehouses(orgId) {
            $('#warehouse_id option').hide();
            $('#warehouse_id option[value=""]').show();
            $('#warehouse_id option[data-org="' + orgId + '"]').show();
        }

        function filterSections(orgId) {
            $('#section_id option').hide();
            $('#section_id option[value=""]').show();
            $('#section_id option[data-org="' + orgId + '"]').show();
        }

        function filterShifts(sectionId) {
            $('#shift_id option').hide();
            $('#shift_id option[value=""]').show();
            $('#shift_id option[data-section="' + sectionId + '"]').show();
        }
    });
</script>
