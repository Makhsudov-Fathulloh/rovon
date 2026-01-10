<form action="{{ $action }}" method="POST">
    @csrf
    @if ($method === 'PUT')
        @method('PUT')
    @endif

    <div class="container-fluid mt-4">
        <div class="card shadow">
            <div class="card-body">
                {{-- Xatoliklar --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div id="inputs"></div>

                <hr>
                <button type="submit" class="btn btn-success">
                    {{ $method === 'PUT' ? 'Янгилаш' : 'Сақлаш' }}
                </button>
            </div>
        </div>
    </div>
</form>

<script>
    const stages = @json($stages);
    const workers = @json($workers);
    const oldInputs = @json(old('input', $oldInputs ?? []));

    function renderInputs() {
        let html = '';

        Object.keys(stages).forEach(stageId => {
            html += `<h5 class="mt-3">${stages[stageId]}</h5>`;

            Object.keys(workers).forEach(workerId => {
                const stageCount = oldInputs?.[workerId]?.[stageId]?.stage_count ?? '';
                const defectAmount = oldInputs?.[workerId]?.[stageId]?.defect_amount ?? '';

                html += `
                    <div class="row mb-2 border p-2 rounded">
                        <div class="col-md-3 text-center">
                            <strong>${workers[workerId]}</strong>
                        </div>
                        <div class="col-md-3">
                            <strong>${stages[stageId]}</strong>
                        </div>
                        <div class="col-md-3">
                            <input type="text"
                                name="input[${workerId}][${stageId}][stage_count]"
                                class="form-control stage-count numeric"
                                data-stage-id="${stageId}"
                                value="${stageCount}"
                                placeholder="Махсулот сони"
                                min="0">
                        </div>
                        <div class="col-md-3">
                            <input type="number"
                                name="input[${workerId}][${stageId}][defect_amount]"
                                class="form-control defect-amount"
                                data-stage-id="${stageId}"
                                value="${defectAmount}"
                                placeholder="Брак хажми"
                                min="0"
                                step="0.001">
                        </div>
                    </div>
                `;
            });

            // Har bir stage uchun jami blok
            html += `
                <div class="card mb-2 shadow-sm">
                    <div class="card-body d-flex justify-content-end align-items-center">
                        <div class="text-end">
                            <strong>
                                <span class="text-dark">${stages[stageId]} жами:</span>
                                <span class="text-primary stage-total numeric" data-stage-id="${stageId}">0</span> та
                                <span class="text-danger defect-total" data-stage-id="${stageId}">0.000</span> кг
                            </strong>
                        </div>
                    </div>
                </div>
            `;
        });

        document.getElementById('inputs').innerHTML = html;

        applyInputMask();
        attachListeners();
        calculateTotals();
    }

    function applyInputMask() {
        $(".numeric").inputmask({
            alias: "integer",
            groupSeparator: " ",
            autoGroup: true,
            rightAlign: false,
            allowMinus: false,
            showMaskOnHover: false,
            showMaskOnFocus: false,
            placeholder: '0'
        });
    }

    function calculateTotals() {
        const stageTotals = {};
        const defectTotals = {};

        document.querySelectorAll('.stage-count').forEach(input => {
            const stageId = input.dataset.stageId;
            const val = input.inputmask
                ? parseInt($(input).inputmask('unmaskedvalue')) || 0
                : parseInt(input.value) || 0;

            stageTotals[stageId] = (stageTotals[stageId] || 0) + val;
        });

        document.querySelectorAll('.defect-amount').forEach(input => {
            const stageId = input.dataset.stageId;
            const val = parseFloat(input.value) || 0;
            defectTotals[stageId] = (defectTotals[stageId] || 0) + val;
        });

        Object.keys(stages).forEach(stageId => {
            const stageSpan = document.querySelector(`.stage-total[data-stage-id="${stageId}"]`);
            const defectSpan = document.querySelector(`.defect-total[data-stage-id="${stageId}"]`);

            if (stageSpan) stageSpan.innerText = (stageTotals[stageId] || 0).toLocaleString('ru-RU');
            if (defectSpan) defectSpan.innerText = (defectTotals[stageId] || 0).toFixed(3);
        });
    }

    function attachListeners() {
        // 🔹 Oddiy input event yetarli emas — inputmask bilan ishlaganda `keyup` kerak
        document.querySelectorAll('.stage-count, .defect-amount').forEach(input => {
            input.addEventListener('input', calculateTotals);
            input.addEventListener('keyup', calculateTotals); // <-- qo‘shildi
        });

        // 🔹 Inputmask qiymati o‘zgarganda ham total yangilansin
        $(".stage-count").on('inputmask:complete', calculateTotals);
    }

    window.addEventListener('DOMContentLoaded', renderInputs);
</script>
