<x-backend.layouts.main title="{{ 'Кирим ва харажатни кўриш: ' . ucfirst($expenseAndIncome->title) }}">

    <style>
        th {
            font-weight: bold !important;
        }
    </style>

    <div class="card">
        <div class="card-header">
{{--            <h1>{{ $expenseAndIncome->title[app()->getLocale()] ?? $expenseAndIncome->title }}</h1>--}}
        </div>
        <div class="card-body">

            <x-backend.action route="expense-and-income" :id="$expenseAndIncome->id" :back="true" :edit="true" editClass="btn btn-primary sm" editLabel="Янгилаш"/>

            <table class="table table-bordered mt-3">
                <tbody>
                <tr>
                    <th>Id</th> <td>{{ $expenseAndIncome->id }}</td>
                </tr>
                <tr>
                    <th>Номи</th> <td>{{ $expenseAndIncome->title }}</td>
                </tr>
                <tr>
                    <th>Тавсифи</th> <td>{!!  $expenseAndIncome->description !!}</td>
                </tr>
                <tr>
                    <th>Микдори</th><td>{{ number_format($expenseAndIncome->amount, 0, '', ' ') }} сўм</td>
                </tr>
                <tr>
                    <th>Тури</th><td>{{ \App\Models\ExpenseAndIncome::getTypeList()[$expenseAndIncome->type] }}</td>
                </tr>
                <tr>
                    <th>Яратилди</th><td>{{ $expenseAndIncome->created_at }}</td>
                </tr>
                <tr>
                    <th>Янгиланди</th><td>{{ $expenseAndIncome->updated_at }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

</x-backend.layouts.main>
