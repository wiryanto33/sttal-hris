<?php

namespace App\Http\Controllers;

use App\Models\Departement;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class DepartementController extends Controller implements HasMiddleware
{

    public static function middleware(): array
    {
        return [
            new Middleware('permission:view departements', only: ['index']),
            new Middleware('permission:edit departements', only: ['edit']),
            new Middleware('permission:create departements', only: ['create']),
            new Middleware('permission:delete departements', only: ['destroy']),
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $departements = Departement::all();
        return view('departements.index', compact('departements'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('departements.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = $request->validate([
            'name' => 'required|string|unique:departements,name|min:3',
            'description' => 'nullable|string',
            'status' => 'required',
        ]);

        Departement::create($validate);
        return redirect()->route('departements.index')->with('success', 'Department created successfully.');
    }

    public function edit(string $id)
    {
        $departement = Departement::findOrFail($id);
        return view('departements.edit', compact('departement'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validate = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required',
        ]);

        Departement::where('id', $id)->update($validate);
        return redirect()->route('departements.index')->with('success', 'Department updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Departement::where('id', $id)->delete();
        return redirect()->route('departements.index')->with('success', 'Department deleted successfully.');

    }
}
