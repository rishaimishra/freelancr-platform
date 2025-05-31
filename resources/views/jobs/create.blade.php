@extends('layouts.master')

@section('title', 'Post a Job')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Post a New Job</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('jobs.store') }}" method="POST">
                        @csrf

                        <!-- Title -->
                        <div class="mb-3">
                            <label for="title" class="form-label">Job Title</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                id="title" name="title" value="{{ old('title') }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Job Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                id="description" name="description" rows="5" required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Budget -->
                      <!-- Budget -->
                        <div class="mb-3">
                            <label for="budget" class="form-label">Budget</label>
                            <select class="form-select @error('budget') is-invalid @enderror" 
                                id="budget" name="budget" required>
                                <option value="0" {{ old('budget') == 0 ? 'selected' : '' }}>-- choose --</option>
                                <option value="1" {{ old('budget') == 1 ? 'selected' : '' }}>Less than 5000 USD</option>
                                <option value="2" {{ old('budget') == 2 ? 'selected' : '' }}>Between 5000 and 15000 USD</option>
                                <option value="3" {{ old('budget') == 3 ? 'selected' : '' }}>More Than 15000 USD</option>
                            </select>
                            @error('budget')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Province -->
                        <div class="mb-3">
                            <label for="provincia_id" class="form-label">Province</label>
                            <select class="form-select @error('provincia_id') is-invalid @enderror" 
                                id="provincia_id" name="provincia_id" required>
                                <option value="">Select Province</option>
                                @foreach($provinces as $province)
                                    <option value="{{ $province->id }}" {{ old('provincia_id') == $province->id ? 'selected' : '' }}>
                                        {{ $province->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('provincia_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Company Types (Multi-select) -->
                        <div class="mb-3">
                            <label for="company_types" class="form-label">Company Types</label>
                            <select class="form-select @error('company_types') is-invalid @enderror" 
                                id="company_types" name="company_types[]" multiple="multiple" required>
                                @foreach($company_types as $type)
                                    <option value="{{ $type->id }}" 
                                        {{ in_array($type->id, old('company_types', [])) ? 'selected' : '' }}>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('company_types')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Post Job</button>
                            <a href="{{ route('jobs.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize Select2 for both selects
    $('#provincia_id, #company_types').select2({
        theme: 'bootstrap-5'
    });

    // For company types, you might want to add some configuration
    $('#company_types').select2({
        theme: 'bootstrap-5',
        placeholder: "Select company types",
        allowClear: true
    });

    console.log('Form initialized');
});
</script>
@endpush
@endsection