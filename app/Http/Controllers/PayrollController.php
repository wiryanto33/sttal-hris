<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\User;
use Illuminate\Http\Request;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class PayrollController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view payrolls', only: ['index']),
            new Middleware('permission:edit payrolls', only: ['edit']),
            new Middleware('permission:create payrolls', only: ['create']),
            new Middleware('permission:delete payrolls', only: ['destroy']),
            new Middleware('permission:show payrolls', only: ['show']),
        ];
    }
    public function index()
    {
        $payrolls = Payroll::orderBy('pay_date', 'desc')->get();
        return view('payrolls.index', compact('payrolls'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::all();

        return view('payrolls.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'salary' => 'required|numeric',
            'bonuses' => 'nullable|numeric',
            'deductions' => 'nullable|numeric',
            'net_salary' => 'required|numeric',
            'date' => 'required|date',
        ]);

        $payroll = new Payroll();
        $payroll->user_id = $request->user_id;
        $payroll->salary = $request->salary;
        $payroll->bonuses = $request->bonuses;
        $payroll->deductions = $request->deductions;
        $payroll->net_salary = $request->net_salary;
        $payroll->pay_date = $request->date; // disesuaikan dengan nama kolom di DB
        $payroll->save();

        return redirect()->route('payrolls.index')->with('success', 'Payroll created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Payroll $payroll)
    {
        return view('payrolls.show', compact('payroll'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Payroll $payroll)
    {
        $users = User::all();
        return view('payrolls.edit', compact('payroll', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Payroll $payroll)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'salary' => 'required|numeric',
            'bonuses' => 'nullable|numeric',
            'deductions' => 'nullable|numeric',
            'net_salary' => 'required|numeric',
            'date' => 'required|date',
        ]);

        $payroll->user_id = $request->user_id;
        $payroll->salary = $request->salary;
        $payroll->bonuses = $request->bonuses;
        $payroll->deductions = $request->deductions;
        $payroll->net_salary = $request->net_salary;
        $payroll->pay_date = $request->date; // disesuaikan dengan nama kolom di DB
        $payroll->save();

        return redirect()->route('payrolls.index')->with('success', 'Payroll updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payroll $payroll)
    {
        $payroll->delete();
        return redirect()->route('payrolls.index')->with('success', 'Payroll deleted successfully.');
    }
}
