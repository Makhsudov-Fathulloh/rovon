@php
    @abort_unless(in_array(\Illuminate\Support\Facades\Auth::user()->role->title, ['Admin', 'Manager', 'Developer']), 403)
@endphp

<x-backend.layouts.main title="{{ 'Фойдаланувчи янгилаш: ' . ucfirst($user->username) }}">
    <div class="user-update">
        <x-backend.user.form
            :method="'PUT'"
            :user="$user"
            :rolesRoot="$rolesRoot"
            :rolesAdmin="$rolesAdmin"
            :roles="$roles"
            :clientRoleId="$clientRoleId"
            :clientRole="$clientRole"
            :debtUzs="$debtUzs"
            :debtUsd="$debtUsd"
            :action="route('user.update', $user->id)"
        />
    </div>
</x-backend.layouts.main>
