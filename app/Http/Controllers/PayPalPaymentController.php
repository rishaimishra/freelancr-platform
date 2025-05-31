<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PayPalPaymentController extends Controller
{
    public function handlePayment(Payment $payment)
    {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $paypalToken = $provider->getAccessToken();

        $response = $provider->createOrder([
            "intent" => "CAPTURE",
            "application_context" => [
                "return_url" => route('payment.success', $payment),
                "cancel_url" => route('payment.cancel', $payment),
            ],
            "purchase_units" => [
                0 => [
                    "amount" => [
                        "currency_code" => "USD",
                        "value" => $payment->amount
                    ],
                    "description" => "Job Application Fee for Job #".$payment->job_id
                ]
            ]
        ]);

        if (isset($response['id'])) {
            foreach ($response['links'] as $link) {
                if ($link['rel'] === 'approve') {
                    return redirect()->away($link['href']);
                }
            }
        }

        return redirect()->route('payment.cancel', $payment)
            ->with('error', 'Something went wrong with PayPal.');
    }

    public function paymentSuccess(Payment $payment, Request $request)
    {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $paypalToken = $provider->getAccessToken();

        $response = $provider->capturePaymentOrder($request->token);

        if (isset($response['status']) && $response['status'] == 'COMPLETED') {
            // Update payment record
            $payment->update([
                'status' => 'completed',
                'transaction_id' => $response['id'],
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

        return redirect()->route('payment.cancel', $payment)
            ->with('error', 'Payment failed or was cancelled.');
    }

    public function paymentCancel(Payment $payment)
    {
        $payment->update(['status' => 'cancelled']);
        return redirect()->route('jobs.show', $payment->job_id)
            ->with('error', 'Payment was cancelled.');
    }

    public function downloadInvoice(Payment $payment)
    {
        if ($payment->user_id !== auth()->id()) {
            abort(403);
        }

        $pdf = PDF::loadView('invoices.job_application', [
            'payment' => $payment,
            'user' => auth()->user(),
            'job' => $payment->job,
        ]);

        return $pdf->download('invoice-'.$payment->id.'.pdf');
    }
}