<?php

namespace App\Http\Controllers;

use App\Article;
use Cache;
use Redis;
use View;

class PageController extends Controller
{
    public function index()
    {
        $articles = Article::latest(10)->get();

        return view('welcome', compact('articles'));
    }

    public function indexWithArticlesCache()
    {
        if (Cache::has('articles')) {
            $articles = Cache::get('articles');
        } else {
            $articles = Article::latest(10)->get();

            Cache::put('articles', $articles, 5);
        }

        return view('welcome', compact('articles'));
    }

    public function indexWithViewCache()
    {
        if (Cache::has('view:welcome')) {
            return Cache::get('view:welcome');
        }

        $articles = Article::latest(10)->get();

        $view = View::make('welcome', compact('articles'))->render();

        Cache::put('view:welcome', $view, 1);

        return $view;
    }

    public function indexWithRawRedisCode()
    {
        $now = date('Y-m-d H:i:s');

        Redis::rPush('tmp:article', serialize([
            'title' => 'Test Title ' . $now,
            'text' => 'Test Text' . $now,
            'published_at' => $now,
        ]));

        for ($i = 0; $i < Redis::lLen('tmp:article'); $i++) {
            dump(unserialize(Redis::lIndex('tmp:article', $i)));
        }
        for ($i = 0; $i < Redis::lLen('tmp:article'); $i++) {
            Article::create(unserialize(Redis::lPop('tmp:article')));
        }

        Redis::hMset('app', [
            'url' => 'http://redis.maniaci.net',
            'name' => 'Redis - Laravel\' best friend',
        ]);

        dump(Redis::hGet('app', 'url'));
        dump(Redis::hGet('app', 'name'));
    }
}
