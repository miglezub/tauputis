@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    You are logged in!
                </div>
                <div>
                    {{ $chart1->container() }}
                 </div>
                 <div class="container mt-5">
                    <div class="col-md-6 float-left">
                        {{ $chart2->container() }}
                    </div>
                    <div class="col-md-6 float-right">
                        {{ $chart3->container() }}
                    </div>
                 </div>
            </div>
        </div>
    </div>
</div>
{{ $chart1->script() }}
{{ $chart2->script() }}
{{ $chart3->script() }}
@endsection
