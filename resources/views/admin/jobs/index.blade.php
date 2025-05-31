@extends('layouts.admin')

@section('title', 'Jobs Management')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Jobs Management</h1>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('admin.jobs') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="{{ request('search') }}" placeholder="Search by title or description">
                </div>
                <div class="col-md-2">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Status</option>
                        <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                        <option value="hired" {{ request('status') == 'hired' ? 'selected' : '' }}>Hired</option>
                        <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="sort" class="form-label">Sort By</label>
                    <select class="form-select" id="sort" name="sort">
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest</option>
                        <option value="budget_high" {{ request('sort') == 'budget_high' ? 'selected' : '' }}>Budget (High to Low)</option>
                        <option value="budget_low" {{ request('sort') == 'budget_low' ? 'selected' : '' }}>Budget (Low to High)</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">Filter</button>
                    <a href="{{ route('admin.jobs') }}" class="btn btn-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Jobs Table -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Client</th>
                            <th>Budget</th>
                            <th>Status</th>
                            <th>Applications</th>
                            <th>Posted</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jobs as $job)
                            <tr>
                                <td>{{ $job->title }}</td>
                                <td>{{ $job->client->name }}</td>
                                <td>${{ number_format($job->budget, 2) }}</td>
                                <td>
                                    <span class="badge bg-{{ $job->status === 'open' ? 'success' : ($job->status === 'hired' ? 'primary' : 'secondary') }}">
                                        {{ ucfirst($job->status) }}
                                    </span>
                                </td>
                                <td>{{ $job->applications_count ?? 0 }}</td>
                                <td>{{ $job->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-info" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#viewJobModal{{ $job->id }}">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                      

                                        <a type="button" href="{{route('admin.jobs.edit',$job->id)}}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                          <button type="button" class="btn btn-sm btn-info"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editJobModal{{ $job->id }}">
                                            Approve
                                        </button>
                                        <form action="{{ route('admin.jobs.destroy', $job) }}" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete this job?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>

                                    <!-- View Job Modal -->
                                    <div class="modal fade" id="viewJobModal{{ $job->id }}" tabindex="-1">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Job Details</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <strong>Title:</strong> {{ $job->title }}
                                                    </div>
                                                    <div class="mb-3">
                                                        <strong>Description:</strong>
                                                        <p class="mt-2">{{ $job->description }}</p>
                                                    </div>
                                                    <div class="mb-3">
                                                        <strong>Budget:</strong> ${{ number_format($job->budget, 2) }}
                                                    </div>
                                                    <div class="mb-3">
                                                        <strong>Status:</strong> 
                                                        <span class="badge bg-{{ $job->status === 'open' ? 'success' : ($job->status === 'hired' ? 'primary' : 'secondary') }}">
                                                            {{ ucfirst($job->status) }}
                                                        </span>
                                                    </div>
                                                    <div class="mb-3">
                                                        <strong>Client:</strong> {{ $job->client->name }}
                                                    </div>
                                                    <div class="mb-3">
                                                        <strong>Location:</strong> {{ $job->provincia->nombre }}
                                                    </div>
                                                    <div class="mb-3">
                                                        <strong>Posted:</strong> {{ $job->created_at->format('M d, Y H:i') }}
                                                    </div>
                                                    <div class="mb-3">
                                                        <strong>Last Updated:</strong> {{ $job->updated_at->format('M d, Y H:i') }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Edit Job Modal -->
                                    <div class="modal fade" id="editJobModal{{ $job->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit Job</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form action="{{ route('admin.jobs.update.status', $job) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label for="title" class="form-label">Title</label>
                                                            <input type="text" class="form-control" id="title" name="title" 
                                                                   value="{{ $job->title }}" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="status" class="form-label">Status</label>
                                                            <select class="form-select" id="status" name="status" required>
                                                                <option value="open" {{ $job->status == 'open' ? 'selected' : '' }}>Open</option>
                                                                <option value="hired" {{ $job->status == 'hired' ? 'selected' : '' }}>Approved</option>
                                                                <option value="closed" {{ $job->status == 'closed' ? 'selected' : '' }}>Closed</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                        <button type="submit" class="btn btn-primary">Save Changes</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No jobs found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $jobs->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Initialize any JavaScript components here
    document.addEventListener('DOMContentLoaded', function() {
        // Add any initialization code here
    });
</script>
@endpush 