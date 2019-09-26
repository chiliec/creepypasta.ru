@extends('layouts.app')

@if($posts->onFirstPage())
    @section('title', $tag->name)
    @section('canonical', action('PostController@index'))
@else
    @section('title', $tag->name . ' - page ' . $posts->currentPage())
    @section('canonical', action('PostController@index', ['page' => $posts->currentPage()]))
@endif

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ $tag->name }}</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        @foreach($posts as $post)
                            <div class="card">
                                <p>Title: <a href="{{route('posts.detail', [$post->id, $post->slug])}}">{{ $post->title }}</a></p>
                                <p>Content: {{ $post->content->show() }}</p>
                                <p>Author: {{ $post->author->name }}</p>
                            </div>
                        @endforeach

                        {{ $posts->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
