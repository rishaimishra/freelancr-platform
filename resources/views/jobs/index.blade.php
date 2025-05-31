@extends('layouts.master')

@section('title', 'Available Jobs')

@section('content')
<div class="container py-5">
    <div class="row">
        <!-- Filters Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">Filters</h5>
                    <form action="{{ route('jobs.index') }}" method="GET" id="filterForm">
                        <!-- Search -->
                        <div class="mb-3">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                value="{{ request('search') }}" placeholder="Search jobs...">
                        </div>

                        <!-- Budget Range -->
                        <div class="mb-3">
                            <label class="form-label">Budget Range</label>
                            <div class="row g-2">
                                <div class="col">
                                    <input type="number" class="form-control" name="min_budget" 
                                        value="{{ request('min_budget') }}" placeholder="Min">
                                </div>
                                <div class="col">
                                    <input type="number" class="form-control" name="max_budget" 
                                        value="{{ request('max_budget') }}" placeholder="Max">
                                </div>
                            </div>
                        </div>

                        <!-- Country Filter -->
                        <div class="mb-3">
                            <label for="country" class="form-label">Country</label>
                            <select class="form-select" id="country" name="paises_id">
                                <option value="">All Countries</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country->id }}" {{ request('paises_id') == $country->id ? 'selected' : '' }}>
                                        {{ $country->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Province Filter -->
                        <div class="mb-3">
                            <label for="province" class="form-label">Province</label>
                            <select class="form-select" id="province" name="provincia_id">
                                <option value="">All Provinces</option>
                                @if(request('paises_id'))
                                    @foreach($provinces as $province)
                                        <option value="{{ $province->id }}" {{ request('provincia_id') == $province->id ? 'selected' : '' }}>
                                            {{ $province->name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <!-- Sort By -->
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
                        @if(request()->hasAny(['search', 'min_budget', 'max_budget', 'paises_id', 'provincia_id', 'sort']))
                            <a href="{{ route('jobs.index') }}" class="btn btn-outline-secondary w-100 mt-2">Clear Filters</a>
                        @endif
                    </form>
                </div>
            </div>
        </div>

        <!-- Jobs List -->
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Available Jobs</h2>
                @auth
                    @if(auth()->user()->user_type === 'client')
                        <a href="{{ route('jobs.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Post a Job
                        </a>
                    @endif
                @endauth
            </div>

            @if($jobs->isEmpty())
                <div class="alert alert-info">
                    No jobs found matching your criteria.
                </div>
            @else
                <div class="row">
                    @foreach($jobs as $job)
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h5 class="card-title mb-0">
                                            <a href="{{ route('jobs.show', $job) }}" class="text-decoration-none">
                                                {{ $job->title }}
                                            </a>
                                        </h5>
                                        <span class="badge bg-{{ $job->status === 'open' ? 'success' : ($job->status === 'hired' ? 'primary' : 'secondary') }}">
                                            {{ ucfirst($job->status) }}
                                        </span>
                                    </div>
                                    
                                    <p class="card-text text-muted small mb-2">
                                        Posted {{ $job->created_at->diffForHumans() }}
                                    </p>
                                    
                                    <p class="card-text mb-3">
                                        {{ Str::limit($job->description, 150) }}
                                    </p>
                                    
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="badge bg-info me-2">
                                                <i class="fas fa-money-bill-wave"></i> ${{ number_format($job->budget, 2) }}
                                            </span>
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-map-marker-alt"></i> {{ $job->province->name }}, {{ $job->country->name }}
                                            </span>
                                        </div>
                                        <a href="{{ route('jobs.show', $job) }}" class="btn btn-outline-primary btn-sm">
                                            View Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $jobs->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize Select2
    $('#country, #province, #sort').select2({
        theme: 'bootstrap-5'
    });

    // Handle country change
    $('#country').change(function() {
        const countryId = $(this).val();
        const provinceSelect = $('#province');
        
        provinceSelect.empty().append('<option value="">All Provinces</option>');
        
        if (countryId) {
            $.get(`/api/provinces/${countryId}`, function(provinces) {
                provinces.forEach(province => {
                    provinceSelect.append(`<option value="${province.id}">${province.name}</option>`);
                });
            });
        }
    });
});
</script>
@endpush
@endsection 