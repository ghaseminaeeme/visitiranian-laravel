<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Services\Seo\SeoBuilder;
use Illuminate\Contracts\View\View;

final class PageController extends Controller
{
    public function __construct(
        private readonly SeoBuilder $seo,
    ) {}

    public function show(Page $page): View
    {
        abort_unless($page->is_published, 404);

        return view('pages.show', [
            'seo' => $this->seo->forPage($page),
            'page' => $page,
        ]);
    }
}
