<?php

namespace App\Http\Controllers;

use App\Services\DonationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MidtransController extends Controller
{
    public function callback(Request $request)
    {
        try {
            Log::info('Midtrans Callback received:', $request->all());

            $donationService = app(DonationService::class);
            $donation = $donationService->handleCallback($request->all());

            if ($donation) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Callback processed successfully',
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Donation not found',
            ], 404);

        } catch (\Exception $e) {
            Log::error('Midtrans Callback error:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'payload' => $request->all(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Callback processing failed',
            ], 500);
        }
    }
}
