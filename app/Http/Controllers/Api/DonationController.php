<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Donation;
use App\Services\DonationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;

class DonationController extends Controller
{
    public function __construct(
        private DonationService $donationService
    ) {}

    #[OA\Post(
        path: '/api/campaigns/{slug}/donations',
        operationId: 'createDonation',
        summary: 'Create a donation for a campaign',
        description: 'Validates the donation, creates a record, and returns a Midtrans Snap payment URL.',
        tags: ['Donations'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'slug', description: 'Campaign slug', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['amount'],
                properties: [
                    new OA\Property(property: 'amount', type: 'integer', description: 'Donation amount in IDR (min 10000, max 100000000)', example: 100000),
                    new OA\Property(property: 'is_anonymous', type: 'boolean', default: false, example: false),
                    new OA\Property(property: 'message', type: 'string', maxLength: 500, nullable: true, example: 'Semoga bermanfaat!'),
                ],
                type: 'object'
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Donation created, payment URL returned',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'snap_url', type: 'string', example: 'https://app.sandbox.midtrans.com/snap/v2/...'),
                        new OA\Property(property: 'donation_id', type: 'integer', example: 1),
                        new OA\Property(property: 'order_id', type: 'string', example: 'ORD-20260412-abc123'),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(response: 422, description: 'Validation error or business rule violation'),
            new OA\Response(response: 404, description: 'Campaign not found'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function store(Request $request, string $slug): JsonResponse
    {
        $campaign = Campaign::where('slug', $slug)
            ->where('status', Campaign::STATUS_ACTIVE)
            ->firstOrFail();

        $validated = $request->validate([
            'amount' => 'required|integer|min:10000|max:100000000',
            'is_anonymous' => 'boolean',
            'message' => 'nullable|string|max:500',
        ]);

        try {
            $result = $this->donationService->recordDonation($campaign, $validated);

            return response()->json($result, 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    #[OA\Get(
        path: '/api/donations',
        operationId: 'listUserDonations',
        summary: "List authenticated user's donations",
        tags: ['Donations'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'search', description: 'Search by campaign title', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'status', description: 'Filter by status (0=pending, 1=paid, 2=failed, 3=cancelled)', in: 'query', required: false, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'List of user donations'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $query = Donation::where('user_id', Auth::id())
            ->with(['campaign']);

        if ($request->filled('search')) {
            $query->whereHas('campaign', function ($q) use ($request) {
                $q->where('title', 'like', '%'.$request->input('search').'%');
            });
        }

        if ($request->filled('status') && is_numeric($request->input('status'))) {
            $query->where('status', $request->input('status'));
        }

        $donations = $query->latest()->paginate(10);

        return response()->json($donations);
    }

    #[OA\Get(
        path: '/api/donations/{id}',
        operationId: 'getDonationDetail',
        summary: 'Get donation detail',
        tags: ['Donations'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'id', description: 'Donation ID', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Donation detail with campaign info'),
            new OA\Response(response: 404, description: 'Donation not found'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function show(int $id): JsonResponse
    {
        $donation = Donation::where('user_id', Auth::id())
            ->with(['campaign'])
            ->findOrFail($id);

        return response()->json($donation);
    }

    #[OA\Get(
        path: '/api/donations/summary',
        operationId: 'getDonationSummary',
        summary: "Get authenticated user's donation summary",
        description: 'Returns total donation amount, total count, and recent donations.',
        tags: ['Donations'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Donation summary',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'total_amount', type: 'number', example: 1500000),
                        new OA\Property(property: 'total_count', type: 'integer', example: 12),
                        new OA\Property(property: 'recent_donations', type: 'array',
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: 'id', type: 'integer'),
                                    new OA\Property(property: 'amount', type: 'number'),
                                    new OA\Property(property: 'status', type: 'integer'),
                                    new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
                                    new OA\Property(property: 'campaign', properties: [
                                        new OA\Property(property: 'title', type: 'string'),
                                        new OA\Property(property: 'slug', type: 'string'),
                                    ], type: 'object'),
                                ],
                                type: 'object'
                            )
                        ),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function summary(): JsonResponse
    {
        $totalAmount = Donation::where('user_id', Auth::id())
            ->where('status', Donation::STATUS_PAID)
            ->sum('amount');

        $totalCount = Donation::where('user_id', Auth::id())
            ->where('status', Donation::STATUS_PAID)
            ->count();

        $recentDonations = Donation::where('user_id', Auth::id())
            ->where('status', Donation::STATUS_PAID)
            ->with(['campaign:id,title,slug'])
            ->latest()
            ->limit(5)
            ->get();

        return response()->json([
            'total_amount' => $totalAmount,
            'total_count' => $totalCount,
            'recent_donations' => $recentDonations,
        ]);
    }
}
