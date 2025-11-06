<?php

namespace App\Http\Controllers;

use App\Models\Barber;
use Illuminate\View\View;

class BarberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $barbers = Barber::with('user')
            ->where('is_available', true)
            ->orderBy('rating', 'desc')
            ->get();

        return view('pages.barbers', compact('barbers'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Barber $barber): View
    {
        $barber->load(['user', 'services', 'workingHours']);

        return view('pages.barber-detail', compact('barber'));
    }
}
