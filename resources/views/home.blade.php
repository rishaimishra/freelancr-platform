@extends('layouts.master')

@section('title', 'Welcome to Freelance Platform')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <h1 class="display-4 mb-4">Welcome to Freelance Platform</h1>
            <p class="lead mb-4">Connect with top freelancers and clients for your projects.</p>
            
            <div class="row mt-5">
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h3>For Freelancers</h3>
                            <p>Find the best projects and showcase your skills.</p>
                            <a href="{{ route('register.freelancer') }}" class="btn btn-primary">Register as Freelancer</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h3>For Clients</h3>
                            <p>Post projects and find the perfect freelancer.</p>
                            <a href="{{ route('register.client') }}" class="btn btn-primary">Register as Client</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 