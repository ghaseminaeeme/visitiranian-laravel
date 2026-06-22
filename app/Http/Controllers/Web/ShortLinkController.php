<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\ShortLink\ShortLinkService;
use Illuminate\Http\RedirectResponse;

final class ShortLinkController extends Controller
{
    public function __construct(
        private readonly ShortLinkService $shortLinks,
    ) {}

    public function redirect(string $code): RedirectResponse
    {
        $link = $this->shortLinks->resolve($code);

        if ($link === null) {
            abort(404);
        }

        $this->shortLinks->recordClick($link);

        return redirect()->away($link->target_url);
    }
}
