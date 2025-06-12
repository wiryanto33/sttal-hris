<?php

namespace App\Http\Controllers;

use App\Models\Departement;
use App\Models\User;
use App\Models\UserDetail;
use App\Traites\FileUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller implements HasMiddleware
{

    public static function middleware(): array
    {
        return [
            new Middleware('permission:view users', only: ['index']),
            new Middleware('permission:edit users', only: ['edit']),
            new Middleware('permission:create users', only: ['create']),
            new Middleware('permission:delete users', only: ['destroy']),
        ];
    }

    use FileUpload;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole('superadmin')) {
            // If the user is a superadmin, show all users
            $users = User::with('roles')->get(); // Eager load roles
        } else {
            // Jika bukan superadmin, tampilkan hanya user yang sedang login
            $users = User::with('roles')->where('id', $user->id)->get();
        }

        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $departements = Departement::all();
        $roles = Role::all();
        return view('users.create', compact('roles', 'departements'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $validatedUser = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create($validatedUser);

        $validateDetail = $request->validate([
            'pangkat' => 'required|string|max:255',
            'korps' => 'required|string|max:255',
            'nrp' => 'required|string|max:255|unique:user_details,nrp',
            'gender' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'address' => 'nullable|string|max:255',
            'birth_date' => 'required|date',
            'join_date' => 'required|date',
            'phone' => 'nullable|string|max:15',
            'departement_id' => 'required',
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|string',
            'salary' => 'required|numeric',
        ]);

        // Handle image if uploaded
        if ($request->hasFile('image')) {
            $image = $this->uploadFile($request->file('image'));
            $validateDetail['image'] = $image;
        }

        $validateDetail['user_id'] = $user->id;

        UserDetail::create($validateDetail);

        $user->assignRole(Role::find($request->role_id)->name);

        // Create the users

        // Optional: redirect or return response
        return redirect()->route('users.index')->with('success', 'Data User berhasil disimpan.');
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::find($id);
        $departements = Departement::all();
        $roles = Role::orderBy('name', 'asc')->get();

        $hasRoles = $user->roles->pluck('name');

        return view('users.edit', compact('user', 'departements', 'roles', 'hasRoles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        // Validasi untuk tabel users
        $validateUser = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
        ]);

        if ($validateUser->fails()) {
            return redirect()->back()->withErrors($validateUser)->withInput();
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        // Validasi untuk detail user
        $validateDetail = Validator::make($request->all(), [
            'pangkat' => 'required|string|max:255',
            'korps' => 'required|string|max:255',
            'nrp' => 'required|string|max:255|unique:user_details,nrp,' . $user->id . ',user_id',
            'gender' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'address' => 'nullable|string|max:255',
            'birth_date' => 'required|date',
            'join_date' => 'required|date',
            'phone' => 'nullable|string|max:15',
            'departement_id' => 'required|exists:departements,id',
            'role' => 'required|exists:roles,name',
            'status' => 'required|string',
            'salary' => 'required|numeric',
        ]);

        if ($validateDetail->fails()) {
            return redirect()->back()->withErrors($validateDetail)->withInput();
        }

        $data = $request->only([
            'pangkat',
            'korps',
            'nrp',
            'gender',
            'address',
            'birth_date',
            'join_date',
            'phone',
            'departement_id',
            'status',
            'salary'
        ]);

        if ($request->hasFile('image')) {
            // Hapus file lama jika ada
            if ($user->userDetail && $user->userDetail->image) {
                $this->deleteFile($user->userDetail->image);
            }

            // Upload file baru
            $image = $this->uploadFile($request->file('image'));

            // Simpan ke array validasi detail
            $data['image'] = $image;
        }

        // Simpan atau update user detail
        if ($user->userDetail) {
            $user->userDetail->update($data);
        } else {
            $data['user_id'] = $user->id;
            \App\Models\UserDetail::create($data);
        }

        // Update Role menggunakan Spatie
        $user->syncRoles([$request->role]);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function changePassword(Request $request, User $user)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'Current password is incorrect.',
            ]);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);
        $user->delete();
        return redirect()->route('users.index')->with('success', 'Data User berhasil dihapus.');
    }
}
