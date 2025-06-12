<?php

use App\Http\Controllers\AttendanceReportController;
use App\Http\Controllers\DepartementController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PresenceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SetingController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use App\Models\Task;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect(route('login'));
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users/store', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::post('/users/{user}/change-password', [UserController::class, 'changePassword'])->name('users.change-password');
    Route::delete('/users/{id}/delete', [UserController::class, 'destroy'])->name('users.destroy');

    Route::get('/permission', [PermissionController::class, 'index'])->name('permissions.index');
    Route::get('/permission/create', [PermissionController::class, 'create'])->name('permissions.create');
    Route::post('/permission/store', [PermissionController::class, 'store'])->name('permissions.store');
    Route::get('/permission/{id}/edit', [PermissionController::class, 'edit'])->name('permissions.edit');
    Route::put('/permission/{id}', [PermissionController::class, 'update'])->name('permissions.update');
    Route::delete('/permission/{id}/delete', [PermissionController::class, 'destroy'])->name('permissions.destroy');

    Route::get('/role', [RoleController::class, 'index'])->name('roles.index');
    Route::get('/role/create', [RoleController::class, 'create'])->name('roles.create');
    Route::post('/role/store', [RoleController::class, 'store'])->name('roles.store');
    Route::get('/role/{id}/edit', [RoleController::class, 'edit'])->name('roles.edit');
    Route::put('/role/{id}', [RoleController::class, 'update'])->name('roles.update');
    Route::delete('/role/{id}/delete', [RoleController::class, 'destroy'])->name('roles.destroy');

    Route::get('/departement', [DepartementController::class, 'index'])->name('departements.index');
    Route::get('/departement/create', [DepartementController::class, 'create'])->name('departements.create');
    Route::post('/departement/store', [DepartementController::class, 'store'])->name('departements.store');
    Route::get('/departement/{id}/edit', [DepartementController::class, 'edit'])->name('departements.edit');
    Route::put('/departement/{id}', [DepartementController::class, 'update'])->name('departements.update');
    Route::delete('/departement/{id}/delete', [DepartementController::class, 'destroy'])->name('departements.destroy');

    Route::get('/task', [TaskController::class, 'index'])->name('tasks.index');
    Route::get('/task/create', [TaskController::class, 'create'])->name('tasks.create');
    Route::post('/task/store', [TaskController::class, 'store'])->name('tasks.store');
    Route::get('/task/{id}/edit', [TaskController::class, 'edit'])->name('tasks.edit');
    Route::put('/task/{id}', [TaskController::class, 'update'])->name('tasks.update');
    Route::delete('/task/{id}/delete', [TaskController::class, 'destroy'])->name('tasks.destroy');

    //handle Task
    Route::get('tasks/pending/{id}', [TaskController::class, 'pending'])->name('tasks.pending');
    Route::get('tasks/selesai/{id}', [TaskController::class, 'selesai'])->name('tasks.selesai');

    Route::get('/presence', [PresenceController::class, 'index'])->name('presences.index');
    Route::post('/presence/checkin', [PresenceController::class, 'checkIn'])->name('presences.checkin');
    Route::post('/presence/checkout', [PresenceController::class, 'checkOut'])->name('presences.checkout');
    Route::get('/presence/location/{id}', [PresenceController::class, 'getLocationDetails'])->name('presences.location');

    Route::resource('setings', SetingController::class);
    Route::post('locations/{location}/toggle', [SetingController::class, 'toggle'])->name('locations.toggle');

    Route::resource('payrolls', PayrollController::class);

    Route::get('reports', [AttendanceReportController::class, 'index'])->name('reports.index');

    Route::resource('/leave_requests', LeaveRequestController::class);

    Route::patch('/leave_requests/{leave_request}/update-status', [LeaveRequestController::class, 'updateStatus'])
        ->name('leave_requests.update_status');
        
    Route::get('leave_requests/rejected/{id}', [LeaveRequestController::class, 'rejected'])->name('leave_requests.rejected');
    Route::get('leave_requests/approved/{id}', [LeaveRequestController::class, 'approved'])->name('leave_requests.approved');


});

require __DIR__.'/auth.php';
