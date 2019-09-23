@extends('layouts.app')

@if($invites->onFirstPage())
    @section('title', 'Invites')
@else
    @section('title', 'Invites - page ' . $invites->currentPage())
@endif

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Posts</div>
                    <div class="pull-right">
                        <a class="btn btn-success" href="{{ route('invites.new') }}">Create new invite</a>
                    </div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        @foreach($invites as $invite)
                            <div class="card">
                                <p>Code: {{ $invite->code }}</p>
                            </div>
                        @endforeach

                        {{ $invites->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
