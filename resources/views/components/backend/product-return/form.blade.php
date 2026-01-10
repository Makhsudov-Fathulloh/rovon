<style>
    /* Umumiy dizayn sozlamalari */
    .form-control:focus, .form-select:focus {
        background-color: #fff !important;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.1);
        border-color: #86b7fe;
    }
    .table > :not(caption) > * > * {
        padding: 0.75rem 0.5rem;
    }

    /* Select2 hover va vizual qismi */
    .select2-container--bootstrap-5 .select2-selection {
        border: none !important;
        background-color: #f8f9fa !important; /* bg-light o'rniga to'g'ri kod */
        min-height: 40px;
        display: flex;
        align-items: center;
    }

    /* RO'YXATDA HOVER BO'LGANDA FON RANGI */
    .select2-container--bootstrap-5 .select2-results__option--highlighted[aria-selected] {
        background-color: #0d6efd !important; /* Professional ko'k rang */
        color: #fff !important;
    }

    /* Tanlangan (lekin hover bo'lmagan) element */
    .select2-container--bootstrap-5 .select2-results__option[aria-selected=true] {
        background-color: #e9ecef !important;
        color: #6c757d !important;
    }

    .item-row {
        transition: all 0.2s ease-in-out;
    }

    .item-row:hover {
        background-color: rgba(13, 110, 253, 0.03) !important; /* Juda och ko'k fon */
        box-shadow: inset 4px 0 0 0 #0d6efd; /* Chap tomondan ko'k chiziq */
    }

    /* Hover bo'lganda inputlarni biroz oqroq qilish (kontrast uchun) */
    .item-row:hover .form-control,
    .item-row:hover .select2-selection {
        background-color: #fff !important;
        border-color: #dee2e6 !important;
    }

    /* O'chirish tugmasi stili */
    .remove-row {
        text-decoration: none;
        transition: all 0.2s;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    /* Mobil versiya */
    @media (max-width: 768px) {
        /* Jadval sarlavhasini yashiramiz */
        .table thead {
            display: none;
        }

        /* Har bir qatorni alohida blok qilamiz */
        .item-row {
            display: block;
            border: 1px solid #eee;
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 8px;
            background: #fff;
            position: relative;
        }

        .item-row td {
            display: block;
            width: 100% !important;
            border: none;
            padding: 5px 0 !important;
            text-align: left !important;
        }

        /* Mobil qurilmada har bir input oldidan sarlavha chiqaramiz */
        .item-row td:before {
            content: attr(data-label);
            font-weight: bold;
            display: block;
            font-size: 0.8rem;
            color: #6c757d;
            margin-bottom: 2px;
        }

        /* O'chirish tugmasini yuqori o'ng burchakka qo'yamiz */
        .item-row .remove-row-cell {
            position: absolute;
            top: 5px;
            right: 5px;
        }
        .item-row .remove-row-cell:before {
            content: ""; /* O'chirish tugmasi uchun sarlavha shartmas */
        }

        .row-total {
            font-size: 1.1rem;
            color: #0d6efd;
        }
    }
</style>

<form action="{{ $action }}" method="POST" enctype="multipart/form-data" id="order-form">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif

    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card border-0 shadow-sm">

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead class="table-light">
                                    <tr class="text-center">
                                        <th class="ps-4" style="width: 45%;">Махсулот</th>
                                        <th style="width: 15%;">Миқдор</th>
                                        <th style="width: 15%;">Нарх</th>
                                        <th class="text-end" style="width: 20%;">Жами</th>
                                        <th class="text-center" style="width: 5%;"></th>
                                    </tr>
                                </thead>
                                <tbody id="items-wrapper">
                                    @php
                                        // 1. Agar validatsiyadan xato qaytsa (old)
                                        // 2. Agar tahrirlash bo'lsa (productReturn->items)
                                        // 3. Agar yangi yaratish bo'lsa (bo'sh massiv)
                                        $items = old('items');

                                        if (!$items) {
                                            if (isset($productReturn) && $productReturn->items->count() > 0) {
                                                $items = $productReturn->items->map(function($item) {
                                                    return [
                                                        'product_variation_id' => $item->product_variation_id,
                                                        'count' => $item->count,
                                                        'price' => $item->price,
                                                    ];
                                                })->toArray();
                                            } else {
                                                $items = [[]]; // Default bitta bo'sh qator
                                            }
                                        }
                                    @endphp

                                    @foreach($items as $i => $item)
                                        <tr class="item-row">
                                            <td class="ps-4" data-label="Махсулот">
                                                <select name="items[{{ $i }}][product_variation_id]"
                                                        class="form-select variation-select" required>
                                                    <option value="">Махсулотни танланг</option>
                                                    @foreach($variations as $v)
                                                        <option value="{{ $v->id }}"
                                                            data-price="{{ $v->price }}"
                                                            data-rate="{{ $v->rate }}"
                                                            {{ (isset($item['product_variation_id']) && $item['product_variation_id'] == $v->id) ? 'selected' : '' }}>
                                                            {{ $v->title }} — {{ $v->product->title }}
                                                            ({{ number_format($v->price, 0, '', ' ') }} сўм)
                                                            [{{ \App\Helpers\CountHelper::format($v->count, $v->unit) }}]
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td data-label="Миқдор">
                                                <input type="number" name="items[{{ $i }}][count]"
                                                    step="0.001" class="form-control border-0 bg-light count"
                                                    placeholder="0.00" value="{{ $item['count'] ?? '' }}" required>
                                            </td>
                                            <td data-label="Нарх">
                                                <input type="number" name="items[{{ $i }}][price]"
                                                    class="form-control border-0 bg-light price"
                                                    value="{{ $item['price'] ?? '' }}" placeholder="0" required>
                                            </td>
                                            <td class="text-end fw-bold text-dark pe-3" data-label="Жами">
                                                <span class="row-total text-secondary">0</span>
                                            </td>
                                            <td class="text-center pe-4 remove-row-cell">
                                                <button type="button" class="btn text-danger remove-row p-0">
                                                    ❌
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                      <div class="card-header bg-white py-3 border-bottom-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-primary btn-sm px-3" id="add-row">
                                <i class="bi bi-plus-lg"></i> + Mahsulot qo'shish
                            </button>
                            <div class="mb-3">
                                <h2 class="fw-bold text-primary mb-0" id="grand-total">0</h2>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-white py-4 border-top">
                        <div class="col-md-12 text-end">
                            <button type="submit" class="btn btn-success px-5 fw-bold shadow-sm rounded-3">
                                Сақлаш
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    $(document).ready(function() {

        // 1. Tanlangan mahsulotlarni yig'ish (Takrorlanishni oldini olish uchun)
        function getSelectedIDs() {
            let selectedIDs = [];
            $('.variation-select').each(function() {
                let val = $(this).val();
                if (val) selectedIDs.push(val);
            });
            return selectedIDs;
        }

        // 2. Select2 ni sozlash va takrorlanishni tekshirish
        function initSelect2(element) {
            $(element).select2({
                theme: 'bootstrap-5',
                placeholder: 'Махсулотни танланг',
                width: '100%',
                allowClear: true
            }).on('select2:opening', function() {
                // Ro'yxat ochilayotganda boshqa selectlarda tanlanganlarni o'chirish
                let currentVal = $(this).val();
                let allSelected = getSelectedIDs();

                $(this).find('option').each(function() {
                    let optVal = $(this).val();
                    if (optVal && allSelected.includes(optVal) && optVal !== currentVal) {
                        $(this).prop('disabled', true);
                    } else {
                        $(this).prop('disabled', false);
                    }
                });
            }).on('select2:select', function (e) {
                // Mahsulot tanlanganda narxni olish
                const data = e.params.data.element.dataset;
                const row = $(this).closest('tr');

                const price = parseFloat(data.price) || 0;
                const rate = parseFloat(data.rate) || 1;

                // row.find('.price').val((price * rate).toFixed(0));
                row.find('.price').val(Math.round(price * rate));

                // Tanlangandan so'ng avtomatik "Miqdor" inputiga fokus qilish (UX uchun)
                row.find('.count').focus();

                recalc();
            }).on('select2:clear', function() {
                recalc();
            });
        }

        // 3. Hisob-kitob funksiyasi
        function recalc() {
            let grandTotal = 0;
            $('.item-row').each(function() {
                const count = parseFloat($(this).find('.count').val()) || 0;
                const price = parseFloat($(this).find('.price').val()) || 0;
                const total = count * price;

                $(this).find('.row-total').text(total.toLocaleString('uz-UZ'));
                grandTotal += total;
            });
            $('#grand-total').text(grandTotal.toLocaleString('uz-UZ'));
        }

        // 4. Dastlabki yuklanish
        $('.variation-select').each(function() {
            initSelect2(this);
        });
        recalc();

        // 5. Yangi qator qo'shish
        $('#add-row').click(function() {
            const tbody = $('#items-wrapper');
            const index = Date.now(); // Indeks takrorlanmasligi uchun vaqtdan foydalanamiz

            const newRow = `
                <tr class="item-row animate__animated animate__fadeIn">
                    <td class="ps-4">
                        <select name="items[${index}][product_variation_id]" class="form-select variation-select" required>
                            <option value="">Qidirish...</option>
                            @foreach($variations as $v)
                                <option value="{{ $v->id }}" data-price="{{ $v->price }}" data-rate="{{ $v->rate }}">
                                    {{ $v->title }} — {{ $v->product->title }} ({{ number_format($v->price, 0, '', ' ') }} сўм) [{{ \App\Helpers\CountHelper::format($v->count, $v->unit) }}]
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input type="number" name="items[${index}][count]" step="0.001" class="form-control border-0 bg-light count" placeholder="0.00" required>
                    </td>
                    <td>
                        <input type="number" name="items[${index}][price]" class="form-control border-0 bg-light price" placeholder="0">
                    </td>
                    <td class="text-end fw-bold text-dark pe-3">
                        <span class="row-total text-secondary">0</span>
                    </td>
                    <td class="text-center pe-4">
                        <button type="button" class="btn text-danger remove-row p-0">
                            ❌
                        </button>
                    </td>
                </tr>`;

            const $newRowHtml = $(newRow);
            tbody.append($newRowHtml);
            initSelect2($newRowHtml.find('.variation-select'));

            // Yangi qatordagi selectni avtomatik ochish
            // $newRowHtml.find('.variation-select').select2('open');
        });

        // 6. Voqealar (Events)
        $(document).on('input', '.count, .price', recalc);

        $(document).on('click', '.remove-row', function() {
            if ($('.item-row').length > 1) {
                $(this).closest('tr').remove();
                recalc();
            } else {
                alert("Kamida bitta mahsulot bo'lishi lozim.");
            }
        });
    });
</script>
