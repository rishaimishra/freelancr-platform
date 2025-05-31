@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Users</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalUsers }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Jobs</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalJobs }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-briefcase fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Contractors</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalContractors }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-tie fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Applications</div>
                            {{-- <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalApplications }}</div> --}}
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="row">
        <!-- User Registration Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">User Registrations</h6>
                    <div class="dropdown no-arrow">
                        <select class="form-control" id="periodSelect">
                            <option value="day">Daily</option>
                            <option value="month">Monthly</option>
                            <option value="year">Yearly</option>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="userRegistrationChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Job Status Chart -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Job Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4">
                        <canvas id="jobStatusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row">
        <!-- Recent Jobs -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Jobs</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Client</th>
                                    <th>Status</th>
                                    <th>Posted</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentJobs as $job)
                                <tr>
                                    <td>{{ $job->title }}</td>
                                    <td>{{ $job->client->name }}</td>
                                    <td>
                                        <span class="badge bg-{{ $job->status === 'open' ? 'success' : ($job->status === 'hired' ? 'primary' : 'secondary') }}">
                                            {{ ucfirst($job->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $job->created_at->diffForHumans() }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Applications -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Applications</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Job</th>
                                    <th>Contractor</th>
                                    <th>Status</th>
                                    <th>Applied</th>
                                </tr>
                            </thead>
                            {{-- <tbody>
                                @foreach($recentApplications as $application)
                                <tr>
                                    <td>{{ $application->job->title }}</td>
                                    <td>{{ $application->contractor->name }}</td>
                                    <td>
                                        <span class="badge bg-{{ $application->status === 'pending' ? 'warning' : ($application->status === 'accepted' ? 'success' : 'danger') }}">
                                            {{ ucfirst($application->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $application->created_at->diffForHumans() }}</td>
                                </tr>
                                @endforeach
                            </tbody> --}}
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// User Registration Chart
const userCtx = document.getElementById('userRegistrationChart').getContext('2d');
const userChart = new Chart(userCtx, {
    type: 'line',
    data: {
        labels: @json($userChartData['labels']),
        datasets: [{
            label: 'New Users',
            data: @json($userChartData['data']),
            borderColor: 'rgb(75, 192, 192)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});

// Job Status Chart
const jobCtx = document.getElementById('jobStatusChart').getContext('2d');
const jobChart = new Chart(jobCtx, {
    type: 'doughnut',
    data: {
        labels: ['Open', 'Hired', 'Closed'],
        datasets: [{
            data: [
                {{ $jobStats['open'] }},
                {{ $jobStats['hired'] }},
                {{ $jobStats['closed'] }}
            ],
            backgroundColor: [
                'rgb(75, 192, 192)',
                'rgb(54, 162, 235)',
                'rgb(201, 203, 207)'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});

// Period Select Change Handler
document.getElementById('periodSelect').addEventListener('change', function() {
    fetch(`/admin/stats/users?period=${this.value}`)
        .then(response => response.json())
        .then(data => {
            userChart.data.labels = data.labels;
            userChart.data.datasets[0].data = data.data;
            userChart.update();
        });
});
</script>
@endpush
