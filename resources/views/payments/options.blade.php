@extends('layouts.master')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Select Payment Method</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="fab fa-cc-paypal fa-4x mb-3"></i>
                                    <h5>Pay with PayPal</h5>
                                    <a href="{{ route('payment.paypal', $payment) }}" 
                                       class="btn btn-primary">PayPal</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-credit-card fa-4x mb-3"></i>
                                    <h5>Pay with Credit Card</h5>
                                    <a href="{{ route('payment.ceca.form', $job) }}" 
                                       class="btn btn-primary">Credit Card</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection