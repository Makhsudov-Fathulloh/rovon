<x-backend.layouts.main title="{{ 'Ролни янгилаш: ' . ucfirst($role->title) }}">
    <div class="role-update">
        <x-backend.role.form
            :role="$role"
            :method="'PUT'"
            :action="route('role.update', $role->id)" />
    </div>
</x-backend.layouts.main>
