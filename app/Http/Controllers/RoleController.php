<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;


class RoleController extends Controller implements HasMiddleware
{

    public static function middleware(): array
    {
        return [
            new Middleware('permission:view roles', only: ['index']),
            new Middleware('permission:edit roles', only: ['edit']),
            new Middleware('permission:create roles', only: ['create']),
            new Middleware('permission:delete roles', only: ['destroy']),
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::orderBy('name', 'asc')->paginate(10);
        return view('roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $permissions = Permission::orderBy('name', 'asc')->get();
        return view('roles.create', compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:3|max:255|unique:roles,name',
        ]);

        if ($validator->passes()) {
            $role = Role::create(['name' => $request->name, 'guard_name' => 'web']);

            if(!empty($request->permissions)){
                foreach ($request->permissions as $name) {
                    $role->givePermissionTo($name);
                }
            }
            return redirect()->route('roles.index')->with('success', 'Role created successfully');
        } else{
            return redirect()->route('roles.create')->withInput()->withErrors($validator);
        }
    }


    public function edit(string $id)
    {
        $role = Role::findOrFail($id);
        $permissions = Permission::orderBy('name', 'asc')->get();

        // Kelompokkan permission berdasarkan kata terakhir (entity name)
        $groupedPermissions = [];

        foreach ($permissions as $permission) {
            // Pisah berdasarkan spasi
            $parts = explode(' ', $permission->name);

            // Ambil entity (kata terakhir)
            $groupKey = end($parts); // contoh: user, product

            $groupedPermissions[$groupKey][] = $permission;
        }

        return view('roles.edit', compact('role', 'groupedPermissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $role = Role::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:3|max:255|unique:roles,name,' . $role->id,
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('roles.edit', $role->id)
                ->withInput()
                ->withErrors($validator);
        }

        // Update nama role
        $role->update([
            'name' => $request->name,
            'guard_name' => 'web',
        ]);

        // Sinkronisasi permissions
        $role->syncPermissions($request->permissions ?? []);

        return redirect()->route('roles.index')->with('success', 'Role updated successfully.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $role = Role::findOrFail($id);
        $role->delete();

        return redirect()->route('roles.index')->with('success', 'Role deleted successfully');
    }
}
