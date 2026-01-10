<x-backend.layouts.main title="{{ 'Филиал яратиш' }}">
    <div class="organization-create">
        <x-backend.organization.form
            :method="'POST'"
            :users="$users"
            :organization="$organization"
            :action="route('organization.store')"
        />
    </div>
</x-backend.layouts.main>
