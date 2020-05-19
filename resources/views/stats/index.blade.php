@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">Statistika</div>
                <div class="card-body">
                    <div class="mt-2 mb-4">
                        <stats />
                    </div>
                    <div class="container h-100">
                        <line-chart-container :payment_types="{{ $payment_types }}"></line-chart-container>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
