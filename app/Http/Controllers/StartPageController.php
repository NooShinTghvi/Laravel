<?php

namespace App\Http\Controllers;

use App\Models\Link;
use App\Models\News;
use App\Models\Slider;
use Illuminate\Routing\Controller;


class StartPageController extends Controller
{
    public function sliders()
    {
        return Slider::orderBy('created_at', 'desc')->get();
    }

    public function allLinks()
    {
        return Link::orderBy('created_at', 'desc')->get();
    }

    public function allNews()
    {
        return News::orderBy('created_at', 'desc')->get();
    }

    public function oneNews($news_id)
    {
        return News::find($news_id);
    }
}
