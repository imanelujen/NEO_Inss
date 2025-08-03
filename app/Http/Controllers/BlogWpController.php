<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class BlogWpController extends Controller
{
    public function index()
    {
        $response = Http::get('http://localhost/wordpress/wp-json/wp/v2/posts');
        $posts = $response->json();

        return view('blog.index', compact('posts'));
    }
}
