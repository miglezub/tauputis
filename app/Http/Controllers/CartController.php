<?php

namespace App\Http\Controllers;

use App\Http\Resources\CartCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $payment_types=\App\Payment_type::all();
        $user=Auth::user();
        
        return view('carts.index', compact('user', 'payment_types'));
        
    }

    public function add(Request $request)
    {
        $id = Auth::id();
        $lastMonthPayments = -(Auth::user()->paymentsByDateType(4, $request->input('fk_type'))->sum('value'));
        $cart = new \App\Cart([
            'fk_user_id' => $id,
            'fk_type' => $request->input('fk_type'),
            'monthly_goal' => $request->input('value'),
            'transfer_balance' => $request->input('transfer_balance'),
            'last_month_value' => $lastMonthPayments,
        ]);
        $cart->save();

        return response()->json("The cart successfully added");
    }

    public function edit($id)
    {
        $cart=\App\Cart::find($id);
        return response()->json($cart);
    }

    public function update($id, Request $request)
    {
        $cart=\App\Cart::find($id);
        $cart->update($request->all());

        return response()->json('The cart was successfully updated');
    }
}
