<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        return view('backend.role.index', compact('roles'));
    }


    public function create()
    {
        $role = new Role();

        return view('backend.role.create', compact('role'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Role::create($request->only(['title', 'description']));

        return redirect()->route('role.index')->with('success', 'Даража яратилди!');
    }


    public function edit(Role $role)
    {
        return view('backend.role.update', compact('role'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $role->update($request->only(['title', 'description']));

        return redirect()->route('role.index')->with('success', 'Даража янгиланди!');
    }


    public function destroy(Role $role)
    {
        $role->delete();

        return response()->json([
            'message' => 'Даража ўчирилди!',
            'type' => 'delete',
            'redirect' => route('role.index')
        ]);
    }
}
