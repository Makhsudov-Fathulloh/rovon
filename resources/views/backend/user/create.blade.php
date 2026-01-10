@php
    @abort_unless(in_array(\Illuminate\Support\Facades\Auth::user()->role->title, ['Admin', 'Manager', 'Developer']), 403)
@endphp

<x-backend.layouts.main title="{{ 'Фойдаланувчи яратиш' }}">
    <div class="user-create">
        <x-backend.user.form
            :method="'POST'"
            :user="$user"
            :rolesRoot="$rolesRoot"
            :rolesAdmin="$rolesAdmin"
            :roles="$roles"
            :clientRoleId="$clientRoleId"
            :clientRole="$clientRole"
            :action="route('user.store')"
        />
    </div>
</x-backend.layouts.main>
