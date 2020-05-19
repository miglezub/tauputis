@component('mail::message')
# <div class="text-success header">Mėnesinė ataskaita (<?php echo date("Y-m", strtotime('first day of last month')) ?>)</div>

Sveiki, {{ $username }}.<br>
Paruošta jūsų praeito mėnesio ataskaita:  
<div class="container">
    <span>Išlaidos: {{ $expenses }}</span>
    <span class="float-right">Įplaukos: {{ $income }}</span>
    <div class="center">
        @if ($income == 0 && $expenses == 0)
            Per praeitą mėnesį nebuvo pridėta išlaidų ar įplaukų.
        @elseif ($income == 0 && $expenses != 0)
            Šį mėnesį nebuvo pridėta pajamų, išlaidos: <span class="text-danger">{{ $expenses }}</span>.
        @elseif ($income != 0 && $expenses == 0)
            Šį mėnesį nebuvo pridėta išlaidų, pajamos: <span class="text-success">{{ $income }}</span>.
        @elseif ($income > $expenses)
            Išlaidos sudaro <span class="text-success">{{ round($expenses/$income*100, 2) }}%</span> įplaukų.
        @else
            Išlaidos viršija įplaukas <span class="text-danger">{{ round($expenses/$income*100-100, 2) }}%</span>.
        @endif
    </div>
    @if(count($carts) > 0)
        <hr class="text-success">
        Išlaidos, suskirstytos pagal kategorijų krepšelius:<br>
        <table id="cart-table" class="table">
            @foreach ($carts as $cart)
                <tr><td>{{ $payment_types[$cart->fk_type]->name }}</td><td>{{ $cart->last_month_value}}</td></tr>
            @endforeach
        </table>
    @endif
</div>
<div class="center">Diagramas ir išsamesnę statistiką, rasite puslapyje.</div>
@component('mail::button', ['url' => config('app.url'), 'color' => 'success'])
Peržiūrėti puslapyje
@endcomponent

<style>
    .header {
        font-size: 24px;
    }

    .center {
        text-align: center;
    }

    .text-success {
        color: #48bb78;
        font-weight: bold;
    }

    .text-danger {
        color: #FC175A;
        font-weight: bold;
    }

    .container {
        border: 1px solid #48bb78;
        font-size: 16px;
        border-radius: 4px;
        padding: 5px 10px;
    }

    .float-right {
        float: right;
    }

    .table {
        width: 70%; 
        margin: auto;
    }

    #cart-table>tr>td {
        border-bottom: 1px solid #48bb78;
    }
</style>
@endcomponent
