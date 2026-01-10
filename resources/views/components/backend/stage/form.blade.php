<form action="{{ $action }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if ($method === 'PUT')
        @method('PUT')
    @endif
    <div class="container-fluid mt-4">
        <div class="row">

            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-body">
                        <div class="tab-content" style="margin: 0">
                            <div class="tab-pane fade show active">

                                <div class="col-md-12 mb-3">
                                    <label for="organization_id"><strong>Филиал</strong></label>
                                    <select name="organization_id" id="organization_id" class="form-control" required>
                                        <option value="">Филиални танланг</option>
                                        @foreach($organizations as $id => $title)
                                            <option value="{{ $id }}"
                                                {{ old('organization_id', $stage->section->organization_id ?? '') == $id ? 'selected' : '' }}>
                                                {{ $title }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('organization_id')
                                    <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label for="section_id"><strong>Бўлим (сектор)</strong></label>
                                    <select name="section_id" id="section_id" class="form-control" required>
                                        <option value="">Бўлимни танланг</option>
                                    </select>
                                    @error('section_id')
                                    <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label for="pre_stage_id"><strong>Олдинги маҳсулот</strong></label>
                                    <select name="pre_stage_id" id="pre_stage_id" class="form-control" disabled>
                                        <option value="">Олдинги маҳсулотни танланг</option>
                                    </select>
                                    @error('pre_stage_id')
                                    <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="title" class="form-label">Номи</label>
                                    <input type="text" id="title" name="title" class="form-control"
                                           value="{{ old('title', $stage->title ?? '') }}">
                                    @error('title')
                                    <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Тавсифи</label>
                                    <textarea id="description"
                                              name="description"
                                              class="form-control ckeditor"
                                              rows="10">
                                                {{ old('description', $stage->description ?? '') }}
                                            </textarea>
                                </div>

                                <div class="mb-3">
                                    <h5>Махсулот материаллари:</h5>
                                    <div id="stage-materials-wrapper">
                                        @php
                                            $materials = old('stage_materials', $stage->stageMaterials ?? []);
                                        @endphp
                                        @foreach($materials as $index => $material)
                                            <div class="row stage-material-row mb-2">
                                                <div class="col-md-5">
                                                    <select
                                                        name="stage_materials[{{ $index }}][raw_material_variation_id]"
                                                        class="form-control materialSelect select2" required>
                                                        <option value="">Хомашёни танланг</option>
                                                        @foreach($rawMaterialVariations as $variation)
                                                            <option value="{{ $variation->id }}"
                                                                {{ (isset($material['raw_material_variation_id']) && $material['raw_material_variation_id'] == $variation->id) ? 'selected' : '' }}>
                                                                {{ $variation->code }} — {{ $variation->rawMaterial->title }} → {{ $variation->title }}
                                                                ({{ \App\Helpers\CountHelper::format($variation->count, $variation->unit) }})
                                                                [{{ \App\Helpers\PriceHelper::format($variation->price, $variation->currency) }}]
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="text" step="0.01"
                                                           name="stage_materials[{{ $index }}][count]"
                                                           class="form-control filter-numeric-decimal" placeholder="Микдори"
                                                           value="{{ $material['count'] ?? '' }}" required>
                                                </div>
                                                <div class="col-md-3">
                                                    <select name="stage_materials[{{ $index }}][unit]" class="form-control">
                                                        @foreach (\App\Services\StatusService::getTypeCount() as $key => $label)
                                                            <option value="{{ $key }}"
                                                                {{ (isset($material['unit']) && $material['unit'] == $key) ? 'selected' : '' }}>
                                                                {{ $label }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-1">
                                                    <button type="button" class="btn btn-sm remove-material">
                                                        ❌
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <button type="button" class="btn btn-success btn-sm mt-2" id="add-stage-material">
                                        + Материал қўшиш
                                    </button>
                                </div>

                                <div class="mb-3">
                                    <label for="price" class="form-label">Нархи</label>
                                    <input type="text" step="0.01" id="price" name="price"
                                           class="form-control filter-numeric"
                                           value="{{ old('price', $stage->price ?? '') }}">
                                    @error('price')
                                    <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="status" class="form-label">Статус</label>
                                    <select name="status" id="status" class="form-control">
                                        @foreach (\App\Services\StatusService::getList() as $key => $label)
                                            <option
                                                value="{{ $key }}" {{ old('status', $stage->status ?? '') == $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        @if (Route::currentRouteName() == 'stage.create')
                            <button type="submit" class="btn btn-info">{{ 'Сақлаш' }}</button>
                        @elseif (Route::currentRouteName() == 'stage.edit')
                            <button type="submit" class="btn btn-info">{{ 'Янгилаш' }}</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    document.addEventListener("DOMContentLoaded", function () {

        let organizationSelect = document.getElementById("organization_id");
        let sectionSelect = document.getElementById("section_id");
        let preStageSelect = document.getElementById("pre_stage_id");

        let oldSectionId = "{{ old('section_id', $stage->section_id ?? '') }}";
        let oldPreStageId = "{{ old('pre_stage_id', $stage->pre_stage_id ?? '') }}";

        /* ===============================
            INIT
        =============================== */
        sectionSelect.disabled  = true;
        preStageSelect.disabled = true;

        /* ===============================
            LOAD SECTIONS
        =============================== */
        function loadSections(orgId, selectedId = null) {
            sectionSelect.innerHTML = '<option value="">Бўлимни танланг</option>';
            sectionSelect.disabled = true;

            preStageSelect.innerHTML = '<option value="">Олдинги маҳсулотни танланг</option>';
            preStageSelect.disabled = true;

            if (!orgId) return;

            fetch(`/admin/section/by-organization/${orgId}`)
                .then(r => r.json())
                .then(data => {
                    data.forEach(s => {
                        const opt = document.createElement('option');
                        opt.value = s.id;
                        opt.text  = s.title;
                        if (selectedId && selectedId == s.id) opt.selected = true;
                        sectionSelect.appendChild(opt);
                    });
                    sectionSelect.disabled = false;

                    if (selectedId) {
                        loadPreStages(selectedId, oldPreStageId);
                    }
                });
        }

        /* ===============================
            LOAD PRE STAGES
        =============================== */
        function loadPreStages(sectionId, selectedId = null) {
            preStageSelect.innerHTML = '<option value="">Олдинги маҳсулотни танланг</option>';
            preStageSelect.disabled = true;

            if (!sectionId) return;

            fetch(`/admin/stage/by-section/${sectionId}`)
                .then(r => r.json())
                .then(data => {
                    data.forEach(st => {
                        const opt = document.createElement('option');
                        opt.value = st.id;
                        opt.text  = st.title;
                        if (selectedId && selectedId == st.id) opt.selected = true;
                        preStageSelect.appendChild(opt);
                    });
                    preStageSelect.disabled = false;
                });
        }

        /* ===============================
            EVENTS
        =============================== */
        organizationSelect.addEventListener('change', function () {
            loadSections(this.value);
        });

        sectionSelect.addEventListener('change', function () {
            loadPreStages(this.value);
        });

        /* ===============================
            EDIT PAGE INIT
        =============================== */
        if (organizationSelect.value) {
            loadSections(organizationSelect.value, oldSectionId);
        }

        let wrapper = document.getElementById('stage-materials-wrapper');
        let addBtn = document.getElementById('add-stage-material');
        let index = wrapper.children.length;

        function getSelectedIds() {
            let ids = [];
            document.querySelectorAll('.materialSelect').forEach(select => {
                if (select.value) ids.push(select.value);
            });
            return ids;
        }

        function refreshOptions() {
            let selected = getSelectedIds();

            $('.materialSelect').each(function () {
                let $select = $(this);
                let currentVal = $select.val();

                // Select2 ni destroy qilamiz
                if ($select.data('select2')) $select.select2('destroy');

                // Optionlarni filterlaymiz
                $select.find('option').each(function () {
                    let $opt = $(this);
                    if ($opt.val() !== '' && selected.includes($opt.val()) && $opt.val() !== currentVal) {
                        $opt.remove(); // boshqa selectlarda tanlangan optionni o'chiramiz
                    } else {
                        if (!$opt.length) {
                            // Agar kerak bo‘lsa, optionni qayta qo‘shish (shu selectga tegishli bo‘lsa)
                        }
                    }
                });

                // Select2 ni qayta ishga tushiramiz
                $select.select2({
                    placeholder: "Танланг...",
                    allowClear: true,
                    minimumInputLength: 2,
                    language: {
                        inputTooShort: () => "Камида 2 та белги киритинг",
                        noResults: () => "Ҳеч қандай натижа топилмади"
                    },
                    width: '100%',
                });
            });
        }

        // Boshlang'ich select2 va filtrlarni ishga tushirish
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
        refreshOptions();

        // Qator qo‘shish
        addBtn.addEventListener('click', function () {
            let row = document.createElement('div');
            row.classList.add('row', 'stage-material-row', 'mb-2');
            row.innerHTML = `
            <div class="col-md-5">
                <select name="stage_materials[${index}][raw_material_variation_id]"
                    class="form-control materialSelect select2" required>
                    <option value="">Хомашёни танланг</option>
                    @foreach($rawMaterialVariations as $variation)
            <option value="{{ $variation->id }}">
                            {{ $variation->code }} — {{ $variation->rawMaterial->title }} → {{ $variation->title }}
                            ({{ \App\Helpers\CountHelper::format($variation->count, $variation->unit) }})
                            [{{ \App\Helpers\PriceHelper::format($variation->price, $variation->currency) }}]
                        </option>
                    @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <input type="text" step="0.01"
                   name="stage_materials[${index}][count]"
                       class="form-control filter-numeric-decimal"
                       placeholder="Микдори" required>
            </div>
            <div class="col-md-3">
                <select name="stage_materials[${index}][unit]" class="form-control">
                    @foreach (\App\Services\StatusService::getTypeCount() as $key => $label)
            <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
            </select>
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-sm remove-material">❌</button>
            </div>
            `;
            wrapper.appendChild(row);
            filterNumericDecimal();
            refreshOptions();
            index++;
        });

        // Qator o'chirish
        wrapper.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-material')) {
                e.target.closest('.stage-material-row').remove();
                refreshOptions();
            }
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
    });
</script>
