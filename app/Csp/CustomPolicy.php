<?php

namespace App\Csp;

use Spatie\Csp\Directive;
use Spatie\Csp\Policies\Policy;

class CustomPolicy extends Policy
{
    public function configure()
    {
        $this
            ->addDirective(Directive::BASE, 'self')
            ->addDirective(Directive::CONNECT, 'self')
            ->addDirective(Directive::DEFAULT, 'self')
            ->addDirective(Directive::FONT, 'self')
            ->addDirective(Directive::FRAME, 'self')
            ->addDirective(Directive::IMG, 'self')
            ->addDirective(Directive::MEDIA, 'self')
            ->addDirective(Directive::OBJECT, 'self')
            ->addDirective(Directive::SCRIPT, 'self')
            ->addDirective(Directive::STYLE, 'self')
            ->addDirective(Directive::UPGRADE_INSECURE_REQUESTS, true)
            ->addDirective(Directive::FORM_ACTION, 'self')
            ->addDirective(Directive::FRAME_ANCESTORS, 'self')
            ->addDirective(Directive::REPORT_URI, 'https://example.com/csp-report-endpoint');
    }
}
