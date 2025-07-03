<?php

namespace App\Http\Csp;

use Spatie\Csp\Directive;
use Spatie\Csp\Keyword;
use Spatie\Csp\Policy;
use Spatie\Csp\Preset;

class CustomCspPreset implements Preset
{
    public function configure(Policy $policy): void
    {
        $policy
            // Base directive - only allow same origin by default
            ->add(Directive::BASE, Keyword::SELF)

            // Default source - restrictive base policy
            ->add(Directive::DEFAULT, Keyword::SELF)

            // Script sources - allow self and domain
            ->add(Directive::SCRIPT, [
                Keyword::SELF,
                'https://jobportal.prus.dev',
                'localhost:*',
                'http://localhost:*',
                'https://localhost:*',
                '127.0.0.1:*',
                'http://127.0.0.1:*',
                'https://127.0.0.1:*',
            ])
            ->addNonce(Directive::SCRIPT)

            // Style sources - allow self, inline styles (no external CDNs)
            ->add(Directive::STYLE, [
                Keyword::SELF,
                Keyword::UNSAFE_INLINE, // Needed for TailwindCSS and inline styles
                'https://jobportal.prus.dev',
                'localhost:*',
                'http://localhost:*',
                'https://localhost:*',
                '127.0.0.1:*',
                'http://127.0.0.1:*',
                'https://127.0.0.1:*',
            ])
            ->addNonce(Directive::STYLE)

            // Font sources - only allow self and data URLs (no external CDNs)
            ->add(Directive::FONT, [
                Keyword::SELF,
                'data:',
                'https://jobportal.prus.dev',
            ])

            // Image sources - allow self, data URLs, and HTTPS
            ->add(Directive::IMG, [
                Keyword::SELF,
                'data:',
                'blob:',
                'https:',
                'https://jobportal.prus.dev',
            ])

            // Connect sources - allow self and API endpoints
            ->add(Directive::CONNECT, [
                Keyword::SELF,
                'https://jobportal.prus.dev',
                'localhost:*',
                'http://localhost:*',
                'https://localhost:*',
                '127.0.0.1:*',
                'http://127.0.0.1:*',
                'https://127.0.0.1:*',
                'ws://localhost:*',
                'wss://localhost:*',
                'ws://127.0.0.1:*',
                'wss://127.0.0.1:*',
                'wss://jobportal.prus.dev',
                'https://api.jobportal.prus.dev',
            ])

            // Media sources - only allow self
            ->add(Directive::MEDIA, [
                Keyword::SELF,
                'https://jobportal.prus.dev',
            ])

            // Object sources - block all
            ->add(Directive::OBJECT, Keyword::NONE)

            // Frame sources - block all
            ->add(Directive::FRAME, Keyword::NONE)

            // Form actions - only allow self
            ->add(Directive::FORM_ACTION, [
                Keyword::SELF,
                'https://jobportal.prus.dev',
            ])

            // Frame ancestors - prevent embedding
            ->add(Directive::FRAME_ANCESTORS, Keyword::NONE);
    }
}
