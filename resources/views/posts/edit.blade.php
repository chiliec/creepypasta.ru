@extends('layouts.app')
@section('title', 'Edit post')

@section('content')
    <div class="container min-h-screen flex justify-center">
        <post-editor :post="{{ json_encode($post) }}" />
    </div>
@endsection
