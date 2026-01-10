<style>
    :root {
        --primary-blue: #005a9e;
        --hover-blue: #0078d4;
        --bg-dark: #201f1e;
        --light-blue-bg: #eff6fc;
        --border-color: #d2d0ce;
        --header-bg: #faf9f8;
        --text-main: #323130;
        --accent-red: #a4262c;
        --sticky-shadow: 4px 0 8px -4px rgba(0,0,0,0.2);
    }

    .excel-theme {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        overflow: hidden;
        background: white;
    }

    /* Tabs */
    .section-tabs { display: flex; gap: 2px; background: var(--bg-dark); padding: 8px 12px 0 12px; }
    .section-btn { padding: 10px 20px; color: #fff; text-decoration: none; font-size: 13px; font-weight: 600; border-radius: 6px 6px 0 0; transition: all 0.2s ease; }
    .section-btn:hover { background: rgba(255,255,255,0.1); color: var(--hover-blue); text-decoration: none; }
    .section-btn.active { background: rgba(255,255,255,0.05); border-bottom: 3px solid var(--hover-blue); color: var(--hover-blue); }

    .shift-tabs { background: white; padding: 12px 15px; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; gap: 10px; }
    .shift-link { padding: 6px 16px; border-radius: 20px; font-size: 12px; font-weight: 500; background: var(--light-blue-bg); color: var(--primary-blue); text-decoration: none; border: 1px solid transparent; }
    .shift-link:hover { background: var(--light-blue-bg); color: var(--hover-blue); }
    .shift-link.active { background: var(--primary-blue); color: white; text-decoration: none; }

    /* Table Core */
    .table-container { overflow: auto; max-height: 75vh; position: relative; }
    table { border-collapse: separate; border-spacing: 0; min-width: 100%; }
    th, td { border-right: 1px solid var(--border-color); border-bottom: 1px solid var(--border-color); white-space: nowrap; }

    /* Sticky Columns */
    .sticky-col { position: sticky; left: 0; background: white; border-right: 2px solid var(--border-color); z-index: 5; }
    .col-id { width: 45px; min-width: 45px; text-align: center; left: 0; z-index: 10; }
    .col-worker { width: 200px; min-width: 200px; left: 45px; z-index: 10; padding: 0 15px; box-shadow: var(--sticky-shadow); }

    /* Header Sticky */
    thead th {
        background: var(--header-bg); color: #605e5c; font-weight: 600; font-size: 12px;
        text-transform: uppercase; text-align: center; height: 35px; position: sticky; top: 0; z-index: 15;
    }
    thead th.sticky-col { z-index: 25; }

    /* Stage Header Styling */
    .stage-header { background: #f3f2f1; color: var(--primary-blue); height: 35px; }
    .sub-header { font-size: 10px; height: 30px; background: #faf9f8; }

    /* Inputs */
    .cell-input {
        width: 100%; min-width: 100px; height: 38px; border: none; outline: none;
        text-align: center; background: transparent; font-size: 14px; transition: 0.1s;
    }
    .cell-input:focus { background: white; box-shadow: inset 0 0 0 2px var(--primary-blue); }
    .defect-input-cell { background: #fff5f5; color: var(--accent-red); font-weight: 600; }

    /* Total Row */
    .total-row { position: sticky; bottom: 0; background: #faf9f8; z-index: 20; font-weight: bold; }
    .total-row td { border-top: 2px solid var(--primary-blue); height: 40px; }
    .total-row td.sticky-col { z-index: 21; background: #faf9f8; }

    .footer-actions { padding: 15px 25px; background: white; display: flex;  border-top: 1px solid var(--border-color); justify-content: flex-end; }
    .save-btn {
        background: var(--primary-blue); color: white; border: none; padding: 10px 25px; font-size: 14px;
        font-weight: 600; border-radius: 4px; cursor: pointer; transition: 0.2s; box-shadow: 0 1px 3px rgba(0,0,0,0.2);
    }
    .save-btn:hover { background: var(--hover-blue); box-shadow: 0 2px 4px rgba(0,0,0,0.2); }
</style>

<div class="container-fluid">
    <div class="excel-theme">
        <div class="section-tabs">
            @foreach($sections as $s)
                <a href="{{ url('admin/shift/' . ($s->shifts->first()->id ?? 0) . '/outputs/create') }}"
                   class="section-btn {{ $s->id == $shift->section_id ? 'active' : '' }}">
                    {{ $s->title }}
                </a>
            @endforeach
        </div>

        <div class="shift-tabs">
            <span style="font-size: 12px; font-weight: 700; color: #605e5c;">Смена:</span>
            @foreach($shifts as $s)
                <a href="{{ url('admin/shift/' . $s->id . '/outputs/create') }}"
                   class="shift-link {{ $s->id == $shift->id ? 'active' : '' }}">
                    {{ $s->title }}
                </a>
            @endforeach
        </div>

        <form action="{{ $action ?? '#' }}" method="POST">
            @csrf
            @if (($method ?? '') === 'PUT') @method('PUT') @endif

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th class="sticky-col col-id">#</th>
                            <th class="sticky-col col-worker">Ходимлар</th>
                            @foreach($stages as $stage)
                                <th class="stage-header">{{ $stage->title }}</th>
                            @endforeach
                            <th style="background-color: #fff0f0; color: var(--accent-red);">Умумий брак (кг)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($workers as $index => $worker)
                        <tr>
                            <td class="sticky-col col-id text-muted small">{{ $index + 1 }}</td>
                            <td class="sticky-col col-worker"><strong>{{ $worker->username }}</strong></td>

                            @foreach($stages as $stage)
                            <td>
                                <input type="text"
                                       name="input[{{ $worker->id }}][{{ $stage->id }}][stage_count]"
                                       class="cell-input qty-input filter-numeric"
                                       data-stage-id="{{ $stage->id }}"
                                       placeholder="-">
                            </td>
                            @endforeach

                            <td style="background-color: #fffafa;">
                                <input type="text"
                                       step="0.001"
                                       name="worker_defects[{{ $worker->id }}]"
                                       class="cell-input defect-input defect-input-cell filter-numeric-decimal"
                                       placeholder="-">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="total-row">
                            <td class="sticky-col col-id"></td>
                            <td class="sticky-col col-worker" style="text-align: right; padding-right: 15px;">ЖАМИ:</td>
                            @foreach($stages as $stage)
                                <td id="total-stage-{{ $stage->id }}" class="text-center" style="color: var(--primary-blue);">0</td>
                            @endforeach
                            <td id="total-all-defects" class="text-center" style="color: var(--accent-red);">0.000</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="footer-actions">
                <button type="submit" class="save-btn">
                    <i class="fas fa-save mr-2"></i> САҚЛАШ
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    function getNumericValue(input) {
        if ($(input).inputmask) {
            return Number($(input).inputmask('unmaskedvalue')) || 0;
        }
        return 0;
    }

    function calculateTotals() {

        @foreach($stages as $stage)
            let stageSum{{ $stage->id }} = 0;

            document
                .querySelectorAll('input[name*="[{{ $stage->id }}][stage_count]"]')
                .forEach(input => {
                    stageSum{{ $stage->id }} += getNumericValue(input);
                });

            document.getElementById('total-stage-{{ $stage->id }}')
                .innerText = stageSum{{ $stage->id }}.toLocaleString('ru-RU');
        @endforeach

        let totalDefects = 0;
        document.querySelectorAll('.defect-input').forEach(input => {
            totalDefects += getNumericValue(input);
        });

        document.getElementById('total-all-defects')
            .innerText = totalDefects.toFixed(3);
    }

    // MUHIM: input emas!
    $(document).on('keyup change', '.qty-input, .defect-input', function () {
        calculateTotals();
    });

    calculateTotals();
});
</script>

