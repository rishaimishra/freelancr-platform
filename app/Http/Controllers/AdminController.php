<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Job;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $userCount = User::count();
        $jobCount = Job::count();
        $recentUsers = User::latest()->take(5)->get();
        $recentJobs = Job::latest()->take(5)->get();

        return view('admin.dashboard', compact('userCount', 'jobCount', 'recentUsers', 'recentJobs'));
    }
}