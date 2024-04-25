<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;

class StripeController extends Controller
{
    public function stripePost(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'number' => 'required|numeric',
            'exp_month' => 'required|numeric',
            'exp_year' => 'required|numeric',
            'cvc' => 'required|numeric',
            'amount' => 'required|numeric',
        ]);

        try {

            $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));


            $token = $stripe->tokens->create([
                'card' => [
                    'number' => $validatedData['number'],
                    'exp_month' => $validatedData['exp_month'],
                    'exp_year' => $validatedData['exp_year'],
                    'cvc' => $validatedData['cvc'],
                ],
            ]);


            $charge = $stripe->charges->create([
                'amount' => $validatedData['amount'],
                'currency' => 'AED',
                'source' => 'tok_visa',
                'description' => $validatedData['description'],
            ]);


            return response()->json(['message' => 'Payment successful!', 'status' => true]);
        } catch (\Exception $e) {

            return response()->json(['message' => 'Payment failed: ' . $e->getMessage(), 'status' => false]);
        }
    }

}
