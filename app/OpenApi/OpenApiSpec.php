<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'DonasiKita API',
    description: 'Official API documentation for DonasiKita.'
)]
#[OA\Server(url: '/', description: 'API server base URL')]
#[OA\SecurityScheme(
    securityScheme: 'sanctum',
    type: 'apiKey',
    in: 'header',
    name: 'Authorization',
    description: 'Use token format: Bearer {token}'
)]
#[OA\Tag(name: 'Auth', description: 'Authentication related endpoints')]
#[OA\Tag(name: 'Webhook', description: 'Payment webhook endpoints')]
class OpenApiSpec {}
