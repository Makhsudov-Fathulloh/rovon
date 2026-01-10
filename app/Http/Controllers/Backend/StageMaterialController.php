<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Stage;
use App\Models\StageMaterial;
use App\Models\RawMaterialVariation;
use Illuminate\Http\Request;

class StageMaterialController extends Controller
{
    public function index(Request $request)
    {
        $query = StageMaterial::with(['stage', 'rawMaterialVariation']);

        // filtrlar
        if ($request->filled('stage_id')) {
            $query->where('stage_id', $request->stage_id);
        }

        if ($request->filled('raw_material_variation_id')) {
            $query->where('raw_material_variation_id', $request->raw_material_variation_id);
        }

        $stageMaterials = $query->paginate(20)->withQueryString();

        $stages = Stage::pluck('title', 'id');
        $variations = RawMaterialVariation::pluck('title', 'id');

        return view('backend.stage-material.index', compact(
            'stageMaterials',
            'stages',
            'variations'
        ));
    }

    public function create()
    {
        $stages = Stage::pluck('title', 'id');
        $variations = RawMaterialVariation::pluck('title', 'id');

        return view('backend.stage-material.create', compact('stages', 'variations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'stage_id' => 'required|exists:stage,id',
            'raw_material_variation_id' => 'required|exists:raw_material_variation,id',
            'count' => 'required|numeric|min:0',
            'unit' => 'nullable|string|max:50',
        ]);

        StageMaterial::create($request->all());

        return redirect()->route('stage-material.index')->with('success', 'Stage material yaratildi!');
    }

    public function edit(StageMaterial $stageMaterial)
    {
        $stages = Stage::pluck('title', 'id');
        $variations = RawMaterialVariation::pluck('title', 'id');

        return view('backend.stage-material.update', compact(
            'stageMaterial',
            'stages',
            'variations'
        ));
    }

    public function update(Request $request, StageMaterial $stageMaterial)
    {
        $request->validate([
            'stage_id' => 'required|exists:stage,id',
            'raw_material_variation_id' => 'required|exists:raw_material_variation,id',
            'count' => 'required|numeric|min:0',
            'unit' => 'nullable|string|max:50',
        ]);

        $stageMaterial->update($request->all());

        return redirect()->route('stage-material.index')->with('success', 'Stage material yangilandi!');
    }

    public function destroy(StageMaterial $stageMaterial)
    {
        $stageMaterial->delete();

        return response()->json([
            'message' => 'Stage material o‘chirildi!',
            'type' => 'delete',
            'redirect' => route('stage-material.index')
        ]);
    }
}

