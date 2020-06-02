<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PeriodicPaymentController extends Controller
{
    public function index()
    {
        return Auth::user()->periodicPayments;
    }

    public function store()
    {
        $data=request()->validate([
            'is_income' => 'required',
            'caption' => 'nullable|max:100',
            'description' => 'nullable|max:255',
            'value' => 'required|numeric|min:0.01|max:999999.99',
            'type' => 'required|integer',
            'period_type' => 'required|integer|min:1|max:2',
            'period_day' => 'required|integer|min:0|max:28',
        ]);
        
        $value=request('is_income') == 0 ? round((-request('value')), 2) : round(request('value'), 2);
        
        auth()->user()->periodicPayments()->create([
            'fk_payment_type_id' => $data['type'],
            'caption' => $data['caption'],
            'description' => $data['description'],
            'is_income' => $data['is_income'],
            'value' => $value,
            'periodic_type' => $data['period_type'],
            'periodic_day' => $data['period_day']
        ]);
    }

    public function destroyById(int $id)
    {
        return Auth::user()->periodicPayments->find($id)->delete();
    }

    public function update(int $id, Request $request)
    {
        $payment=Auth::user()->periodicPayments->find($id);
        $payment->update($request->all());
    }

    public function todayPayments()
    {
        $payments = Auth::user()
            ->periodicPayments
            ->where('periodic_type', 1)
            ->where('periodic_day', date('d'))
            ->where('last_added', '<', date('Y-m-d'));
        $payments2 = Auth::user()
            ->periodicPayments
            ->where('periodic_type', 2)
            ->where('periodic_day', date('w'))
            ->where('last_added', '<', date('Y-m-d'));
        return $payments->merge($payments2);
    }

    public function storePayment()
    {
        $data=request()->validate([
            'is_income' => 'required',
            'caption' => 'nullable|max:100',
            'description' => 'nullable|max:255',
            'value' => 'required|numeric|min:-999999.99|max:999999.99',
            'fk_payment_type_id' => 'required',
            'periodic_type' => 'required|integer|min:1|max:2',
            'periodic_day' => 'required|integer|min:0|max:28'
        ]);
        $value = abs(request('value'));
        $value=request('is_income') == 0 ? round(-$value, 2) : round($value, 2);
        $date=Date('Y-m-d');
        auth()->user()->payments()->create([
            'fk_payment_type_id' => $data['fk_payment_type_id'],
            'caption' => $data['caption'],
            'description' => $data['description'],
            'is_income' => $data['is_income'],
            'date' => $date,
            'value' => $value,
        ]);
        
        $payment = Auth::user()->periodicPayments()->find(request('id'));
        $payment->last_added = Date('Y-m-d');
        $payment->save();
    }
}
