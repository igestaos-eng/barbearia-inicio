<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function index(): View
    {
        return view('pages.contact');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'message' => ['required', 'string', 'max:1000'],
        ]);

        // Log the contact message
        Log::info('Contact form submission', $validated);

        // TODO: Send email notification to admin
        // Mail::to(config('barbershop.email'))->send(new ContactMessage($validated));

        return back()->with('success', 'Thank you for your message! We will get back to you soon.');
    }
}
