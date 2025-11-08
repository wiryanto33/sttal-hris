<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Traites\FileUpload;
use App\Models\Departement;
use Spatie\Permission\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    use FileUpload;
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user()->load('userDetail');
        $departements = Departement::all();
        $roles = Role::orderBy('name', 'asc')->get();
        return view('profile.edit', compact('user', 'departements', 'roles'));
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $user = $request->user();
        $user->save();

        // Handle optional UserDetail updates
        $detailRules = [
            'pangkat' => ['sometimes', 'nullable', 'string', 'max:255'],
            'korps' => ['sometimes', 'nullable', 'string', 'max:255'],
            'nrp' => ['sometimes', 'nullable', 'string', 'max:255', 'unique:user_details,nrp,' . $user->id . ',user_id'],
            'gender' => ['sometimes', 'nullable', 'in:male,female'],
            'image' => ['sometimes', 'nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'address' => ['sometimes', 'nullable', 'string', 'max:255'],
            'birth_date' => ['sometimes', 'nullable', 'date'],
            'join_date' => ['sometimes', 'nullable', 'date'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:15'],
            'departement_id' => ['sometimes', 'nullable', 'exists:departements,id'],
            'status' => ['sometimes', 'nullable', 'in:active,inactive'],
            'role' => ['sometimes', 'nullable', 'exists:roles,name'],
        ];

        $validatedDetails = $request->validate($detailRules);

        // Only superadmin may change 'status'
        if (!$user->hasRole('superadmin')) {
            unset($validatedDetails['status']);
        }

        if (!empty(array_filter($validatedDetails, fn ($v) => !is_null($v) && $v !== ''))) {
            // If image uploaded, handle upload and delete previous
            if ($request->hasFile('image')) {
                if ($user->userDetail && $user->userDetail->image) {
                    $this->deleteFile($user->userDetail->image);
                }
                $validatedDetails['image'] = $this->uploadFile($request->file('image'));
            }

            if ($user->userDetail) {
                $user->userDetail->update($validatedDetails);
            } else {
                // Do not create an incomplete user_detail record; only create if required keys are present
                $requiredForCreate = ['pangkat', 'korps', 'nrp', 'gender', 'birth_date', 'join_date'];
                $canCreate = !array_diff($requiredForCreate, array_keys(array_filter($validatedDetails, fn ($v) => !is_null($v) && $v !== '')));
                if ($canCreate) {
                    $validatedDetails['user_id'] = $user->id;
                    \App\Models\UserDetail::create($validatedDetails);
                }
            }
        }

        // Allow superadmin to change own role if provided
        if ($request->filled('role') && $user->hasRole('superadmin')) {
            $user->syncRoles([$request->role]);
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
