<?php

namespace App\Http\Controllers\Backend;

use App\Models\Organization;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    public function index()
    {
        $organizations = Organization::with('user:id,username')->orderBy('id')->paginate(20);
        return view('backend.organization.index', compact('organizations'));
    }


    public function organizationSections(Organization $organization)
    {
        $sections = $organization->section()->pluck('title', 'id');
        return response()->json($sections);
    }

    public function show(Organization $organization)
    {
        return view('backend.organization.show', compact('organization'));
    }


    public function create()
    {
        $organization = new Organization();
        $users = User::where('role_id', \App\Models\Role::where('title', 'Moderator')->value('id'))->get();

        return view('backend.organization.create', compact('users', 'organization'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:user,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ], [
            'user_id.required' => 'Филиалга жавобгарни ходимни танланг.',
            'title.required' => 'Филиал номи мажбурий.',
            'title.string'   => 'Филиал номи матн бўлиши керак.',
        ]);

        Organization::create($request->all());

        return redirect()->route('organization.index')->with('success', 'Филиал яратилди!');
    }


    public function edit(Organization $organization)
    {
        $users = User::where('role_id', \App\Models\Role::where('title', 'Moderator')->value('id'))->get();

        return view('backend.organization.update', compact('users', 'organization'));
    }

    public function update(Request $request, Organization $organization)
    {
        $request->validate([
            'user_id' => 'required|exists:user,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ], [
            'user_id.required' => 'Филиалга жавобгарни ходимни танланг.',
            'title.required' => 'Филиал номи мажбурий.',
            'title.string'   => 'Филиал номи матн бўлиши керак.',
        ]);

        $organization->update($request->all());

        return redirect()->route('organization.index')->with('success', 'Филиал янгиланди!');
    }


    public function destroy(Organization $organization)
    {
        $organization->delete();

        return response()->json([
            'message' => 'Филиал ўчирилди!',
            'type' => 'delete',
            'redirect' => route('organization.index')
        ]);
    }
}
