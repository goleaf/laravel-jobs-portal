<?php

namespace App\Http\Resources\Universal;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * Universal Company Collection
 * Implements MCP best practices for collection responses.
 */
class CompanyCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
            'links' => [
                'self' => $request->url(),
            ],
        ];
    }

    /**
     * Universal Pattern: Add collection metadata.
     */
    public function with(Request $request): array
    {
        return [
            'meta' => [
                'count' => $this->collection->count(),
                'timestamp' => now()->toISOString(),
                'version' => config('app.version', '1.0.0'),
                'resource_type' => 'company_collection',
            ],
        ];
    }

    /**
     * Universal Pattern: Customize pagination information.
     *
     * @param mixed $request
     * @param mixed $paginated
     * @param mixed $default
     */
    public function paginationInformation($request, $paginated, $default)
    {
        $default['meta']['total_pages'] = $paginated['last_page'];
        $default['meta']['current_page'] = $paginated['current_page'];
        $default['meta']['per_page'] = $paginated['per_page'];

        return $default;
    }
}
