@extends('layouts.app')

@section('title', 'Mokėjimai')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="accordion" id="accordionPayments">
                <div class="card">
                    <div class="card-header" id="headingOne">
                    <h2 class="mb-0">
                        <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseOne" 
                            aria-expanded="false" aria-controls="collapseOne" style="font-size: 16px;">
                        Mokėjimai pagal savaites
                        </button>
                    </h2>
                    </div>
                    @if( $showPeriodic == false )
                    <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionPayments">
                    @else
                    <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordionPayments">
                    @endif
                        <div class="container row ml-1">
                            <payments :payment_types="{{ $payment_types }}" class="mt-4" />
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header" id="headingTwo">
                        <h2 class="mb-0">
                            <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseTwo" 
                                aria-expanded="false" aria-controls="collapseTwo" style="font-size: 16px;">
                            Išlaidų/įplaukų sąrašas
                            </button>
                        </h2>
                    </div>
                    <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionPayments">
                        <div class="card-body">
                            @if (session('status'))
                                <div class="alert alert-success" role="alert">
                                    {{ session('status') }}
                                </div>
                            @endif
                            <div class="container ml-2">
                                Bendras balansas: 
                                <span class="font-weight-bold">{{ $balanceCount }}</span>
                            </div>
                        <payments-table :payment_types="{{ $payment_types }}" />
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header" id="headingThree">
                    <h2 class="mb-0">
                        <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseThree" 
                            aria-expanded="false" aria-controls="collapseThree" style="font-size: 16px;">
                        Periodiniai mokėjimai
                        </button>
                    </h2>
                    </div>

                    @if( $showPeriodic == true )
                    <div id="collapseThree" class="collapse show" aria-labelledby="headingThree" data-parent="#accordionPayments">
                    @else
                    <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordionPayments">
                    @endif
                        <div class="container row ml-1">
                            <periodic-payments :payment_types="{{ $payment_types }}" :show_id="{{ $showId }}"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
