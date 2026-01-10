<x-backend.layouts.main title="{{ 'Қатегория янгилаш: ' . ucfirst($category->title) }}">
    <div class="category-update">
        <x-backend.category.form
            :category="$category"
            :categoryDropdown="$categoryDropdown"
            :method="'PUT'"
            :action="route('category.update', $category->id)" />
    </div>
</x-backend.layouts.main>
