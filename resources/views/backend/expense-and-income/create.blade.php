<x-backend.layouts.main title="{{ 'Кирим ва харажат яратиш' }}">
    <div class="expense-and-income-create">
        <x-backend.expense-and-income.form
            :method="'POST'"
            :expenseAndIncome="$expenseAndIncome"
            :action="route('expense-and-income.store')"
        />
    </div>
</x-backend.layouts.main>
