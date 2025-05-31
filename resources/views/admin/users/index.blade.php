@extends('layouts.admin')

@section('title', 'Users Management')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Users Management</h1>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('admin.users') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="{{ request('search') }}" placeholder="Search by name or email">
                </div>
                <div class="col-md-3">
                    <label for="type" class="form-label">User Type</label>
                    <select class="form-select" id="type" name="type">
                        <option value="">All Types</option>
                        <option value="user" {{ request('type') == 'user' ? 'selected' : '' }}>User</option>
                        <option value="contractor" {{ request('type') == 'contractor' ? 'selected' : '' }}>Contractor</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="sort" class="form-label">Sort By</label>
                    <select class="form-select" id="sort" name="sort">
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest</option>
                        <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">Filter</button>
                    <a href="{{ route('admin.users') }}" class="btn btn-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Type</th>
                            <th>Jobs Posted</th>
                            <th>Applications</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <span class="badge bg-{{ $user->user_type == 'user' ? 'primary' : 'success' }}">
                                        {{ ucfirst($user->user_type) }}
                                    </span>
                                </td>
                                <td>{{ $user->jobs_count }}</td>
                                <td>{{ $user->applications_count }}</td>
                                <td>{{ $user->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-info" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#viewUserModal{{ $user->id }}">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-warning"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editUserModal{{ $user->id }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('admin.users.destroy', $user) }}" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete this user?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>

                                    <!-- View User Modal -->
                                    <div class="modal fade" id="viewUserModal{{ $user->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">User Details</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <strong>Name:</strong> {{ $user->name }}
                                                    </div>
                                                    <div class="mb-3">
                                                        <strong>Email:</strong> {{ $user->email }}
                                                    </div>
                                                    <div class="mb-3">
                                                        <strong>Type:</strong> {{ ucfirst($user->user_type) }}
                                                    </div>
                                                    <div class="mb-3">
                                                        <strong>Joined:</strong> {{ $user->created_at->format('M d, Y H:i') }}
                                                    </div>
                                                    <div class="mb-3">
                                                        <strong>Last Updated:</strong> {{ $user->updated_at->format('M d, Y H:i') }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Edit User Modal -->
                                    <div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit User</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form action="{{ route('admin.users.update', $user) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label for="name" class="form-label">Name</label>
                                                            <input type="text" class="form-control" id="name" name="name" 
                                                                   value="{{ $user->name }}" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="email" class="form-label">Email</label>
                                                            <input type="email" class="form-control" id="email" name="email" 
                                                                   value="{{ $user->email }}" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="user_type" class="form-label">User Type</label>
                                                            <select class="form-select" id="user_type" name="user_type" required>
                                                                <option value="user" {{ $user->user_type == 'user' ? 'selected' : '' }}>User</option>
                                                                <option value="contractor" {{ $user->user_type == 'contractor' ? 'selected' : '' }}>Contractor</option>
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
                                <td colspan="7" class="text-center">No users found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $users->links() }}
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