@extends('layouts.master')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Chat</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                        <div class="row">
{{--                            <div class="col-sm-6">--}}
{{--                                <create-group :initial-users="{{ $users }}"></create-group>--}}
{{--                            </div>--}}
                            <div class="col-12">
                                <groups :initial-groups="{{ $groups }}" :user="{{ $user }}"></groups>
                            </div>
                        </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
