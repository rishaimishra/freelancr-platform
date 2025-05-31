@extends('layouts.master')

@section('title', 'Client Registration')

@section('content')
<div class="form-container">
    <h2 class="text-center mb-4">Client Registration</h2>

    <form method="POST" action="{{ route('register.client') }}" enctype="multipart/form-data">
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
                <input type="tel" name="mobile" id="mobile" class="form-control" placeholder="Enter your mobile number" value="{{ old('mobile') }}">
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

        <div class="mb-3">
            <label for="provincia_id" class="form-label">Geographical Area</label>
            <select name="provincia_id" id="provincia_id" class="form-select" required>
                <option value="">Select Province</option>
                @if(old('provincia_id'))
                    @foreach($provincias as $provincia)
                        <option value="{{ $provincia->id }}" {{ old('provincia_id') == $provincia->id ? 'selected' : '' }}>
                            {{ $provincia->nombre }}
                        </option>
                    @endforeach
                @endif
            </select>
        </div>

        <div class="mb-3">
            <label for="profile_picture" class="form-label">Profile Picture</label>
            <input type="file" name="profile_picture" id="profile_picture" class="form-control" accept="image/*">
        </div>

        <button type="submit" class="btn btn-primary w-100">Register as Client</button>
    </form>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Initialize Select2 for single select
        $('#provincia_id').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Select province',
            allowClear: true
        });

        // Handle country change
        $('#paises_id').on('change', function() {
            var paisId = $(this).val();
            $('#provincia_id').empty().append('<option value="">Select Province</option>');
            
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
