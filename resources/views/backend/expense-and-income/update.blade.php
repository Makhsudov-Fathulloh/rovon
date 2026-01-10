<x-backend.layouts.main title="{{ 'Кирим ва харажат янгилаш: ' . ucfirst($expenseAndIncome->title) }}">
    <div class="expense-and-income-update">
        <x-backend.expense-and-income.form
            :expenseAndIncome="$expenseAndIncome"
            :method="'PUT'"
            :action="route('expense-and-income.update', $expenseAndIncome->id)"
        />
    </div>
</x-backend.layouts.main>
