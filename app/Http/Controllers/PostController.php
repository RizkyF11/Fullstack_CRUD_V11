<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Controllers\Middleware\HashMiddleware;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Gate;


class PostController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum', except: ['index', 'show'])
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Post::with('user')->latest()->get(); 
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = $request->validate([
            'title' => 'required|max:255',
            'body' => 'required'
        ]);

        // $post = Post::create($validate);

        //Post with autorization
        $post = $request->user()->posts()->create($validate);

        return ['post' => $post,  'user' => $post->user];
    }

    /**
     * Display the specified resource.
     */
    public function show(post $post)
    {
        return ['post' => $post,  'user' => $post->user];
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, post $post)
    {
        Gate::authorize('modify', $post);

        
        $validate = $request->validate([
            'title' => 'required|max:255',
            'body' => 'required'
        ]);

        $post->update($validate);

        return ['post' => $post,  'user' => $post->user];
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(post $post)
    {
        Gate::authorize('modify', $post);

        $post->delete();

        return ['message' => 'The Post was Deleted'];
}
}
