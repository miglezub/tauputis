@extends('layouts.app')

@section('title', 'Vartotojo nustatymai')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">Vartotojo informacija</div>
                <div class="card-body px-0">
                    <user-settings :user="{{ $user }}"></user-settings>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
