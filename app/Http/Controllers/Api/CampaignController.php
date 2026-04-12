<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\CampaignArticle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class CampaignController extends Controller
{
    #[OA\Get(
        path: '/api/campaigns',
        operationId: 'listCampaigns',
        summary: 'List active campaigns with search and filters',
        description: 'Returns paginated list of active campaigns, optionally filtered by category and search query.',
        tags: ['Campaigns'],
        parameters: [
            new OA\Parameter(name: 'search', description: 'Search by campaign title', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'category_id', description: 'Filter by category ID', in: 'query', required: false, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'page', description: 'Page number', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 1)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Paginated list of campaigns',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', type: 'array',
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: 'id', type: 'integer', example: 1),
                                    new OA\Property(property: 'title', type: 'string', example: 'Bantu Sekolah Daerah Terpencil'),
                                    new OA\Property(property: 'slug', type: 'string', example: 'bantu-sekolah-daerah-terpencil'),
                                    new OA\Property(property: 'description', type: 'string'),
                                    new OA\Property(property: 'target_amount', type: 'number', example: 50000000),
                                    new OA\Property(property: 'total_donations', type: 'number', example: 25000000),
                                    new OA\Property(property: 'donation_count', type: 'integer', example: 45),
                                    new OA\Property(property: 'start_date', type: 'string', format: 'date-time'),
                                    new OA\Property(property: 'end_date', type: 'string', format: 'date-time'),
                                    new OA\Property(property: 'image', type: 'string', nullable: true),
                                    new OA\Property(property: 'is_featured', type: 'boolean', example: false),
                                ],
                                type: 'object'
                            )
                        ),
                        new OA\Property(property: 'current_page', type: 'integer'),
                        new OA\Property(property: 'last_page', type: 'integer'),
                        new OA\Property(property: 'total', type: 'integer'),
                    ],
                    type: 'object'
                )
            ),
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $query = Campaign::where('status', Campaign::STATUS_ACTIVE)
            ->with(['category', 'user']);

        if ($request->filled('search')) {
            $query->where('title', 'like', '%'.$request->input('search').'%');
        }

        if ($request->filled('category_id')) {
            $query->where('campaign_category_id', $request->input('category_id'));
        }

        $campaigns = $query->latest()->paginate(12);

        return response()->json($campaigns);
    }

    #[OA\Get(
        path: '/api/campaigns/featured',
        operationId: 'listFeaturedCampaigns',
        summary: 'List featured campaigns',
        tags: ['Campaigns'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of featured campaigns',
                content: new OA\JsonContent(type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'id', type: 'integer', example: 1),
                            new OA\Property(property: 'title', type: 'string', example: 'Featured Campaign'),
                            new OA\Property(property: 'slug', type: 'string'),
                            new OA\Property(property: 'target_amount', type: 'number'),
                            new OA\Property(property: 'total_donations', type: 'number'),
                            new OA\Property(property: 'image', type: 'string', nullable: true),
                        ],
                        type: 'object'
                    )
                )
            ),
        ]
    )]
    public function featured(): JsonResponse
    {
        $campaigns = Campaign::where('status', Campaign::STATUS_ACTIVE)
            ->where('is_featured', true)
            ->with(['category'])
            ->latest()
            ->get();

        return response()->json($campaigns);
    }

    #[OA\Get(
        path: '/api/campaigns/{slug}',
        operationId: 'getCampaignDetail',
        summary: 'Get campaign detail with donations and articles',
        tags: ['Campaigns'],
        parameters: [
            new OA\Parameter(name: 'slug', description: 'Campaign slug', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Campaign detail',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer'),
                        new OA\Property(property: 'title', type: 'string'),
                        new OA\Property(property: 'slug', type: 'string'),
                        new OA\Property(property: 'description', type: 'string'),
                        new OA\Property(property: 'target_amount', type: 'number'),
                        new OA\Property(property: 'total_donations', type: 'number'),
                        new OA\Property(property: 'donation_count', type: 'integer'),
                        new OA\Property(property: 'image', type: 'string', nullable: true),
                        new OA\Property(property: 'start_date', type: 'string', format: 'date-time'),
                        new OA\Property(property: 'end_date', type: 'string', format: 'date-time'),
                        new OA\Property(property: 'category', type: 'object'),
                        new OA\Property(property: 'user', type: 'object'),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(response: 404, description: 'Campaign not found'),
        ]
    )]
    public function show(string $slug): JsonResponse
    {
        $campaign = Campaign::where('slug', $slug)
            ->where('status', Campaign::STATUS_ACTIVE)
            ->with(['category', 'user'])
            ->withCount('donations')
            ->firstOrFail();

        $latestDonations = $campaign->donations()
            ->where('status', \App\Models\Donation::STATUS_PAID)
            ->with(['user'])
            ->latest()
            ->limit(10)
            ->get();

        $articles = $campaign->articles()
            ->where('status', CampaignArticle::STATUS_PUBLISHED)
            ->latest()
            ->limit(5)
            ->get();

        return response()->json([
            'campaign' => $campaign,
            'latest_donations' => $latestDonations,
            'articles' => $articles,
        ]);
    }

    #[OA\Get(
        path: '/api/campaigns/{slug}/articles',
        operationId: 'listCampaignArticles',
        summary: 'List published articles for a campaign',
        tags: ['Campaigns'],
        parameters: [
            new OA\Parameter(name: 'slug', description: 'Campaign slug', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'List of published articles'),
            new OA\Response(response: 404, description: 'Campaign not found'),
        ]
    )]
    public function articles(string $slug): JsonResponse
    {
        $campaign = Campaign::where('slug', $slug)->firstOrFail();

        $articles = $campaign->articles()
            ->where('status', CampaignArticle::STATUS_PUBLISHED)
            ->with(['author:id,name'])
            ->latest()
            ->paginate(10);

        return response()->json($articles);
    }
}
