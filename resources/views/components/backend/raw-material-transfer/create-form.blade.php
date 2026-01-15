{{--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">--}}
{{--<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>--}}

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
                                <option
                                    value="{{ $id }}" {{ isset($rawMaterialTransfer) && $rawMaterialTransfer->organization_id == $id ? 'selected' : '' }}>
                                    {{ $title }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- WAREHOUSE -->
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Омбор</label>
                        <select name="warehouse_id" id="warehouse_id" class="form-select select2" disabled>
                            <option value="">Аввал филиални танланг</option>
                        </select>
                    </div>

                    <!-- SECTION -->
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Бўлим</label>
                        <select name="section_id" id="section_id" class="form-select select2" disabled>
                            <option value="">Аввал омборни танланг</option>
                        </select>
                    </div>

                    <!-- SHIFT -->
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Смена</label>
                        <select name="shift_id" id="shift_id" class="form-select select2" disabled>
                            <option value="">Аввал бўлимни танланг</option>
                        </select>
                    </div>
                </div>

                <!-- TITLE -->
                <div class="row">
                    <div class="col-md-4">
                        <label>Трансфер номи</label>
                        <input type="text" name="title" class="form-control"
                               value="{{ $rawMaterialTransfer->title ?? '' }}" required>
                    </div>
                    <div class="col-md-4">
                        <label>Олувчи</label>
                        <select name="receiver_id" class="form-select select2" required>
                            <option value="">Танланг...</option>
                            @foreach($users as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label>Статус</label>
                        <select name="status" class="form-select">
                            @foreach(\App\Services\StatusService::getList() as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
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
                        </tbody>
                    </table>

                    <button type="button" class="btn btn-sm btn-success" id="addItemBtn">
                        <i class="bi bi-plus-circle"></i> + Хомашё қўшиш
                    </button>
                </div>

                <!-- TOTAL -->
                <div class="text-end mb-4">
                    <strong>Умумий сумма: <span id="totalSum" class="fw-bold text-info"> 0</span> сўм</strong>
                    <input type="hidden" name="total_item_price" id="total_item_price">
                </div>

                <button type="submit" class="btn btn-info">Сақлаш</button>
            </div>
        </div>
    </div>
</form>

<script>
    $(document).ready(function () {
        $('.select2').select2();

        let rawMaterials = @json($rawMaterials ?? []);
        let itemIndex = 0;

        // ORGANIZATION tanlash
        // $('#organization_id').on('change', function () {
        //     const orgId = $(this).val();

        //     $('#warehouse_id').prop('disabled', !orgId);
        //     $('#section_id').prop('disabled', !orgId);
        //     $('#shift_id').prop('disabled', true);

        //     $('#warehouse_id').html('<option value="">Омбор танланг...</option>');
        //     $('#section_id').html('<option value="">Бўлим танланг...</option>');
        //     $('#shift_id').html('<option value="">Аввал бўлимни танланг</option>');

        //     if (orgId) {
        //         @foreach($warehouses as $id => $title)
        //         if ("{{ \App\Models\Warehouse::find($id)->organization_id ?? '' }}" == orgId)
        //             $('#warehouse_id').append(`<option value="{{ $id }}">{{ $title }}</option>`);
        //         @endforeach

        //             @foreach($sections as $id => $title)
        //         if ("{{ \App\Models\Section::find($id)->organization_id ?? '' }}" == orgId)
        //             $('#section_id').append(`<option value="{{ $id }}">{{ $title }}</option>`);
        //         @endforeach
        //     }
        // });

        $('#organization_id').on('change', function () {
            const orgId = $(this).val();

            // Selectlarni tozalash va kutish holatiga keltirish
            $('#warehouse_id').prop('disabled', !orgId).html('<option value="">Юкланмоқда...</option>');
            $('#section_id').prop('disabled', !orgId).html('<option value="">Юкланмоқда...</option>');
            $('#shift_id').prop('disabled', true).html('<option value="">Аввал бўлимни танланг</option>');

            if (orgId) {
                $.ajax({
                    url: "{{ route('raw-material-transfer.get-warehouses') }}",
                    type: "GET",
                    data: { organization_id: orgId },
                    success: function (response) {
                        // 1. Omborlarni to'ldirish
                        let wOptions = '<option value="">Омбор танланг...</option>';
                        $.each(response.warehouses, function (id, title) {
                            wOptions += `<option value="${id}">${title}</option>`;
                        });
                        $('#warehouse_id').html(wOptions);

                        // 2. Bo'limlarni to'ldirish (Endi bular ham keladi)
                        let sOptions = '<option value="">Бўлим танланг...</option>';
                        $.each(response.sections, function (id, title) {
                            sOptions += `<option value="${id}">${title}</option>`;
                        });
                        $('#section_id').html(sOptions);

                        // Select2 bo'lsa yangilab qo'yamiz
                        $('.select2').trigger('change.select2');
                    },
                    error: function () {
                        toastr.error("Маълумотларни юклашда хатолик!");
                    }
                });
            }
        });

        // SECTION => SHIFTLAR
        $('#section_id').on('change', function () {
            const sectionId = $(this).val();
            $('#shift_id').prop('disabled', !sectionId);
            $('#shift_id').html('<option value="">Смена танланг...</option>');

            if (sectionId) {
                @foreach($shifts as $id => $title)
                if ("{{ \App\Models\Shift::find($id)->section_id ?? '' }}" == sectionId)
                    $('#shift_id').append(`<option value="{{ $id }}">{{ $title }}</option>`);
                @endforeach
            }
        });

        // WAREHOUSE => AJAX orqali xomashyo
        $('#warehouse_id').on('change', function () {
            const warehouseId = $(this).val();
            if (!warehouseId) {
                rawMaterials = [];
                rebuildMaterialOptions();
                return;
            }

            $.ajax({
                url: "{{ route('raw-material-transfer.raw-materials') }}",
                type: "GET",
                data: {warehouse_id: warehouseId},
                success: function (data) {
                    rawMaterials = data;
                    toastr.success("Хомашёлар янгиланди (" + data.length + " та)");
                    rebuildMaterialOptions();
                },
                error: function () {
                    toastr.error("Хомашёларни юклаб бўлмади");
                }
            });
        });

        // ITEM qo‘shish
        $('#addItemBtn').on('click', function () {
            if (!rawMaterials || !rawMaterials.length) {
                alert('Аввал омборни танланг!');
                return;
            }

            // 🔥 Hozir tanlangan xomashyolarni yig‘amiz
            let selectedIds = $('.materialSelect').map(function () {
                return $(this).val();
            }).get();

            let row = `<tr>
                <td>
                    <select name="items[${itemIndex}][raw_material_variation_id]" class="form-select materialSelect" required>
                        <option value="">Танланг...</option>`;

            rawMaterials.forEach(r => {
                if (selectedIds.includes(r.id.toString())) return;

                let unit = r.unit === {{ \App\Services\StatusService::UNIT_PSC }} ? 'дона' : 'кг';
                let countDisplay = r.unit === {{ \App\Services\StatusService::UNIT_PSC }} ? parseInt(r.count) : parseFloat(r.count).toFixed(3);

                row += `<option value="${r.id}"
                data-price="${r.price}"
                data-currency="${r.currency}"
                data-code="${r.code}">
            ${r.code} - ${r.title} - (${countDisplay} ${unit})
        </option>`;
            });

            row += `</select></td>
                <td><input type="text" name="items[${itemIndex}][count]" class="form-control qty filter-numeric-decimal" min="0.001" step="0.001" required></td>
                <td class="price fw-bold text-success text-center" data-price="0" data-currency="{{ \App\Services\StatusService::CURRENCY_UZS }}">0</td>
                <td class="total fw-bold text-info text-center">0</td>
                <td class="text-center"><button type="button" class="btn btn-sm removeItem">❌</button></td>
            </tr>`;

            $('#materialsTable tbody').append(row);

            // Select2
            $('#materialsTable tbody tr:last .materialSelect').select2({
                placeholder: "Танланг...",
                allowClear: true,
                minimumInputLength: 2,
                language: {
                    inputTooShort: () => "Камида 2 та белги киритинг",
                    noResults: () => "Ҳеч қандай натижа топилмади"
                },
                width: '100%',

                // ⭐ QO‘SHILGAN QISM
                matcher: function (params, data) {
                    if ($.trim(params.term) === '') return data;

                    let term = params.term.toLowerCase();

                    // option text → ichida code + title bor
                    let text = (data.text || '').toLowerCase();

                    // qo‘shimcha: agar siz kodni alohida berayotgan bo‘lsangiz:
                    let code = $(data.element).data('code') ? $(data.element).data('code').toString().toLowerCase() : '';

                    if (text.indexOf(term) > -1 || code.indexOf(term) > -1) {
                        return data;
                    }

                    return null;
                }
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

        // VALYUTA & KURS
        const CURRENCY_UZS = @json(\App\Services\StatusService::CURRENCY_UZS);
        const CURRENCY_USD = @json(\App\Services\StatusService::CURRENCY_USD);
        const USD_RATE = {{ $usdRate ?? 0 }};

        // Xomashyo tanlanganda
        // USD: 2 xonali nuqta-decimal va minglik ajratgichi bo'shliq
        $(document).on('change', '.materialSelect', function () {
            const price = parseFloat($(this).find(':selected').data('price')) || 0;
            const currency = $(this).find(':selected').data('currency');
            const row = $(this).closest('tr');

            // formatni valyutaga qarab tanlaymiz
            // const formattedPrice = currency == CURRENCY_USD
            //     ? price.toLocaleString('ru-RU', {minimumFractionDigits: 2, maximumFractionDigits: 2})
            //     : price.toLocaleString('ru-RU', {maximumFractionDigits: 0});

            const formattedPrice = currency == CURRENCY_USD
                ? price.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ' ')
                : Math.round(price).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ');

            const currencySymbol = currency == CURRENCY_UZS ? 'сўм' : '$';

            row.find('.price')
                .text(formattedPrice + ' ' + currencySymbol)
                .attr('data-currency', currency)
                .attr('data-price', price);

            updateRowTotal(row);
        });

        // Miqdor o‘zgarganda
        $(document).on('input', '.qty', function () {
            updateRowTotal($(this).closest('tr'));
        });

        // Satr summasini hisoblash
        function updateRowTotal(row) {
            const qty = parseFloat(row.find('.qty').val()) || 0;
            const price = parseFloat(row.find('.price').data('price')) || 0;
            const currency = row.find('.price').data('currency');

            const priceInUzs = (currency == CURRENCY_USD) ? (price * USD_RATE) : price;
            const total = qty * priceInUzs;

            row.find('.total').text(total.toLocaleString('ru-RU', {maximumFractionDigits: 0}) + ' сўм');

            updateTotalSum();
        }

        // Umumiy summani hisoblash
        function updateTotalSum() {
            let totalSum = 0;

            $('#materialsTable tbody tr').each(function () {
                const price = parseFloat($(this).find('.price').data('price')) || 0;
                const currency = $(this).find('.price').data('currency');
                const qty = parseFloat($(this).find('.qty').val()) || 0;

                const priceInUzs = (currency == CURRENCY_USD) ? (price * USD_RATE) : price;
                totalSum += priceInUzs * qty;
            });

            $('#totalSum').text(totalSum.toLocaleString('ru-RU', {maximumFractionDigits: 0}));
            $('#total_item_price').val(totalSum);
        }


        // Xomashyolarni qayta yuklash
        function rebuildMaterialOptions() {
            $('.materialSelect').each(function () {
                const $sel = $(this);
                const currentVal = $sel.val();
                $sel.empty().append('<option value="">Танланг...</option>');
                if (rawMaterials.length) {
                    rawMaterials.forEach(r => {
                        $sel.append(`<option value="${r.id}" data-price="${r.price}" data-currency="${r.currency}">
                            ${r.title}
                        </option>`);
                    });
                }
                if (currentVal) {
                    $sel.val(currentVal).trigger('change');
                }
            });
        }

        // Qatorni o‘chirish
        $(document).on('click', '.removeItem', function () {
            $(this).closest('tr').remove();
            updateTotalSum();
        });
    });
</script>
