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
                        <div class="mb-3">
                            <label for="budget" class="form-label">Budget ($)</label>
                            <input type="number" class="form-control @error('budget') is-invalid @enderror" 
                                id="budget" name="budget" value="{{ old('budget') }}" min="0" step="0.01" required>
                            @error('budget')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Country -->
                        <div class="mb-3">
                            <label for="paises_id" class="form-label">Country</label>
                            <select class="form-select @error('paises_id') is-invalid @enderror" 
                                id="paises_id" name="paises_id" required>
                                <option value="">Select Country</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country->id }}" {{ old('paises_id') == $country->id ? 'selected' : '' }}>
                                        {{ $country->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('paises_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Province -->
                        <div class="mb-3">
                            <label for="provincia_id" class="form-label">Province</label>
                            <select class="form-select @error('provincia_id') is-invalid @enderror" 
                                id="provincia_id" name="provincia_id" required>
                                <option value="">Select Province</option>
                            </select>
                            @error('provincia_id')
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
    // Initialize Select2
    $('#paises_id, #provincia_id').select2({
        theme: 'bootstrap-5'
    });

    // Handle country change
    $('#paises_id').change(function() {
        const countryId = $(this).val();
        const provinceSelect = $('#provincia_id');
        
        provinceSelect.empty().append('<option value="">Select Province</option>');
        
        if (countryId) {
            $.get(`/provincias/${countryId}`, function(provinces) {
                provinces.forEach(province => {
                    provinceSelect.append(`<option value="${province.id}">${province.name}</option>`);
                });
            });
        }
    });

    // If there's a selected country on page load, load its provinces
    if ($('#paises_id').val()) {
        $('#paises_id').trigger('change');
    }
});
</script>
@endpush
@endsection 