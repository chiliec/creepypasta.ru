@extends('layouts.app')
@section('title', $user->name)

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ $user->name }}</div>
                <div class="pull-right">
                    <a class="btn btn-primary" href="{{ route('users.index') }}">Назад</a>
                </div>
                <div class="card-body">
                    <p>All posts of this user:</p>
                    @foreach ($user->posts as $post)
                        <p><a href="{{route('posts.detail', [$post->id, $post->slug])}}">{{ $post->title }}</a></p>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
