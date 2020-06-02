<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('user_settings.index', compact('user'));
    }

    public function editUser()
    {
        $validated=request()->validate([
            'name' => 'required|string|min:4|max:255',
            'email' => 'required|string|email|max:255',
            'subscription' => 'required',
        ]);
        $errors["name"]=array();
        $errors["email"]=array();
        $error = 0;
        if($validated['name'] != Auth::user()->name && 
            \App\User::all()->where('name', $validated['name'])->count() > 0) {
                array_push($errors["name"], "Pasirinktas vartotojo vardas jau panaudotas");
                $error++;
        }
        if($validated['email'] != Auth::user()->email && 
            \App\User::all()->where('email', $validated['email'])->count() > 0) {
                array_push($errors["email"], "Pasirinktas el. pašto adresas jau panaudotas");
                $error++;
        }
        if($error > 0)
            return response()->json(["errors" => $errors]);

        $user=\App\User::find(request('id'));
        $user->subscription = (int)request('subscription');
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->save();

        return response("User updated", 200);
    }

    public function destroyPaymentsCarts()
    {
        \App\Payment::where('fk_user_id', Auth::id())->delete();
        \App\Cart::where('fk_user_id', Auth::id())->delete();
    }

    public function destroyUser()
    {
        $this->destroyPaymentsCarts();
        \App\User::where('id', Auth::id())->delete();
    }
}
