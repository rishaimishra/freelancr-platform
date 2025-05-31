@extends('layouts.master')

@section('title', 'My Jobs')

@section('content')
<div class="container">
    <div class="row">
        <!-- Filters Sidebar -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Filter Jobs</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('profile') }}" method="GET">
                        <div class="mb-3">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                value="{{ request('search') }}" placeholder="Search jobs...">
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">All Status</option>
                                <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                                <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                                <option value="hired" {{ request('status') == 'hired' ? 'selected' : '' }}>Hired</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="sort" class="form-label">Sort By</label>
                            <select class="form-select" id="sort" name="sort">
                                <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest First</option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                                <option value="budget_high" {{ request('sort') == 'budget_high' ? 'selected' : '' }}>Budget (High to Low)</option>
                                <option value="budget_low" {{ request('sort') == 'budget_low' ? 'selected' : '' }}>Budget (Low to High)</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Jobs List -->
        <div class="col-md-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>My Posted Jobs</h2>
                <a href="{{ route('jobs.create') }}" class="btn btn-primary">Post New Job</a>
            </div>

            @if(isset($jobs) && $jobs->count() > 0)
                @foreach($jobs as $job)
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h5 class="card-title">{{ $job->title }}</h5>
                                    <p class="card-text text-muted">
                                        Posted {{ $job->created_at->diffForHumans() }}
                                    </p>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-{{ $job->status === 'open' ? 'success' : 
                                        ($job->status === 'hired' ? 'primary' : 'secondary') }} mb-2">
                                        {{ ucfirst($job->status) }}
                                    </span>
                                    <p class="mb-0">Budget: ${{ number_format($job->budget, 2) }}</p>
                                </div>
                            </div>
                            
                            <p class="card-text">{{ Str::limit($job->description, 200) }}</p>
                            
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div>
                                    <span class="badge bg-info me-2">{{ $job->paises_id }}</span>
                                    <span class="badge bg-info">{{ $job->provincia_id }}</span>
                                </div>
                                <div>
                                    <a href="{{ route('jobs.show', $job) }}" class="btn btn-outline-primary btn-sm">View Details</a>
                                    <a href="{{ route('jobs.edit', $job) }}" class="btn btn-outline-secondary btn-sm">Edit</a>
                                    <form action="{{ route('jobs.destroy', $job) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm" 
                                            onclick="return confirm('Are you sure you want to delete this job?')">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="d-flex justify-content-center">
                    {{ $jobs->links() }}
                </div>
            @else
                <div class="alert alert-info">
                    You haven't posted any jobs yet.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Select2 for dropdowns
        $('#status, #sort').select2({
            theme: 'bootstrap-5'
        });
    });
</script>
@endsection 