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
                                        <select class="form-select" name="min_budget">
                                            <option value="">Min Budget</option>
                                            <option value="0" {{ request('min_budget') == '0' ? 'selected' : '' }}>$0+
                                            </option>
                                            <option value="5000" {{ request('min_budget') == '5000' ? 'selected' : '' }}>
                                                $5,000+</option>
                                            <option value="15000" {{ request('min_budget') == '15000' ? 'selected' : '' }}>
                                                $15,000+</option>
                                        </select>
                                    </div>
                                    <div class="col">
                                        <select class="form-select" name="max_budget">
                                            <option value="">Max Budget</option>
                                            <option value="5000" {{ request('max_budget') == '5000' ? 'selected' : '' }}>
                                                Up to $5,000</option>
                                            <option value="15000" {{ request('max_budget') == '15000' ? 'selected' : '' }}>
                                                Up to $15,000</option>
                                            <option value="999999"
                                                {{ request('max_budget') == '999999' ? 'selected' : '' }}>Any amount
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Country Filter -->


                            <!-- Province Filter -->


                            <!-- Sort By -->
                            <div class="mb-3">
                                <label for="sort" class="form-label">Sort By</label>
                                <select class="form-select" id="sort" name="sort">
                                    <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest First
                                    </option>
                                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First
                                    </option>
                                    <option value="budget_high" {{ request('sort') == 'budget_high' ? 'selected' : '' }}>
                                        Budget (High to Low)</option>
                                    <option value="budget_low" {{ request('sort') == 'budget_low' ? 'selected' : '' }}>
                                        Budget (Low to High)</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
                            @if (request()->hasAny(['search', 'min_budget', 'max_budget', 'paises_id', 'provincia_id', 'sort']))
                                <a href="{{ route('jobs.index') }}" class="btn btn-outline-secondary w-100 mt-2">Clear
                                    Filters</a>
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
                        @if (auth()->user()->user_type === 'user')
                            <a href="{{ route('jobs.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Post a Job
                            </a>
                        @endif
                    @endauth
                </div>

                @if (auth()->user()->user_type === 'user')
                    @if ($jobs->isEmpty())
                        <div class="alert alert-info">
                            No jobs found matching your criteria.
                        </div>
                    @else
                        <div class="row">
                            @foreach ($jobs as $job)
                                <div class="col-md-6 mb-4">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h5 class="card-title mb-0">
                                                    <a href="{{ route('jobs.show', $job) }}" class="text-decoration-none">
                                                        {{ $job->title }}
                                                    </a>
                                                </h5>
                                                <span
                                                    class="badge bg-{{ $job->status === 'open' ? 'success' : ($job->status === 'hired' ? 'primary' : 'secondary') }}">
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
                                                        <i class="fas fa-money-bill-wave"></i>
                                                        @switch($job->budget)
                                                            @case(1)
                                                                Under $5,000
                                                            @break

                                                            @case(2)
                                                                $5,000 - $15,000
                                                            @break

                                                            @case(3)
                                                                $15,000+
                                                            @break

                                                            @default
                                                                Budget not specified
                                                        @endswitch
                                                    </span>

                                                </div>
                                                <a href="{{ route('jobs.edit', $job) }}"
                                                    class="btn btn-outline-primary btn-sm">
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
                @else
                    @php
                        $userProvinciaIdRaw = auth()->user()->provincia_id;

                        // First decode gives string like: '["4","5"]'
                        $decodedOnce = json_decode($userProvinciaIdRaw, true);

                        // Second decode gives the actual array
                        $userProvinces = json_decode($decodedOnce, true);

                        if (!is_array($userProvinces)) {
                            $userProvinces = [];
                        }
                    @endphp



                    @foreach ($jobsData as $job)
                        @if (in_array((string) $job->provincia_id, $userProvinces))
                            <!-- Display matching job -->
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
                                            <span
                                                class="badge bg-{{ $job->status === 'open' ? 'success' : ($job->status === 'hired' ? 'primary' : 'secondary') }} mb-2">
                                                {{-- {{ ucfirst($job->status) }} --}}
                                                Hiring
                                            </span>
                                            <p class="mb-0">Budget: ${{ number_format($job->budget, 2) }}</p>
                                        </div>
                                    </div>

                                    <p class="card-text">{{ Str::limit($job->description, 200) }}</p>

                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <div>
                                            <span class="badge bg-success">{{ $job->provincia_id ? "Latest":"CLOSED" }}</span>
                                        </div>
                                        <div>
                                            <a href="{{ route('freelance.jobs.edit', $job) }}"
                                                class="btn btn-outline-primary btn-sm">View Job Details</a>
                                            
                                           
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
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
                                provinceSelect.append(
                                    `<option value="${province.id}">${province.name}</option>`
                                );
                            });
                        });
                    }
                });
            });
        </script>
    @endpush
@endsection
