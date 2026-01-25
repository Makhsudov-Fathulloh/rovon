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
                                    <select name="organization_id" id="organization_id" class="form-control"
                                            required>
                                        <option value="">Филиални танланг</option>
                                        @foreach($organizations as $id => $title)
                                            <option value="{{ $id }}"
                                                {{ old('organization_id', $shift->section->organization_id ?? '') == $id ? 'selected' : '' }}>
                                                {{ $title }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('organization_id')
                                    <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label for="section_id"><strong>Бўлим</strong></label>
                                    <select name="section_id" id="section_id" class="form-control" required disabled>
                                        <option value="">Бўлимни танланг</option>
                                    </select>
                                    @error('section_id')
                                    <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label for="user_id"><strong>Ходимлар</strong></label>
                                    @php
                                        $selectedUsers = old('user_id', ($shift->exists ? $shift->users->pluck('id')->toArray() : []));
                                    @endphp
                                    <select name="user_id[]" id="user_id" class="form-control worker-select2" required multiple>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}"
                                                {{ in_array($user->id, $selectedUsers) ? 'selected' : '' }}>
                                                {{ $user->username }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('user_id')
                                    <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="title" class="form-label">Номи</label>
                                    <input type="text" id="title" name="title" class="form-control"
                                           value="{{ old('title', $shift->title ?? '') }}">
                                    @error('title')
                                    <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label for="status" class="form-label">Статус</label>
                                    <select name="status" id="status" class="form-control">
                                        @foreach (\App\Services\StatusService::getList() as $key => $label)
                                            <option
                                                value="{{ $key }}" {{ old('status', $shift->status ?? '') == $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <div class="mb-3">
                                        <label for="started_at" class="form-label">Бошланиш вақти</label>
                                        <input type="time" name="started_at" id="started_at" class="form-control"
                                               value="{{ old('started_at', isset($shift->started_at) ? \Carbon\Carbon::parse($shift->started_at)->format('H:i') : '') }}">
                                    </div>
                                    <div class="mb-3">
                                        <label for="ended_at" class="form-label">Тугаш вақти</label>
                                        <input type="time" name="ended_at" id="ended_at" class="form-control"
                                               value="{{ old('ended_at', isset($shift->ended_at) ? \Carbon\Carbon::parse($shift->ended_at)->format('H:i') : '') }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if (Route::currentRouteName() == 'shift.create')
                            <button type="submit" class="btn btn-success">{{ 'Сақлаш' }}</button>
                        @elseif (Route::currentRouteName() == 'shift.edit')
                            <button type="submit" class="btn btn-success">{{ 'Янгилаш' }}</button>
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
        let oldSectionId = "{{ old('section_id', $shift->section_id ?? '') }}";

        function loadSections(organizationId, selectedId = null) {
            sectionSelect.innerHTML = '<option value="">Бўлимни танланг</option>';
            sectionSelect.disabled = true;

            if (organizationId) {
                let url = "{{ url('/admin/section/by-organization') }}/" + organizationId;

                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(function (section) {
                            let option = document.createElement("option");
                            option.value = section.id;
                            option.text = section.title;
                            if (selectedId && selectedId == section.id) {
                                option.selected = true; // eski tanlovni tiklash
                            }
                            sectionSelect.appendChild(option);
                        });
                        sectionSelect.disabled = false;
                    })
                    .catch(error => {
                        // console.error(error);
                        sectionSelect.innerHTML = '<option value="">Хатолик юз берди</option>';
                    });
            }
        }

        // Bino tanlanganda ishlasin
        organizationSelect.addEventListener("change", function () {
            loadSections(this.value);
        });

        // Sahifa yuklanganda agar eski qiymat bo‘lsa qayta yuklash
        if (organizationSelect.value) {
            loadSections(organizationSelect.value, oldSectionId);
        }
    });

</script>

<style>
    /* Tanlangan elementlar (choice) */
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #0d6efd !important; /* Asosiy rang */
        color: #fff !important;
        padding-right: 7px !important;
        position: relative;
    }

    /* Tanlangan elementdagi x tugmasi */
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: red !important;
        margin-right: 5px;
        float: left;
        font-weight: bold;
    }

    /* Hover va active/focus holatlari */
    .select2-container--default .select2-selection--multiple .select2-selection__choice:hover,
    .select2-container--default .select2-selection--multiple .select2-selection__choice:active,
    .select2-container--default .select2-selection--multiple .select2-selection__choice:focus {
        background-color: #0b5ed7 !important;
        color: #fff !important;
    }

    .select2-container--classic .select2-selection--multiple .select2-selection__choice, .select2-container--default .select2-selection--multiple .select2-selection__choice, .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        background-color: transparent;
    !important;
    }

    /* Dropdown optionlari bir xil rangda, hover uchun quyuqroq */
    .worker-select2 .select2-results__option {
        background-color: #0d6efd;
        color: #fff;
    }

    .worker-select2 .select2-results__option--highlighted {
        background-color: #0b5ed7 !important;
        color: #fff !important;
    }
</style>

<script>
    $('.worker-select2').select2({
        placeholder: "Барчаси",
        allowClear: true,
        minimumInputLength: 2,
        language: {
            inputTooShort: function () {
                return "Камида 2 та белги киритинг";
            },
            noResults: function () {
                return "Натижа топилмади";
            }
        },
        templateResult: function (user) {
            return user.text;
        },
        templateSelection: function (user) {
            return user.text;
        },
        matcher: function (params, data) {
            if (!data.id) return data;

            var selected = $('#user_id').val() || [];
            if (selected.includes(data.id)) return null; // tanlangan optionni dropdowndan yashirish

            if ($.trim(params.term) === '') return data;

            if (data.text.toLowerCase().indexOf(params.term.toLowerCase()) > -1) return data;

            return null;
        }
    });
</script>

{{--<style>--}}
{{--    /* Tanlangan elementlar (choice) */--}}
{{--    .select2-container--default .select2-selection--multiple .select2-selection__choice {--}}
{{--        background-color: #0d6efd !important; /* Asosiy rang */--}}
{{--        color: #fff !important;                /* Text rangi */--}}
{{--        border: none !important;--}}
{{--        border-radius: 3px !important;--}}
{{--        padding-right: 7px !important;--}}
{{--        position: relative;--}}
{{--    }--}}

{{--    /* Tanlangan elementdagi x tugmasi */--}}
{{--    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {--}}
{{--        color: red !important;--}}
{{--        margin-right: 5px;--}}
{{--        float: left;--}}
{{--        font-weight: bold;--}}
{{--    }--}}

{{--    /* Hover va active/focus holatlari */--}}
{{--    .select2-container--default .select2-selection--multiple .select2-selection__choice:hover,--}}
{{--    .select2-container--default .select2-selection--multiple .select2-selection__choice:active,--}}
{{--    .select2-container--default .select2-selection--multiple .select2-selection__choice:focus {--}}
{{--        background-color: #0b5ed7 !important;--}}
{{--        color: #fff !important;--}}
{{--    }--}}

{{--    .select2-container--classic .select2-selection--multiple .select2-selection__choice, .select2-container--default .select2-selection--multiple .select2-selection__choice, .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {--}}
{{--    background-color: transparent; !important;--}}
{{--    }--}}

{{--    /* Dropdown optionlari bir xil rangda, hover uchun quyuqroq */--}}
{{--    .worker-select2 .select2-results__option {--}}
{{--        background-color: #0d6efd;--}}
{{--        color: #fff;--}}
{{--    }--}}
{{--    .worker-select2 .select2-results__option--highlighted {--}}
{{--        background-color: #0b5ed7 !important;--}}
{{--        color: #fff !important;--}}
{{--    }--}}
{{--</style>--}}

{{--<script>--}}
{{--    $('.worker-select2').select2({--}}
{{--        placeholder: "Барчаси",--}}
{{--        allowClear: true,--}}
{{--        minimumInputLength: 2,--}}
{{--        language: {--}}
{{--            inputTooShort: function () {--}}
{{--                return "Камида 2 та белги киритинг";--}}
{{--            },--}}
{{--            noResults: function () {--}}
{{--                return "Натижа топилмади";--}}
{{--            }--}}
{{--        },--}}
{{--        templateResult: function(user) { return user.text; },--}}
{{--        templateSelection: function(user) { return user.text; },--}}

{{--    // $('.worker-select2').on('select2:opening', function(e) {--}}
{{--    //     var select = $(this);--}}
{{--    //     var selected = select.val() || [];--}}
{{--    //--}}
{{--    //     select.find('option').each(function() {--}}
{{--    //         var val = $(this).val();--}}
{{--    //         if (selected.includes(val)) {--}}
{{--    //             $(this).attr('disabled', 'disabled'); // tanlangan optionni disable qilish--}}
{{--    //         } else {--}}
{{--    //             $(this).removeAttr('disabled');--}}
{{--    //         }--}}
{{--    //     });--}}
{{--    // });--}}

{{--        matcher: function(params, data) {--}}
{{--            // Agar search params bo'lmasa, textni qaytaramiz--}}
{{--            if ($.trim(params.term) === '') {--}}
{{--                // Tanlangan elementlarni yashirish--}}
{{--                var selected = $('#user_id').val() || [];--}}
{{--                if (selected.includes(data.id)) {--}}
{{--                    return null; // null qaytarilsa, option ko‘rinmaydi--}}
{{--                }--}}
{{--                return data;--}}
{{--            }--}}

{{--            // Search bilan ishlash--}}
{{--            if (typeof data.text === 'undefined') return null;--}}
{{--            if (data.text.toLowerCase().indexOf(params.term.toLowerCase()) > -1) {--}}
{{--                // Tanlangan elementlarni yashirish--}}
{{--                var selected = $('#user_id').val() || [];--}}
{{--                if (selected.includes(data.id)) {--}}
{{--                    return null;--}}
{{--                }--}}
{{--                return data;--}}
{{--            }--}}

{{--            return null;--}}
{{--        }--}}
{{--    });--}}
{{--</script>--}}

{{--<style>--}}
{{--    /* Tanlangan elementlar bir xil rangda */--}}
{{--    .select2-container--default .select2-selection--multiple .select2-selection__choice {--}}
{{--        background-color: #5897fb !important;--}}
{{--        color: #fff !important;--}}
{{--        padding-right: 7px !important;--}}
{{--    }--}}

{{--    /* Dropdown optionlari bir xil rangda, hover uchun rang biroz o'zgaradi */--}}
{{--    .worker-select2 .select2-results__option {--}}
{{--        background-color: #0d6efd;--}}
{{--        color: #fff;--}}
{{--    }--}}

{{--    .worker-select2 .select2-results__option--highlighted {--}}
{{--        background-color: #0b5ed7 !important;--}}
{{--        color: #fff !important;--}}
{{--    }--}}
{{--</style>--}}
{{--<script>--}}
{{--    $('.worker-select2').select2({--}}
{{--        placeholder: "Барчаси",--}}
{{--        allowClear: true,--}}
{{--        minimumInputLength: 2,--}}
{{--        language: {--}}
{{--            inputTooShort: function () {--}}
{{--                return "Камида 2 та белги киритинг";--}}
{{--            },--}}
{{--            noResults: function () {--}}
{{--                return "Натижа топилмади";--}}
{{--            }--}}
{{--        },--}}
{{--        // templateResult va templateSelection oddiy text bilan ishlaydi--}}
{{--        templateResult: function(user) {--}}
{{--            return user.text;--}}
{{--        },--}}
{{--        templateSelection: function(user) {--}}
{{--            return user.text;--}}
{{--        }--}}
{{--    });--}}
{{--</script>--}}

<script>
    flatpickr("#started_at", {
        enableTime: true,
        noCalendar: true,   // kalendarsiz faqat vaqt
        dateFormat: "H:i",  // format faqat soat:daqiqalar
        time_24hr: true,
        minTime: "00:00", // 00:00 dan kichik bo‘lmasin
        maxTime: "23:59", // 24 soatdan oshmasin
    });

    flatpickr("#ended_at", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        minTime: "00:00", // 00:00 dan kichik bo‘lmasin
        maxTime: "23:59", // 24 soatdan oshmasin
        time_24hr: true,
    });


    {{--    flatpickr.localize({--}}
    {{--        weekdays: {--}}
    {{--            shorthand: ['Як', 'Ду', 'Се', 'Чо', 'Па', 'Жу', 'Ша'],--}}
    {{--            longhand: ['Якшанба', 'Душанба', 'Сешанба', 'Чоршанба', 'Пайшанба', 'Жума', 'Шанба'],--}}
    {{--        },--}}
    {{--        months: {--}}
    {{--            shorthand: ['Ян', 'Фев', 'Мар', 'Апр', 'Май', 'Июн', 'Июл', 'Авг', 'Сен', 'Окт', 'Ной', 'Дек'],--}}
    {{--            longhand: ['Январ', 'Феврал', 'Март', 'Апрел', 'Май', 'Июн', 'Июл', 'Август', 'Сентабр', 'Октябр', 'Ноябр', 'Декабр'],--}}
    {{--        },--}}
    {{--        firstDayOfWeek: 1,--}}
    {{--        ordinal: function() {--}}
    {{--            return '';--}}
    {{--        }--}}
    {{--    });--}}

    {{--    flatpickr("#started_at", {--}}
    {{--        enableTime: true,--}}
    {{--        dateFormat: "Y-m-d H:i",--}}
    {{--        time_24hr: true,--}}
    {{--        locale: flatpickr.l10ns.default // uzbekcha qilib berdik--}}
    {{--    });--}}

    {{--    flatpickr("#ended_at", {--}}
    {{--        enableTime: true,--}}
    {{--        dateFormat: "Y-m-d H:i",--}}
    {{--        time_24hr: true,--}}
    {{--        locale: flatpickr.l10ns.default--}}
    {{--    });--}}
</script>
