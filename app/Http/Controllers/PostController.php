<?php

namespace App\Http\Controllers;

use App\Post;
use App\PostContent;
use Illuminate\Http\Request;
use Exception;

class PostController extends Controller
{
    private $postsPerPage = 10;
    private $minPostLength = 50;

    private function validationRules() {
        $types = implode(',', Post::$validTypes);
        return [
            'title' => 'required|string|max:255',
            'source' => "required|min:{$this->minPostLength}",
            'type' => "required|in:{$types}",
            'tags' => 'nullable'
        ];
    }

    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::latest()->paginate($this->postsPerPage);
        return view('posts.index', compact('posts'))
            ->with('i', (request()->input('page', 1) - 1) * $this->postsPerPage);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('posts.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $source = request('source');
        $attributes = $request->validate($this->validationRules());
        $attributes['description'] = $source;
        $attributes['hash'] = md5($source);
        $attributes['user_ip'] = $request->getClientIp();
        $post = Post::create($attributes);
        switch ($post->type) {
            case 'content':
                PostContent::create([
                    'post_id' => $post->id,
                    'source' => $source,
                    'text' => $source,
                ]);
                break;
            default:
                throwException(new Exception('Unknown post type'));
        }
        return redirect()->route('posts.show', $post->id)
            ->with('status', 'Post created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        return view('posts.show',compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        if (auth()->user()->id !== $post->user_id) {
            throwException(new Exception('You are not allowed to edit this post.'));
        }
        return view('posts.edit', compact('post'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
        if (auth()->user()->id !== $post->user_id) {
            throwException(new Exception('You are not allowed to update this post.'));
        }
        $attributes = $request->validate($this->validationRules());
        $source = request('source');
        $attributes['description'] = $source;
        $attributes['hash'] = md5($source);
        $post->update($attributes);
        switch ($post->type) {
            case 'content':
                $post->content()->update([
                    'post_id' => $post->id,
                    'source' => $source,
                    'text' => $source,
                ]);
                break;
            default:
                throwException(new Exception('Unknown post type'));
        }
        return redirect()->route('posts.show', $post->id)
            ->with('status', 'Post updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        if (auth()->user()->id !== $post->user_id) {
            throwException(new Exception('You are not allowed to delete this post.'));
        }
        $post->delete();
        return redirect('/posts');
    }
}
