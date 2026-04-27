<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\PageModel;

class PageController extends Controller
{
    public function show(string $slug = ''): void
    {
        $page = (new PageModel())->findBySlug($slug);

        if (!$page || $page['status'] !== 'published') {
            $this->abort(404);
        }

        $this->view('public/page/show', [
            'page'  => $page,
            'title' => $page['meta_title'] ?: $page['title'],
        ]);
    }
}
