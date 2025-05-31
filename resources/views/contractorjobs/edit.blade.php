@extends('layouts.master')

@section('title', 'View Job')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">View Job</h4>
                    </div>
                    <div class="card-body">
                        <form>
                            <!-- Title -->
                            <div class="mb-3">
                                <label for="title" class="form-label">Job Title</label>
                                <input type="text" class="form-control"
                                    id="title" name="title" value="{{ old('title', $job->title) }}" disabled>
                            </div>

                            <!-- Description -->
                            <div class="mb-3">
                                <label for="description" class="form-label">Job Description</label>
                                <textarea class="form-control" id="description" name="description"
                                    rows="5" disabled>{{ old('description', $job->description) }}</textarea>
                            </div>

                            <!-- Budget -->
                            <div class="mb-3">
                                <label for="budget" class="form-label">Budget</label>
                                <select class="form-select" id="budget" name="budget" disabled>
                                    <option value="0" {{ old('budget', $job->budget) == 0 ? 'selected' : '' }}>-- choose --</option>
                                    <option value="1" {{ old('budget', $job->budget) == 1 ? 'selected' : '' }}>Less than 5000 USD</option>
                                    <option value="2" {{ old('budget', $job->budget) == 2 ? 'selected' : '' }}>Between 5000 and 15000 USD</option>
                                    <option value="3" {{ old('budget', $job->budget) == 3 ? 'selected' : '' }}>More Than 15000 USD</option>
                                </select>
                            </div>

                            <!-- Province -->
                            <div class="mb-3">
                                <label for="provincia_id" class="form-label">Province</label>
                                <select class="form-select" id="provincia_id"
                                    name="provincia_id" disabled>
                                    <option value="">Select Province</option>
                                    @foreach ($provinces as $province)
                                        <option value="{{ $province->id }}"
                                            {{ old('provincia_id', $job->provincia_id) == $province->id ? 'selected' : '' }}>
                                            {{ $province->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Company Types (Multi-select) -->
                            <div class="mb-3">
                                <label for="company_types" class="form-label">Company Types</label>
                                <select class="form-select" id="company_types"
                                    name="company_types[]" multiple="multiple" disabled>
                                    @foreach ($company_types as $type)
                                        <option value="{{ $type->id }}"
                                            {{ in_array($type->id, old('company_types', $job->company_types ?? [])) ? 'selected' : '' }}>
                                            {{ $type->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                             <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Apply Job</button>
                                <a href="{{ route('jobs.apply',$job->id) }}" class="btn btn-outline-secondary">Cancel</a>
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
                // Initialize Select2 for provinces and disable
                $('#provincia_id').select2({
                    theme: 'bootstrap-5',
                    placeholder: "Select province"
                }).prop("disabled", true);

                // Initialize Select2 for company types and disable
                $('#company_types').select2({
                    theme: 'bootstrap-5',
                    placeholder: "Select company types",
                    allowClear: true
                }).prop("disabled", true);
            });
        </script>
    @endpush
@endsection
