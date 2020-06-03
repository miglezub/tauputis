<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use \App\Payment_type;

class PaymentTypeController extends Controller
{
    public function index()
    {
        return Payment_type::all();
    }

    public function seedPaymentTypes()
    {
        $names = ["Įplaukos", "Maistas", "Buitis", "Mokesčiai", "Pramogos", "Restoranai",
            "Asmeninis transportas", "Viešas transportas", "Mokslas", "Kelionės", "Sveikatos išlaidos", "Kita"];
        $i = 1;
        foreach($names as $name) {
            $type = new Payment_type();
            $type->id = $i;
            $type->name = $name;
            $type->save();
            $i++;
        }
    }
}
