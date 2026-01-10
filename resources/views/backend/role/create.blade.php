<x-backend.layouts.main title="{{ 'Роль яратиш' }}">
    <div class="role-create">
        <x-backend.role.form
            :method="'POST'"
            :role="$role"
            :action="route('role.store')" />
    </div>
</x-backend.layouts.main>
