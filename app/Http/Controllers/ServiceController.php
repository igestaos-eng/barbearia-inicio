<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\View\View;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $services = Service::active()
            ->popular()
            ->get();

        return view('pages.services', compact('services'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Service $service): View
    {
        $service->load('barbers.user');

        return view('pages.service-detail', compact('service'));
    }
}
