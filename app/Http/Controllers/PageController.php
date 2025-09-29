<?php
// app/Http/Controllers/PageController.php
namespace App\Http\Controllers;

use App\Models\Page;

class PageController extends Controller
{
    public function show(string $slug)
    {
        $page = Page::where('slug', $slug)->firstOrFail();
        return view('pages.dynamic', compact('page'));
    }
}
