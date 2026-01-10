<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class ModeratorScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $user = Auth::user();

        // Faqat Moderator uchun
        if (!$user || $user->role->title !== 'Moderator') {
            return;
        }

        $table = $model->getTable();

        // 1. organization_id mavjud
        if (Schema::hasColumn($table, 'organization_id')) {
            $builder->whereHas('organization', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
            return;
        }

        // 2. warehouse_id mavjud
        if (Schema::hasColumn($table, 'warehouse_id')) {
            $builder->whereHas('warehouse.organization', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
            return;
        }

        // 3. section_id mavjud
        if (Schema::hasColumn($table, 'section_id')) {
            $builder->whereHas('section.organization', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
            return;
        }

        // 4. shift_id mavjud
        if (Schema::hasColumn($table, 'shift_id')) {
            $builder->whereHas('shift.section.organization', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
            return;
        }

        // 5. shift_output_id mavjud (ShiftOutputWorker uchun)
        if (Schema::hasColumn($table, 'shift_output_id')) {
            $builder->whereHas('shiftOutput.shift.section.organization', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
            return;
        }

        // 6. raw_material_id mavjud
        if (Schema::hasColumn($table, 'raw_material_id')) {
            $builder->whereHas('rawMaterial.warehouse.organization', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
            return;
        }

        // 7. product_id mavjud
        if (Schema::hasColumn($table, 'product_id')) {
            $builder->whereHas('product.warehouse.organization', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
            return;
        }

        // 8. stage_id mavjud
        if (Schema::hasColumn($table, 'stage_id')) {
            $builder->whereHas('stage.section.organization', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
            return;
        }

        // 9. Fallback: user_id mavjud bo‘lsa
        if (in_array('user_id', Schema::getColumnListing($table))) {
            $builder->where('user_id', $user->id);
        }
    }
}
