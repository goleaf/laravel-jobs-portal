<?php

namespace App\Http\Resources\Universal;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class TokenCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'tokens' => $this->collection->map(function ($token) {
                return [
                    'id' => $token['id'],
                    'name' => $token['name'],
                    'abilities' => $token['abilities'],
                    'last_used_at' => $token['last_used_at'],
                    'created_at' => $token['created_at'],
                    'is_current' => $this->isCurrentToken($token['id']),
                ];
            }),
            'summary' => [
                'total_tokens' => $this->collection->count(),
                'active_tokens' => $this->collection->filter(function ($token) {
                    return $token['last_used_at'] !== null;
                })->count(),
                'inactive_tokens' => $this->collection->filter(function ($token) {
                    return $token['last_used_at'] === null;
                })->count(),
            ],
        ];
    }

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @return array<string, mixed>
     */
    public function with(Request $request): array
    {
        return [
            'success' => true,
            'message' => __('auth.tokens_retrieved'),
            'meta' => [
                'timestamp' => now()->toISOString(),
                'version' => '1.0',
                'endpoint' => 'auth/tokens',
                'pagination' => [
                    'count' => $this->collection->count(),
                    'limit' => $request->input('limit', 20),
                ],
            ],
        ];
    }

    /**
     * Customize the response for the resource.
     *
     * @param  mixed  $response
     */
    public function withResponse(Request $request, $response): void
    {
        $response->setStatusCode(200);
    }

    /**
     * Check if the token is the current token.
     *
     * @param  mixed  $tokenId
     */
    private function isCurrentToken($tokenId): bool
    {
        $currentToken = request()->user()?->currentAccessToken();

        return $currentToken && $currentToken->id === $tokenId;
    }
}
