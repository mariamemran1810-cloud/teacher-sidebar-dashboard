<?php

namespace App\Http\Controllers;

use App\Models\School;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SchoolController extends Controller
{
    /**
     * Display school information.
     */
    public function index(): View
    {
        $school = School::firstOrFail();

        return view('school.index', compact('school'));
    }

    /**
     * Update school information.
     */
    public function update(Request $request): RedirectResponse
    {
        $school = School::firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'principal_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'established_year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'website' => 'nullable|url|max:255',
        ]);

        $school->update($validated);

        return redirect()->route('school.index')
            ->with('success', 'School information updated successfully.');
    }
}
