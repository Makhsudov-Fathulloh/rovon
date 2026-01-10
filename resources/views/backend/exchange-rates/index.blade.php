<x-backend.layouts.main title="Валюта курслари">

    <div class="card shadow">
        <div class="card-body table-responsive">
            <h4>💱 Валюта курслари</h4>
            <table class="table table-bordered table-striped align-middle text-center">
                <thead class="table-primary">
                <tr>
                    <th style="width: 75px;">#</th>
                    <th style="width: 150px;">Валюта</th>
                    <th style="width: 150px;">Курс (сўм)</th>
                    <th style="width: 250px;">Янгилаш</th>
                </tr>
                </thead>
                <tbody>
                @forelse($rates as $key => $rate)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td class="fw-bold">{{ $rate->currency }}</td>
                        <td>{{ number_format($rate->rate, 2, '.', ' ') }}</td>
                        <td style="white-space: nowrap;">
                            <form action="{{ route('exchange-rates.update') }}" method="POST" class="d-flex justify-content-center align-items-center gap-2">
                                @csrf
                                <input type="hidden" name="currency" value="{{ $rate->currency }}">

                                <input type="number" name="rate" step="0.01" value="{{ $rate->rate }}"
                                       class="form-control form-control-sm text-end" style="width: 120px;">

                                <button type="submit" class="btn btn-success btn-sm px-3">
                                    💾 Сақлаш
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-muted py-3">Маълумот топилмади</td>
                    </tr>
                @endforelse
                </tbody>
            </table>

        </div>
    </div>

</x-backend.layouts.main>
