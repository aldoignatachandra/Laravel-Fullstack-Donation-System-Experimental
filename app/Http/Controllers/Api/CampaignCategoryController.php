<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CampaignCategory;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class CampaignCategoryController extends Controller
{
    #[OA\Get(
        path: '/api/categories',
        operationId: 'listCampaignCategories',
        summary: 'List all campaign categories',
        tags: ['Categories'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of campaign categories',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'id', type: 'integer', example: 1),
                            new OA\Property(property: 'name', type: 'string', example: 'Pendidikan'),
                            new OA\Property(property: 'description', type: 'string', nullable: true, example: 'Kampanye untuk pendidikan'),
                        ],
                        type: 'object'
                    )
                )
            ),
        ]
    )]
    public function index(): JsonResponse
    {
        $categories = CampaignCategory::withCount('campaigns')->get();

        return response()->json($categories);
    }

    #[OA\Get(
        path: '/api/categories/{id}',
        operationId: 'getCampaignCategory',
        summary: 'Get category detail with campaigns',
        tags: ['Categories'],
        parameters: [
            new OA\Parameter(name: 'id', description: 'Category ID', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Category detail with campaigns'),
            new OA\Response(response: 404, description: 'Category not found'),
        ]
    )]
    public function show(int $id): JsonResponse
    {
        $category = CampaignCategory::with(['campaigns' => function ($query) {
            $query->where('status', \App\Models\Campaign::STATUS_ACTIVE);
        }])->findOrFail($id);

        return response()->json($category);
    }
}
