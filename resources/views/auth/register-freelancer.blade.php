@extends('layouts.master')

@section('title', 'Freelancer Registration')

@section('content')
<div class="form-container">
    <h2 class="text-center mb-4">Freelancer Registration</h2>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('register.freelancer') }}" enctype="multipart/form-data">
        @csrf

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="name" class="form-label">Full Name</label>
                <input type="text" name="name" id="name" class="form-control" placeholder="Enter your full name" required value="{{ old('name') }}">
            </div>

            <div class="col-md-6 mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="Enter your email" required value="{{ old('email') }}">
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="Create a password" required>
            </div>

            <div class="col-md-6 mb-3">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Confirm your password" required>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="mobile" class="form-label">Mobile</label>
                <input type="tel" name="mobile" id="mobile" class="form-control" placeholder="Enter your mobile number" required value="{{ old('mobile') }}">
            </div>

            <div class="col-md-6 mb-3">
                <label for="paises_id" class="form-label">Country</label>
                <select name="paises_id" id="paises_id" class="form-select" required>
                    <option value="">Select Country</option>
                    @foreach($paises as $pais)
                        <option value="{{ $pais->id }}" {{ old('paises_id') == $pais->id ? 'selected' : '' }}>
                            {{ $pais->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label for="bio" class="form-label">Address</label>
            <textarea name="bio" id="bio" class="form-control" rows="3" placeholder="Enter your address">{{ old('bio') }}</textarea>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="provincia_id" class="form-label">Geographical Areas</label>
                <select name="provincia_id[]" id="provincia_id" class="form-select" multiple="multiple" required>
                    @if(old('provincia_id'))
                        @foreach($provincias as $provincia)
                            <option value="{{ $provincia->id }}" {{ in_array($provincia->id, old('provincia_id')) ? 'selected' : '' }}>
                                {{ $provincia->nombre }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>

            <div class="col-md-6 mb-3">
                <label for="company_types_id" class="form-label">Company Types</label>
                <select name="company_types_id[]" id="company_types_id" class="form-select" multiple="multiple" required>
                    @foreach($company_types as $type)
                        <option value="{{ $type->id }}" {{ old('company_types_id') && in_array($type->id, old('company_types_id')) ? 'selected' : '' }}>
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label for="profile_picture" class="form-label">Profile Picture</label>
            <input type="file" name="profile_picture" id="profile_picture" class="form-control" accept="image/*">
        </div>

        <button type="submit" class="btn btn-primary w-100">Register as Freelancer</button>
    </form>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Initialize Select2 for multiselect
        $('#provincia_id, #company_types_id').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Select options',
            allowClear: true
        });

        // Handle country change
        $('#paises_id').on('change', function() {
            var paisId = $(this).val();
            $('#provincia_id').empty();
            
            if (paisId) {
                $.ajax({
                    url: '/provincias/' + paisId,
                    type: 'GET',
                    success: function(data) {
                        $.each(data, function(key, provincia) {
                            $('#provincia_id').append(new Option(provincia.nombre, provincia.id));
                        });
                        $('#provincia_id').trigger('change');
                    },
                    error: function() {
                        console.error('Error loading provinces');
                    }
                });
            }
        });
    });
</script>
@endsection
