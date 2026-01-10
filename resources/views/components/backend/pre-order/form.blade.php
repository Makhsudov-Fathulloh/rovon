@props(['pre_order' => null, 'users', 'method', 'action'])

<form action="{{ $action }}" method="POST">
    @csrf
    @if($method === 'PUT')
        @method('PUT')
    @endif

    <div class="container-fluid mt-4">
        <div class="card shadow">
            <div class="card-body">

                <div class="col-md-12 mb-3">
                    <label class="form-label">Буюртмачи</label>
                    <select name="user_id" class="form-control filter-select2" required>
                        <option value="">Танланг…</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}"
                                {{ old('user_id', $pre_order->user_id ?? null) == $u->id ? 'selected' : '' }}>
                                {{ $u->username }} ({{ \App\Services\PhoneFormatService::uzPhone($u->phone) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="title" class="form-label">Номи</label>
                    <input type="text" id="title" name="title" class="form-control"
                           value="{{ old('title', $pre_order->title ?? '') }}">
                    @error('title')
                    <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Тавсифи</label>
                    <textarea id="description" name="description" class="form-control ckeditor" rows="10">
                        {{ old('description', $pre_order->description ?? '') }}
                    </textarea>
                </div>

                @if (Route::currentRouteName() == 'pre-order.edit')
                    <div class="mb-5">
                        <label class="form-label">Статус</label>
                        <select name="status" class="form-control">
                            @foreach (\App\Models\PreOrder::getStatusList() as $key => $label)
                                <option value="{{ $key }}"
                                    {{ old('status', $pre_order->status) == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="table-responsive mb-3">
                    <table class="table table-bordered align-middle" id="itemTable">
                        <thead class="table-light">
                        <tr class="text-center">
                            <th>Маҳсулот</th>
                            <th>Код</th>
                            <th>Номи</th>
                            <th>Миқдори</th>
                            <th>Тури</th>
                            <th></th>
                        </tr>
                        </thead>

                        <tbody>
                        @if(isset($pre_order) && $pre_order->preOrderItems)
                            @foreach($pre_order->preOrderItems as $i => $item)
                                <tr class="text-center">
                                    <td>
                                        <select class="form-control product-select2"
                                                name="items[{{ $i }}][product_variation_id]" required>
                                            <option value="{{ $item->product_variation_id }}" selected>
                                                {{ ($item->code ? $item->code.' — ' : '') . $item->title }}
                                            </option>
                                        </select>
                                        @error('items[{{ $i }}][product_variation_id]')
                                        <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td>
                                        <input type="text" class="form-control code-field"
                                               name="items[{{ $i }}][code]"
                                               value="{{ $item->code }}" readonly>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control title-field"
                                               name="items[{{ $i }}][title]"
                                               value="{{ $item->title }}" readonly>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control count-field filter-numeric"
                                               name="items[{{ $i }}][count]"
                                               value="{{ $item->count }}" required>
                                        @error('items[{{ $i }}][count]')
                                        <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td>
                                        <input type="text" class="form-control type-field"
                                               value="{{ \App\Services\StatusService::getTypeCount()[$item->unit] }}"
                                               readonly>
                                        <input type="hidden" name="items[{{ $i }}][unit]"
                                               value="{{ $item->unit }}">
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm remove-item">❌</button>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>

                <div class="row">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <button type="button" class="btn btn-outline-primary" id="addItemBtn">
                            + Маҳсулот қўшиш
                        </button>
                        @if (Route::currentRouteName() == 'pre-order.create')
                            <button type="submit" class="btn btn-success">{{ 'Сақлаш' }}</button>
                        @elseif (Route::currentRouteName() == 'pre-order.edit')
                            <button type="submit" class="btn btn-success">{{ 'Янгилаш' }}</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

{{-- ✅ TEMPLATE ROW --}}
<template id="itemRowTemplate">
    <tr>
        <td>
            <select class="form-control product-select2" name="__NAME__[product_variation_id]" required></select>
        </td>
        <td><input type="text" class="form-control code-field" name="__NAME__[code]" readonly></td>
        <td><input type="text" class="form-control title-field" name="__NAME__[title]" readonly></td>
        <td><input type="text" class="form-control count-field filter-numeric" name="__NAME__[count]" required>
        </td>
        <td>
            <input type="text" class="form-control type-label" readonly>
            <input type="hidden" class="type-value" name="__NAME__[unit]">
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm remove-item">❌</button>
        </td>
    </tr>
</template>

<script>
    let itemIndex = {{ isset($pre_order) ? $pre_order->preOrderItems->count() : 0 }};

    // ✅ ADD ITEM
    document.getElementById('addItemBtn').addEventListener('click', function () {
        let tpl = document.getElementById('itemRowTemplate').innerHTML;
        tpl = tpl.replace(/__NAME__/g, 'items[' + itemIndex + ']');

        const tbody = document.querySelector('#itemTable tbody');
        tbody.insertAdjacentHTML('beforeend', tpl);

        initSelect2(tbody.lastElementChild);
        itemIndex++;
    });

    // ✅ REMOVE ITEM
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-item')) {
            e.target.closest('tr').remove();
        }
    });

    // ✅ SELECT2 INIT
    function initSelect2(row) {
        const sel = $(row).find('.product-select2');

        sel.select2({
            placeholder: 'Код ёки ном киритинг…',
            allowClear: true,
            width: '100%',
            ajax: {
                url: '{{ route('ajax.product') }}',
                dataType: 'json',
                delay: 250,
                data: params => ({
                    q: params.term
                }),
                processResults: data => {
                    // 🔥 Barcha tanlangan product_variation_id larni to‘playmiz
                    const selectedIds = Array.from(document.querySelectorAll('.product-select2'))
                        .map(el => $(el).val())
                        .filter(Boolean)
                        .map(Number);

                    // 🔥 Tanlanganlarni filtrlab tashlaymiz
                    const filtered = data.results.filter(item => !selectedIds.includes(Number(item.id)));

                    return { results: filtered };
                }
            }
        });

        // ✅ Tanlanganda inputlarni to‘ldirish
        sel.on('select2:select', (e) => {
            let d = e.params.data;
            $(row).find('.code-field').val(d.code || '');
            $(row).find('.title-field').val(d.title || '');
            $(row).find('.type-value').val(d.unit || '');
            $(row).find('.type-label').val(d.type_label || '');
        });

        // ✅ Tozalanganda inputlarni tozalash
        sel.on('select2:clear', () => {
            $(row).find('.code-field, .title-field, .type-label, .type-value').val('');
        });
    }

    // ✅ INITIALIZE EXISTING ROWS (edit mode)
    document.querySelectorAll('#itemTable tbody tr').forEach(row => initSelect2(row));
</script>
