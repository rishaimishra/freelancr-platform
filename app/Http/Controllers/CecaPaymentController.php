<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CecaPaymentController extends Controller
{
    public function showPaymentForm(Job $job)
    {
        // Check if user is a contractor
        if (auth()->user()->user_type !== 'contractor') {
            return redirect()->back()->with('error', 'Only contractors can apply for jobs.');
        }

        // Check if job is available
        if ($job->status !== 'hired') {
            return redirect()->back()->with('error', 'This job is no longer available.');
        }



        // Create a payment record
        $payment = Payment::create([
            'user_id' => auth()->id(),
            'job_id' => $job->id,
            'amount' => 50.00, // Set your application fee amount
            'payment_method' => 'credit_card',
            'status' => 'pending',
            'order_id' => 12, // Generate unique order ID
        ]);

        return view('payments.ceca', [
            'payment' => $payment,
            'job' => $job,
        ]);
    }

    public function handleSuccess(Request $request, $paymentId)
    {
        $payment = Payment::findOrFail($paymentId);

        // Verify the payment with CECA (you'll need to implement this based on CECA's API)
        $isValid = $this->verifyCecaPayment($request->all());

        if ($isValid) {
            // Update payment record
            $payment->update([
                'status' => 'completed',
                'transaction_id' => $request->input('Num_operacion'), // Or other CECA reference
                'paid_at' => now(),
            ]);

            // Create the job application
            Application::create([
                'user_id' => auth()->id(),
                'job_id' => $payment->job_id,
                'payment_id' => $payment->id,
                'status' => 'submitted',
            ]);

            return redirect()->route('jobs.show', $payment->job_id)
                ->with('success', 'Payment successful! Your application has been submitted.');
        }

        return redirect()->route('payment.failed', $payment->id);
    }

    public function handleFailure($paymentId)
    {
        $payment = Payment::findOrFail($paymentId);
        $payment->update(['status' => 'failed']);

        return redirect()->route('jobs.show', $payment->job_id)
            ->with('error', 'Payment failed. Please try again.');
    }

    private function verifyCecaPayment($responseData)
    {
        // Implement verification logic based on CECA's documentation
        // This typically involves verifying the signature/hash

        // Example (pseudo-code):
        $expectedSignature = hash_hmac(
            'sha256',
            $responseData['MerchantID'] .
            $responseData['AcquirerBIN'] .
            $responseData['TerminalID'] .
            $responseData['Num_operacion'] .
            $responseData['Importe'] .
            $responseData['TipoMoneda'] .
            $responseData['Exponente'] .
            $responseData['Cifrado'],
            config('services.ceca.key_encryption')
        );

        return hash_equals($expectedSignature, $responseData['Firma']);
    }

    public function generateHash(Request $request)
    {
        $abc = $request->all();
        \Log::info($abc);

        $encryptin = '1WPALZKM';
        $merchant_id = $request->input('merchant_id');
        $AcquirerBIN = $request->input('AcquirerBIN');
        $TerminalID = $request->input('TerminalID');
        $Num_operacion = $request->input('Num_operacion');
        $Importe = $request->input('Importe');
        $TipoMoneda = $request->input('TipoMoneda');
        $Exponente = $request->input('Exponente');
        $Cifrado = $request->input('Cifrado');
        $URL_OK = $request->input('URL_OK');
        $URL_NOK = $request->input('URL_NOK');

        $password = $encryptin . $merchant_id . $AcquirerBIN . $TerminalID . $Num_operacion . $Importe . $TipoMoneda . $Exponente . $Cifrado . $URL_OK . $URL_NOK;
        \Log::info("Hash Input: $password");

        $hash = hash('sha256', $password);
        \Log::info("Generated Hash: $hash");

        return response()->json(['hash' => $hash]);
    }
}