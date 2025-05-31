<?php

namespace App\Http\Controllers;

use App\Models\CompanyType;
use App\Models\Job;
use App\Models\Pais;
use App\Models\Provincia;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function index(Request $request)
    {
        $query = Job::with(['country', 'province']);

        // If user is a contractor, show only jobs in their area
        if (auth()->check() && auth()->user()->user_type === 'contractor') {
            $user = auth()->user();
            $query->where('provincia_id', $user->provincia_id);
        }

        // Apply search filter
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Apply budget range filter
        if ($request->has('min_budget')) {
            $query->where('budget', '>=', $request->min_budget);
        }
        if ($request->has('max_budget')) {
            $query->where('budget', '<=', $request->max_budget);
        }

        // Apply country filter
        if ($request->has('paises_id') && $request->paises_id !== '') {
            $query->where('paises_id', $request->paises_id);
        }

        // Apply province filter
        if ($request->has('provincia_id') && $request->provincia_id !== '') {
            $query->where('provincia_id', $request->provincia_id);
        }

        // Apply sorting
        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'oldest':
                    $query->oldest();
                    break;
                case 'budget_high':
                    $query->orderBy('budget', 'desc');
                    break;
                case 'budget_low':
                    $query->orderBy('budget', 'asc');
                    break;
                default: // latest
                    $query->latest();
                    break;
            }
        } else {
            $query->latest();
        }

        $jobs = $query->paginate(10)->withQueryString();
        $countries = Pais::all();
        $provinces = $request->has('paises_id') ? Provincia::where('paises_id', $request->paises_id)->get() : collect();

        return view('jobs.index', compact('jobs', 'countries', 'provinces'));
    }

    public function create()
    {
        $user = auth()->user();
        $countryId = $user->paises_id ?? null;
        if ($user->user_type !== 'user') {
            return redirect()->route('jobs.index')->with('error', 'Only clients can post jobs.');
        }
        $provinces = $countryId ? Provincia::where('fk_pais', $countryId)->get() : collect();
        $company_types = CompanyType::all();
        return view('jobs.create', compact('provinces', 'company_types'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'budget' => 'required|numeric|min:0',
            'provincia_id' => 'required|exists:provincias,id',
            'company_types' => 'required|array',
            'company_types.*' => 'exists:company_type,id',
        ]);

        $job = auth()->user()->jobs()->create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'budget' => $validated['budget'],
            'provincia_id' => $validated['provincia_id'],
            'company_types' => $validated['company_types'], // Store as JSON
        ]);

        return redirect()->route('jobs.show', $job)
            ->with('success', 'Job posted successfully!');
    }

    // In your controller
    // In your controller
    public function show(Job $job)
    {
        $companyTypes = CompanyType::whereIn('id', $job->company_types ?? [])->get();
        return view('jobs.show', compact('job', 'companyTypes'));
    }

    public function edit(Job $job)
    {
        $this->authorize('update', $job);
        $countries = Pais::all();
        $provinces = Provincia::where('paises_id', $job->paises_id)->get();
        return view('jobs.edit', compact('job', 'countries', 'provinces'));
    }

    public function update(Request $request, Job $job)
    {
        $this->authorize('update', $job);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'budget' => 'required|numeric|min:0',
            'paises_id' => 'required|exists:paises,id',
            'provincia_id' => 'required|exists:provincia,id',
            'status' => 'required|in:open,closed,hired',
        ]);

        $job->update($validated);

        return redirect()->route('jobs.show', $job)
            ->with('success', 'Job updated successfully!');
    }

    public function destroy(Job $job)
    {
        $this->authorize('delete', $job);
        $job->delete();
        return redirect()->route('jobs.index')
            ->with('success', 'Job deleted successfully!');
    }

    public function apply(Job $job)
    {
        // Check if user is a contractor
        if (auth()->user()->user_type !== 'contractor') {
            return redirect()->back()->with('error', 'Only contractors can apply for jobs.');
        }

        // Check if job is in contractor's area
        if (auth()->user()->provincia_id !== $job->provincia_id) {
            return redirect()->back()->with('error', 'This job is not available in your area.');
        }

        // Check if job is open
        if ($job->status !== 'open') {
            return redirect()->back()->with('error', 'This job is no longer accepting applications.');
        }

        // TODO: Implement payment processing
        // For now, just redirect to a payment page
        return redirect()->route('payment.job', $job);
    }
}