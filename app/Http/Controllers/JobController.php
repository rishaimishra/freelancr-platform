<?php

namespace App\Http\Controllers;

use App\Models\CompanyType;
use App\Models\Job;
use App\Models\Pais;
use App\Models\Payment;
use App\Models\Provincia;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function index(Request $request)
    {
        $query = Job::query()
            ->where('status', 'open'); // Show only open jobs

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

        // Apply budget range filter (updated for 1,2,3 values)
        if ($request->has('min_budget') || $request->has('max_budget')) {
            $query->where(function ($q) use ($request) {
                $min = $request->min_budget ? (int) $request->min_budget : 0;
                $max = $request->max_budget ? (int) $request->max_budget : PHP_INT_MAX;

                // Convert dollar amounts to budget ranges (1,2,3)
                $validRanges = [];

                // Check which ranges match the filter criteria
                if ($min <= 5000 && $max >= 0) {
                    $validRanges[] = 1; // Under $5,000
                }
                if ($min <= 15000 && $max >= 5000) {
                    $validRanges[] = 2; // $5,000-$15,000
                }
                if ($min >= 15000 || $max >= 15000) {
                    $validRanges[] = 3; // $15,000+
                }

                if (!empty($validRanges)) {
                    $q->whereIn('budget', $validRanges);
                }
            });
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
        $jobsData = Job::where('status', 'hired')->get();
        $provinces = $request->has('paises_id') ? Provincia::where('paises_id', $request->paises_id)->get() : collect();

        return view('jobs.index', compact('jobs', 'provinces', 'jobsData'));
    }

    public function create()
    {
        if (auth()->user()->user_type !== 'user') {
            return redirect()->route('jobs.index')->with('error', 'Only clients can update jobs.');
        }
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
        if (auth()->user()->user_type !== 'user') {
            return redirect()->route('jobs.index')->with('error', 'Only clients can update jobs.');
        }
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

        return redirect()->route('jobs.index', $job)
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
        if (auth()->user()->user_type !== 'user') {
            return redirect()->route('jobs.index')->with('error', 'Only clients can update jobs.');
        }
        // $this->authorize('update', $job);
        $provinces = Provincia::all();
        $current_province = Provincia::find($job->provincia_id); // Get current province
        $company_types = CompanyType::all();

        return view('jobs.edit', compact('job', 'provinces', 'current_province', 'company_types'));
    }
    public function freelanceredit(Job $job)
    {
        if (auth()->user()->user_type !== 'contractor') {
            return redirect()->route('jobs.index')->with('error', 'Only clients can update jobs.');
        }
        // $this->authorize('update', $job);
        $provinces = Provincia::all();
        $current_province = Provincia::find($job->provincia_id); // Get current province
        $company_types = CompanyType::all();

        return view('contractorjobs.edit', compact('job', 'provinces', 'current_province', 'company_types'));
    }

    public function update(Request $request, Job $job)
    {
        if (auth()->user()->user_type !== 'user') {
            return redirect()->route('jobs.index')->with('error', 'Only clients can update jobs.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'budget' => 'required|integer|between:0,3',  // Changed to include 0 (-- choose -- option)
            'provincia_id' => 'required|exists:provincias,id',
            'company_types' => 'required|array',
            'company_types.*' => 'exists:company_type,id',  // Fixed table name (was company_type)
        ]);

        // Update the job with validated data
        $job->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'budget' => $validated['budget'],
            'provincia_id' => $validated['provincia_id'],
            'company_types' => $validated['company_types'],
        ]);

        // If using many-to-many relationship for company types, sync them
        if (method_exists($job, 'companyTypes')) {
            $job->companyTypes()->sync($validated['company_types']);
        }

        return redirect()->route('jobs.index')
            ->with('success', 'Job updated successfully!');
    }

    public function destroy(Job $job)
    {
        if (auth()->user()->user_type !== 'user') {
            return redirect()->route('jobs.index')->with('error', 'Only clients can update jobs.');
        }
        $job->delete();
        return redirect()->route('jobs.index')
            ->with('success', 'Job deleted successfully!');
    }

    public function apply($id)
    {
        // Check if user is a contractor
        if (auth()->user()->user_type !== 'contractor') {
            return redirect()->back()->with('error', 'Only contractors can apply for jobs.');
        }
        $job = Job::findOrFail($id);


        if (!$job) {
            return redirect()->back()->with('error', 'The job does not exist.');
        }
        // Check if the job is open
        if ($job->status !== 'hired') {
            return redirect()->back()->with('error', 'This job is no longer available.');
        }

        // Check if the contractor has already applied
        // Create a payment record
        $payment = Payment::create([
            'user_id' => auth()->id(),
            'job_id' => (int) $job->id,
            'amount' => 50.00,
            'payment_method' => 'pending',
            'status' => 'pending',
        ]);

        // Redirect to payment option selection
        return view('payments.options', [
            'job' => $job,
            'payment' => $payment,
        ]);

    }

    public function adminIndex()
    {
        $jobs = Job::with('user')->latest()->paginate(10);
        return view('admin.jobs.index', compact('jobs'));
    }

    public function adminShow(Job $job)
    {
        return view('admin.jobs.show', compact('job'));
    }

    public function adminDestroy(Job $job)
    {
        $job->delete();
        return redirect()->route('admin.jobs.index')
            ->with('success', 'Job deleted successfully');
    }
}