@extends('layouts.app')

@if($users->onFirstPage())
    @section('title', 'Users')
@else
    @section('title', 'Users - page ' . $users->currentPage())
    @section('canonical', action('UserController@index', ['page' => $users->currentPage()]))
@endif

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Users</div>
                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        @foreach($users as $user)
                            <div class="card">
                                <p>Name: <a href="{{route('users.show', $user->id)}}">{{ $user->name }}</a></p>
                            </div>
                        @endforeach

                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
