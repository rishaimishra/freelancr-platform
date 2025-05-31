<?php

namespace App\Http\Controllers;

use App\Models\CompanyType;
use App\Models\Provincia;
use App\Models\User;
use App\Models\Job;
use App\Models\JobApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Get total counts
        $totalUsers = User::count();
        $totalJobs = Job::count();
        $totalContractors = User::where('user_type', 'contractor')->count();
        // $totalApplications = JobApplication::count();

        // Get recent jobs
        $recentJobs = Job::with('client')
            ->latest()
            ->take(5)
            ->get();

        // Get recent applications
       

        // Get job statistics
        $jobStats = [
            'open' => Job::where('status', 'open')->count(),
            'hired' => Job::where('status', 'hired')->count(),
            'closed' => Job::where('status', 'closed')->count(),
        ];

        // Get user registration data for the last 30 days
        $userChartData = $this->getUserRegistrationData('day');

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalJobs',
            'totalContractors',
            // 'totalApplications',
            'recentJobs',
            // 'recentApplications',
            'jobStats',
            'userChartData'
        ));
    }

    public function getUserStats(Request $request)
    {
        $period = $request->get('period', 'day');
        $data = $this->getUserRegistrationData($period);
        return response()->json($data);
    }

    private function getUserRegistrationData($period)
    {
        $query = User::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('count(*) as total')
        )
        ->groupBy('date')
        ->orderBy('date');

        switch ($period) {
            case 'month':
                $query = User::select(
                    DB::raw('DATE_FORMAT(created_at, "%Y-%m") as date'),
                    DB::raw('count(*) as total')
                )
                ->groupBy('date')
                ->orderBy('date');
                break;

            case 'year':
                $query = User::select(
                    DB::raw('YEAR(created_at) as date'),
                    DB::raw('count(*) as total')
                )
                ->groupBy('date')
                ->orderBy('date');
                break;
        }

        $data = $query->get();

        return [
            'labels' => $data->pluck('date'),
            'data' => $data->pluck('total')
        ];
    }

    public function users(Request $request)
    {
        $query = User::withCount(['jobs']);

        // Search
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by type
        if ($request->has('type') && $request->get('type') !== '') {
            $query->where('user_type', $request->get('type'));
        }

        // Sort
        switch ($request->get('sort', 'latest')) {
            case 'oldest':
                $query->oldest();
                break;
            case 'name':
                $query->orderBy('name');
                break;
            default:
                $query->latest();
                break;
        }

        $users = $query->paginate(10)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'user_type' => 'required|in:user,contractor'
        ]);

        $user->update($validated);

        return redirect()->route('admin.users')
            ->with('success', 'User updated successfully.');
    }

    public function destroyUser(User $user)
    {
        // Prevent deleting the last admin
        if ($user->user_type === 'admin' && User::where('user_type', 'admin')->count() <= 1) {
            return redirect()->route('admin.users')
                ->with('error', 'Cannot delete the last admin user.');
        }

        $user->delete();

        return redirect()->route('admin.users')
            ->with('success', 'User deleted successfully.');
    }

    public function jobs(Request $request)
    {
        $query = Job::with(['client', 'provincia']);

        // Search
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && $request->get('status') !== '') {
            $query->where('status', $request->get('status'));
        }

        // Sort
        switch ($request->get('sort', 'latest')) {
            case 'oldest':
                $query->oldest();
                break;
            case 'budget_high':
                $query->orderBy('budget', 'desc');
                break;
            case 'budget_low':
                $query->orderBy('budget', 'asc');
                break;
            default:
                $query->latest();
                break;
        }

        $jobs = $query->paginate(10)->withQueryString();

        return view('admin.jobs.index', compact('jobs'));
    }

     public function editJob(Job $job)
    {
        if (auth()->user()->user_type !== 'admin') {
            return redirect()->route('admin.jobs')->with('error', 'Only clients can update jobs.');
        }
        // $this->authorize('update', $job);
        $provinces = Provincia::all();
        $current_province = Provincia::find($job->provincia_id); // Get current province
        $company_types = CompanyType::all();

        return view('admin.jobs.edit', compact('job', 'provinces', 'current_province', 'company_types'));
    }

    public function updateJob(Request $request, Job $job)
    {
         if (auth()->user()->user_type !== 'admin') {
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

        return redirect()->route('admin.jobs')
            ->with('success', 'Job updated successfully.');
    }

     public function updateJobStatus(Request $request, Job $job)
    {
        // dd($request->all());
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'status' => 'required|in:open,hired,closed'
        ]);

        $job->update($validated);

        return redirect()->route('admin.jobs')
            ->with('success', 'Job updated successfully.');
    }

    public function destroyJob(Job $job)
    {
        $job->delete();

        return redirect()->route('admin.jobs')
            ->with('success', 'Job deleted successfully.');
    }

    public function applications()
    {
        $applications = JobApplication::with(['job', 'contractor'])
            ->latest()
            ->paginate(10);
        return view('admin.applications.index', compact('applications'));
    }

    public function settings()
    {
        return view('admin.settings.index');
    }

    public function profile()
    {
        return view('admin.profile');
    }

     public function logout(Request $request)
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}