@extends('layouts.master')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Credit Card Payment</div>

                <div class="card-body">
                    <form id="form-{{ $payment->id }}" 
                          action="https://pgw.ceca.es/tpvweb/tpv/compra.action" 
                          method="POST" 
                          enctype="application/x-www-form-urlencoded">
                        
                        @csrf

                        <h3>Job Application: {{ $job->title }}</h3>
                        <p>Application Fee: €{{ number_format($payment->amount, 2) }}</p>

                        <input type="hidden" name="Key_encryption" value="{{ config('services.ceca.key_encryption') }}">
                        <input type="hidden" name="MerchantID" value="{{ config('services.ceca.merchant_id') }}">
                        <input type="hidden" name="AcquirerBIN" value="{{ config('services.ceca.acquirer_bin') }}">
                        <input type="hidden" name="TerminalID" value="{{ config('services.ceca.terminal_id') }}">

                        <input type="hidden" name="URL_OK" value="{{ route('payment.ceca.success', $payment) }}">
                        <input type="hidden" name="URL_NOK" value="{{ route('payment.ceca.failed', $payment) }}">

                        <input type="hidden" name="Firma" id="Firma-{{ $payment->id }}" value="">
                        <input type="hidden" name="Cifrado" value="SHA2">
                        <input type="hidden" name="Num_operacion" value="{{ $payment->order_id }}">
                        <input type="hidden" name="Importe" value="{{ round($payment->amount * 100) }}">
                        <input type="hidden" name="TipoMoneda" value="978">
                        <input type="hidden" name="Exponente" value="2">
                        <input type="hidden" name="Pago_soportado" value="SSL">
                        <input type="hidden" name="Idioma" value="6">

                        <div class="form-group">
                            <label>Amount (EUR):</label>
                            <input type="text" class="form-control" 
                                   value="€{{ number_format($payment->amount, 2) }}" readonly>
                        </div>

                        <p class="text-danger mt-3"><b>Note:</b> You will be redirected to CECA's secure payment page.</p>

                        <div class="text-center">
                            <button type="button" class="btn btn-primary btn-generate-hash" 
                                    data-id="{{ $payment->id }}"
                                    data-key-encryption="{{ config('services.ceca.key_encryption') }}">
                                Proceed to Payment
                            </button>
                            <a href="{{ route('jobs.show', $job) }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include jQuery if not already included -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function () {
    $(".btn-generate-hash").on("click", function (e) {
        e.preventDefault();

        var $button = $(this);
        var id = $button.data("id");
        var keyEncryption = $button.data("key-encryption");
        var $form = $("#form-" + id);

        var formData = {
            merchant_id: $("input[name='MerchantID']", $form).val(),
            AcquirerBIN: $("input[name='AcquirerBIN']", $form).val(),
            TerminalID: $("input[name='TerminalID']", $form).val(),
            Num_operacion: $("input[name='Num_operacion']", $form).val(),
            Importe: $("input[name='Importe']", $form).val(),
            TipoMoneda: $("input[name='TipoMoneda']", $form).val(),
            Exponente: $("input[name='Exponente']", $form).val(),
            Cifrado: $("input[name='Cifrado']", $form).val(),
            URL_OK: $("input[name='URL_OK']", $form).val(),
            URL_NOK: $("input[name='URL_NOK']", $form).val(),
            Key_encryption: keyEncryption,
            _token: $('meta[name="csrf-token"]').attr("content")
        };

        $button.prop("disabled", true).html('<span class="spinner-border spinner-border-sm"></span> Processing...');

        $.ajax({
            type: "POST",
            url: "{{ route('generate.hash') }}",
            data: formData,
            dataType: "json",
            success: function (response) {
                if (response.hash) {
                    $("#Firma-" + id).val(response.hash);
                    $form.submit();
                } else {
                    alert("Error: Hash not received");
                    $button.prop("disabled", false).text("Proceed to Payment");
                }
            },
            error: function (xhr) {
                console.error("AJAX Error:", xhr.responseText);
                alert("Error generating hash");
                $button.prop("disabled", false).text("Proceed to Payment");
            }
        });
    });
});
</script>
@endsection
