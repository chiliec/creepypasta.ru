@extends('layouts.app')
@section('title', 'Edit post')

@section('footer')
    @include('partials.editor-init')
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Edit post</h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-primary" href="{{ route('posts.index') }}"> Back</a>
            </div>
        </div>
    </div>

    @include('partials.errors')

    <form action="{{ route('posts.update', $post->id) }}" method="POST">
        @csrf
        @method('PUT')

        <input type="text" name="type" value="{{ $post->type }}" hidden />

        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Заголовок:</strong>
                    <input type="text" name="title" class="form-control" placeholder="Заголовок" value="{{ old('title') ? old('title') : $post->title }}">
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Текст:</strong>
                    <textarea class="form-control source" style="height:150px" name="source" placeholder="Текст">{{ old('source') ? old('source') : $post->content->source }}</textarea>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Теги:</strong>
                    <input type="text" name="tags" class="form-control" placeholder="google, вконтакте, кирпич" value="{{ old('tags') ? old('tags') : $post->tagsString }}">
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>

    </form>
@endsection
