@extends('layouts.app')
@section('title', 'Add New Post')

@section('footer')
    <script src="{{ asset('tinymce/tinymce.js') }}"></script>
    <script>
        tinymce.init({
            selector: 'textarea.source',
            theme: 'silver',
            menubar: false,
            plugins: ["lists link image media autolink imagetools preview code fullscreen"],
            toolbar: "bold italic | bullist numlist | link image media | preview code fullscreen"
        });
    </script>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Add New Post</h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-primary" href="{{ route('posts.index') }}"> Back</a>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('posts.store') }}" method="POST">
        @csrf
        <input type="text" name="type" value="content" hidden />
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Заголовок:</strong>
                    <input type="text" name="title" class="form-control" placeholder="Заголовок" value="{{ old('title') }}">
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Текст:</strong>
                    <textarea class="form-control source" style="height:340px" name="source" placeholder="Текст">{{ old('source') }}</textarea>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Теги:</strong>
                    <input type="text" name="tags" class="form-control" placeholder="google, вконтакте, кирпич" value="{{ old('tags') }}">
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>

    </form>
@endsection
