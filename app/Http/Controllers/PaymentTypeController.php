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
}
