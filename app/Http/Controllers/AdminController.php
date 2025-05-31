<?php

namespace App\Http\Controllers;

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

    public function users()
    {
        $users = User::withCount(['jobs', 'applications'])
            ->latest()
            ->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    public function jobs()
    {
        $jobs = Job::with(['client', 'applications'])
            ->withCount('applications')
            ->latest()
            ->paginate(10);
        return view('admin.jobs.index', compact('jobs'));
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
}