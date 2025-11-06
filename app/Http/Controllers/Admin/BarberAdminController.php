<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateBarberRequest;
use App\Http\Requests\UpdateBarberRequest;
use App\Models\Barber;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BarberAdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $barbers = Barber::with('user')
            ->latest()
            ->paginate(15);

        return view('admin.barbers.index', compact('barbers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        // Get users with barber role who don't have a barber profile yet
        $users = User::where('role', 'barber')
            ->whereDoesntHave('barber')
            ->get();

        return view('admin.barbers.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateBarberRequest $request): RedirectResponse
    {
        Barber::create($request->validated());

        return redirect()
            ->route('admin.barbers.index')
            ->with('success', 'Barber created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Barber $barber): View
    {
        $barber->load('user', 'services', 'appointments');

        return view('admin.barbers.show', compact('barber'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Barber $barber): View
    {
        $barber->load('user');

        return view('admin.barbers.edit', compact('barber'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBarberRequest $request, Barber $barber): RedirectResponse
    {
        $barber->update($request->validated());

        return redirect()
            ->route('admin.barbers.index')
            ->with('success', 'Barber updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Barber $barber): RedirectResponse
    {
        $barber->delete();

        return redirect()
            ->route('admin.barbers.index')
            ->with('success', 'Barber deleted successfully.');
    }
}
