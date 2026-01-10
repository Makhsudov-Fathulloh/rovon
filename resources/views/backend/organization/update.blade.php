<x-backend.layouts.main title="{{ 'Филиални янгилаш: ' . ucfirst($organization->title) }}">
    <div class="organization-update">
        <x-backend.organization.form
            :users="$users"
            :organization="$organization"
            :method="'PUT'"
            :action="route('organization.update', $organization->id)"
        />
    </div>
</x-backend.layouts.main>
