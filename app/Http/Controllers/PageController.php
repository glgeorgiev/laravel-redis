<?php

namespace App\Http\Controllers;

use Cache;
use View;

class PageController extends Controller
{
    public function index()
    {
        if (Cache::has('index_page')) {
            return Cache::get('index_page');
        }

        $view = View::make('welcome')->render();

        Cache::put('index_page', $view, 1);

        return $view;
    }
}
