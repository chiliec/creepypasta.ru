@extends('layouts.app')
@section('title', $post->title)
@section('canonical', $post->url)

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ $post->title }}</div>
                    <div class="pull-right">
                        <form action="{{ route('posts.destroy', $post->id) }}" method="POST">
                            <a class="btn btn-primary" href="{{ route('posts.index') }}">Назад</a>
                            @can('update', $post)
                                <a class="btn btn-dark" href="{{ route('posts.edit', $post->id) }}">Редактировать</a>
                            @endcan
                            @can('delete', $post)
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Вы уверены что хотите удалить пост?')">Удалить</button>
                            @endcan
                        </form>
                    </div>


                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <div class="card">
                            <p class="card-text">
                                {{ $post->content->show() }}
                            </p>
                            <div class="card-footer">
                                <div class="d-flex justify-content-center">
                                    <a href="{{ route('posts.vote', [$post, 'like']) }}" class="btn btn-primary btn-sm">({{ $post->viaLoveReactant()->getReactionCounterOfType('Like')->getCount() }}) 👍</a>
                                    <a href="{{ route('posts.vote', [$post, 'dislike']) }}" class="btn btn-secondary btn-sm">({{ $post->viaLoveReactant()->getReactionCounterOfType('Dislike')->getCount() }}) 👎</a>
                                </div>
                                <p>Автор: <a href="{{route('users.show', $post->author->id)}}">{{ $post->author->name }}</a></p>
                                <p>Теги: @foreach($post->tags as $tag) <a href="{{route('posts.tag', $tag->slug)}}">{{ $tag->name }}</a>, @endforeach</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
