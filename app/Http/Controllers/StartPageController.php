<?php

namespace App\Http\Controllers;

use App\Models\Link;
use App\Models\News;
use App\Models\Slider;
use Illuminate\Routing\Controller;


class StartPageController extends Controller
{
    public function main()
    {
        return response([
            'sliders' => Slider::orderBy('created_at', 'desc')->get(),
            'links' => Link::orderBy('created_at', 'desc')->get(),
            'news' => News::orderBy('created_at', 'desc')->get()
        ]);
    }


    public function oneNews($newsId)
    {
        return response(['news' => News::find($newsId)]);
    }
}
