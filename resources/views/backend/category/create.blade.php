<x-backend.layouts.main title="{{ 'Қатегория яратиш' }}">
    <div class="category-create">
        <x-backend.category.form
            :method="'POST'"
            :category="$category"
            :categoryDropdown="$categoryDropdown"
            :action="route('category.store')" />
    </div>
</x-backend.layouts.main>
