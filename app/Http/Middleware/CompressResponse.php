<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CompressResponse
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, \Closure $next): Response
    {
        $response = $next($request);

        // Only compress HTML, CSS, JS, JSON responses
        $contentType = $response->headers->get('Content-Type', '');
        $compressibleTypes = [
            'text/html',
            'text/css',
            'application/javascript',
            'application/json',
            'text/javascript',
            'text/plain',
        ];

        $shouldCompress = false;
        foreach ($compressibleTypes as $type) {
            if (str_contains($contentType, $type)) {
                $shouldCompress = true;

                break;
            }
        }

        if ($shouldCompress && $this->supportsGzip($request)) {
            $content = $response->getContent();

            if (strlen($content) > 1024) { // Only compress if larger than 1KB
                $compressed = gzencode($content, 6); // Level 6 compression

                if (false !== $compressed) {
                    $response->setContent($compressed);
                    $response->headers->set('Content-Encoding', 'gzip');
                    $response->headers->set('Content-Length', strlen($compressed));
                }
            }
        }

        return $response;
    }

    private function supportsGzip(Request $request): bool
    {
        $acceptEncoding = $request->headers->get('Accept-Encoding', '');

        return str_contains($acceptEncoding, 'gzip');
    }
}
