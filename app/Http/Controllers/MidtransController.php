<?php

namespace App\Http\Controllers;

use App\Services\DonationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use OpenApi\Attributes as OA;
use Throwable;

class MidtransController extends Controller
{
    public function __construct(private DonationService $donationService) {}

    #[OA\Post(
        path: '/api/webhook/midtrans',
        operationId: 'handleMidtransWebhookCallback',
        summary: 'Handle Midtrans payment webhook callback',
        description: 'Processes Midtrans callback payload and updates donation status accordingly.',
        tags: ['Webhook'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['order_id', 'transaction_status'],
                properties: [
                    new OA\Property(property: 'order_id', type: 'string', example: 'ORD-TEST-123'),
                    new OA\Property(property: 'transaction_status', type: 'string', example: 'settlement'),
                    new OA\Property(property: 'payment_type', type: 'string', nullable: true, example: 'bank_transfer'),
                    new OA\Property(property: 'fraud_status', type: 'string', nullable: true, example: 'accept'),
                ],
                type: 'object'
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Callback processed successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(property: 'message', type: 'string', example: 'Callback processed successfully'),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Donation not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'error'),
                        new OA\Property(property: 'message', type: 'string', example: 'Donation not found'),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: 500,
                description: 'Callback processing failed',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'error'),
                        new OA\Property(property: 'message', type: 'string', example: 'Callback processing failed'),
                    ],
                    type: 'object'
                )
            ),
        ]
    )]
    public function callback(Request $request): JsonResponse
    {
        try {
            Log::info('Midtrans Callback received:', $request->all());

            $donation = $this->donationService->handleCallback($request->all());

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
        } catch (Throwable $e) {
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
