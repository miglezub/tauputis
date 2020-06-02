@extends('layouts.app')

@section('title', 'Krepšeliai')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">Krepšeliai</div>
                <div class="card-body px-0">
                    <all-carts :payment_types="{{ $payment_types }}" :balances="{{ $user->monthlyBalanceByType() }}"></all-carts>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
