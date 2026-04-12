<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'DonasiKita API',
    description: 'Official API documentation for DonasiKita — a donation and crowdfunding platform for the Indonesian community.'
)]
#[OA\Server(url: '/', description: 'API server base URL')]
#[OA\SecurityScheme(
    securityScheme: 'sanctum',
    type: 'apiKey',
    in: 'header',
    name: 'Authorization',
    description: 'Use token format: Bearer {token}'
)]
#[OA\Tag(name: 'Auth', description: 'Authentication and profile management')]
#[OA\Tag(name: 'Campaigns', description: 'Campaign listing, details, and articles')]
#[OA\Tag(name: 'Categories', description: 'Campaign categories')]
#[OA\Tag(name: 'Donations', description: 'Donation creation, history, and summary')]
#[OA\Tag(name: 'Webhook', description: 'Payment webhook endpoints')]
class OpenApiSpec {}
