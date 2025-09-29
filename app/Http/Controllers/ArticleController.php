<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema; // ⬅️
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    /** Accueil / Association : présentation + articles publiés */
    public function index()
    {
        $intro = null;

        // On ne tente la requête que si la table existe
        if (Schema::hasTable('pages')) {
            $query = Page::query()->where('slug', 'association-intro');
            // Ne filtre "is_published" que si la colonne existe
            if (Schema::hasColumn('pages', 'is_published')) {
                $query->where('is_published', true);
            }
            $intro = $query->first();
        }

        $articles = Article::query()
            ->with('media')
            ->where('is_published', true)
            ->orderByDesc('published_at')
            ->paginate(9);

        return view('articles.index', compact('articles', 'intro'));
    }

    public function show(string $slug)
    {
        $article = Article::with('media')
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        return view('articles.show', compact('article'));
    }
}
