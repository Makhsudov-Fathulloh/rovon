<?php

namespace App\Http\Controllers\Backend;

use App\Models\User;
use App\Models\Organization;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrganizationController extends Controller
{
    public function index()
    {
        $organizations = Organization::with('users:id,username')->orderBy('id')->paginate(20);
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
        $users = User::whereHas('role', fn($q) => $q->where('title', 'Moderator'))->get();
        $organization = new Organization();

        return view('backend.organization.create', compact('users', 'organization'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'users' => 'required|array',
            'users.*' => 'exists:user,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ], [
            'users.required' => 'Филиалга жавобгарни ходимни танланг.',
            'title.required' => 'Филиал номи мажбурий.',
            'title.string'   => 'Филиал номи матн бўлиши керак.',
        ]);

        $organization = Organization::create($request->only('title', 'description'));
        $organization->users()->sync($request->users);

        return redirect()->route('organization.index')->with('success', 'Филиал яратилди!');
    }


    public function edit(Organization $organization)
    {
        $users = User::whereHas('role', fn($q) => $q->where('title', 'Moderator'))->get();
        $organization->load('users');

        return view('backend.organization.update', compact('users', 'organization'));
    }

    public function update(Request $request, Organization $organization)
    {
        $request->validate([
            'users' => 'required|array',
            'users.*' => 'exists:user,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ], [
            'users.required' => 'Филиалга жавобгарни ходимни танланг.',
            'title.required' => 'Филиал номи мажбурий.',
            'title.string'   => 'Филиал номи матн бўлиши керак.',
        ]);

        $organization->update($request->only('title', 'description'));
        $organization->users()->sync($request->users);

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
