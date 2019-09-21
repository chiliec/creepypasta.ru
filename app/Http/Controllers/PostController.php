<?php

namespace App\Http\Controllers;

use App\Post;
use App\PostContent;
use Cog\Laravel\Love\ReactionType\Models\ReactionType;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
        $this->middleware('auth', ['except' => ['index', 'show', 'detail']]);
        $this->authorizeResource(Post::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
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
     * @return Response
     */
    public function create()
    {
        return view('posts.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     * @throws HttpException
     * @throws NotFoundHttpException
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
                abort(Response::HTTP_BAD_REQUEST, 'Unknown post type');
        }
        return redirect()->route('posts.detail', [$post->id, $post->slug])
            ->with('status', 'Post created successfully.');
    }

    /**
     * Display post with slug url
     *
     * @param int $id
     * @param string $slug
     * @return Response
     * @throws AuthorizationException
     */
    public function detail($id, $slug = '')
    {
        $post = Post::findOrFail($id);
        $this->authorize('view', $post);
        if ($slug !== $post->slug) {
            return redirect()->to($post->url);
        }
        return view('posts.detail')
            ->withPost($post);
    }

    /**
     * Display the specified resource.
     *
     * @param Post $post
     * @return Response
     */
    public function show(Post $post)
    {
        return redirect()->to($post->url);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Post $post
     * @return Response
     */
    public function edit(Post $post)
    {
        return view('posts.edit', compact('post'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Post $post
     * @return Response
     * @throws HttpException
     * @throws NotFoundHttpException
     */
    public function update(Request $request, Post $post)
    {
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
                abort(Response::HTTP_BAD_REQUEST, 'Unknown post type');
        }
        return redirect()->route('posts.detail', [$post->id, $post->slug])
            ->with('status', 'Post updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Post $post
     * @return Response
     * @throws Exception
     */
    public function destroy(Post $post)
    {
        $post->delete();
        return redirect()->route('posts.index');
    }

    /**
     * @param Post $post
     * @param string $reactionTypeName
     * @return RedirectResponse|void
     */
    public function vote(Post $post, string $reactionTypeName = 'like')
    {
        $reactionNames = ReactionType::all()
            ->map(function($reaction) {
                return Str::lower($reaction->name);
            })
            ->toArray();
        if (!in_array($reactionTypeName, $reactionNames)) {
            return abort(500, 'Unknown reaction');
        }
        $statusMessage = 'Вы успешно проголосовали!';
        if ($post->viaLoveReactant()->isReactedBy(auth()->user(), $reactionTypeName)) {
            auth()->user()->viaLoveReacter()->unreactTo($post, $reactionTypeName);
            $statusMessage = 'Ваш голос успешно удалён!';
        } else {
            auth()->user()->viaLoveReacter()->reactTo($post, $reactionTypeName);
        }
        $reverseReactionTypeName = ($reactionTypeName === 'like') ? 'dislike' : 'like';
        if ($post->viaLoveReactant()->isReactedBy(auth()->user(), $reverseReactionTypeName)) {
            auth()->user()->viaLoveReacter()->unreactTo($post, $reverseReactionTypeName);
            $statusMessage = 'Ваш голос успешно изменён!';
        }
        return back()->with('status', $statusMessage);
    }
}
