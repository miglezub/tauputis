<?php

namespace App\Http\Controllers;

use App\Http\Resources\PaymentResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;
use mt_rand;

class PaymentController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        /*
        $filterType=request()->has('filter_type') && request('filter_type') < 13 && request('filter_type') >= 0 ? request('filter_type') : 0;
        $filterDate=request()->has('filter_date') && request('filter_date') < 7 && request('filter_date') >= 0 ? request('filter_date') : 0;

        $payments=$this->filterPayments($filterType, $filterDate, $user)->paginate(5);
        */
        $payment_types=\App\Payment_type::all();
        $balanceCount=Cache::remember(
            'balance.' . $user->id, 
            now()->addSeconds(20),
            function() use ($user) {
                return $user->payments->sum('value');
            });
        $showPeriodic = request()->has('showPeriodic') ? true : false;
        $showId = request()->has('showId') ? request('showId') : -1;

        //$filteredBalanceCount=$filterType!=0 || $filterDate!=0 ? $payments->sum('value'):0;
        /*
        return view('payments.index', compact('user', 'payments', 
            'payment_types', 'filterType', 'filterDate', 'balanceCount',
            'filteredBalanceCount'));
            */
        return view('payments.index', compact('balanceCount', 'payment_types', 'showPeriodic', 'showId'));
    }

    public function show($id)
    {
        return Auth::user()->payments->find($id);
    }

    public function destroy(\App\Payment $payment)
    {
        $payment->delete();
        $user = Auth::user();
        return redirect(route("payments.show"));
        //return view('payments.index', compact('user'));
    }

    public function destroyById($id)
    {
        return Auth::user()->payments->find($id)->delete();
        //return view('payments.index', compact('user'));
    }

    public function update($id, Request $request)
    {
        $payment=\App\Payment::find($id);
        $payment->update($request->all());

        return response()->json('The payment was successfully updated');
    }

    public function create()
    {
        $payment_types=\App\Payment_type::all();
        return view('payments.create', compact('payment_types'));
    }

    public function store()
    {
        $data=request()->validate([
            'is_income' => 'required',
            'caption' => 'nullable|max:100',
            'description' => 'nullable|max:255',
            'date' => 'required|date',
            'value' => 'required|numeric|min:0.01|max:999999.99',
            'payment_type' => 'required',
        ]);
            /*
        if(request('photo')!=null) {
            $imagePath=request('photo')->store('uploads', 'public');
                    
            $image=Image::make(public_path("storage/{$imagePath}"))->fit(1200,1200);
            $image->save();
        }
        else
            $imagePath=null;
            */
        /*
        $isincome=request('type')=='type_expense'?'0':'1';
        $expensetype=request('type')=='type_expense'?request('payment_type'):'1';
        */
        $value=request('is_income') == 0 ? round((-request('value')), 2) : round(request('value'), 2);
        
        $newPayment = auth()->user()->payments()->create([
            'fk_payment_type_id' => $data['payment_type'],
            'caption' => $data['caption'],
            'description' => $data['description'],
            'is_income' => $data['is_income'],
            'date' => $data['date'],
            'value' => $value,
        ]);
        
        // updates cart's last month value
        if($newPayment && $data['payment_type'] != '1' && 
            $data['date'] >= date("Y-m-d", strtotime('first day of last month')) 
            && $data['date'] <= date("Y-m-d", strtotime('last day of last month')))
            $this->updateLastMonthBalance($data['payment_type'], request('value'));
        return redirect('payments');
    }
    
    public function getPaymentsForTable(Request $request)
    {
        $filterType=request()->has('filter_type') && request('filter_type') < 13 && request('filter_type') >= 0 ? request('filter_type') : 0;
        $filterDate=request()->has('filter_date') && request('filter_date') < 7 && request('filter_date') >= 0 ? request('filter_date') : 0;
        //dd($filterDate);
        return $this->filterPayments($filterType, $filterDate)->get();
    }
    
    public function filterPayments(int $filterType, int $filterDate, bool $desc = true)
    {
        $user = Auth::user();
        if($desc)
            $payments=$user->payments();
        else
            $payments=$user->payments_asc();
        if($filterType!=0 && $filterDate!=0) {
                $payments=$user->paymentsByDateType($filterDate, $filterType, $desc);
            }
        else if($filterDate!=0) {
            $payments=$user->paymentsByDate($filterDate, $desc);
        }
        else if($filterType!=0) {
            $payments=$user->paymentsByType($filterType, $desc);
        }

        return $payments;
    }

    public function updateLastMonthBalance($type, $value)
    {
        $cart=\App\Cart::where('fk_user_id', Auth::id())
            ->where('fk_type', $type)
            ->first();
        if($cart) {
            $oldValue = $cart->last_month_value ? $cart->last_month_value : 0;
            $newValue= $oldValue + $value;
            $cart->last_month_value = $newValue;
            $cart->save();
        }

    }

    public function lineChartPayments()
    {
        $dateFrom=request()->has('date_from')?request('date_from'):date("Y-01-01");
        $dateTo=request()->has('date_to')?request('date_to'):date("Y-m-d");
        $payments=Auth::user()
            ->payments_asc()
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->get();
        $lastValue=Auth::user()
            ->payments_asc()
            ->whereDate('date', '<', $dateFrom)
            ->sum('value');
        foreach($payments as $payment) {
            $value=round($payment->value+$lastValue, 2);
            $payment->balance=$value;
            $lastValue=$value;
        }
        return $payments;
    }

    public function pieChartPayments()
    {
        $dateFrom=request()->has('date_from')?request('date_from'):date("Y-01-01");
        $dateTo=request()->has('date_to')?request('date_to'):date("Y-m-d");
        $payments=Auth::user()
            ->payments
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->groupBy('fk_payment_type_id');
        //return $payments;
        $array=array();
        
        foreach($payments as $payment ) {
            if($payment[0]->fk_payment_type_id!=1) {
                $payment_type=\App\Payment_type::where('id', $payment[0]->fk_payment_type_id)->first();
                array_push($array, 
                    array("value" => round(abs($payment->sum('value')), 2), 
                    "label" => $payment_type->name));
            }
        }
        return $array;
    }

    public function groupPayments()
    {
        //$selectedPage = request()->has('page') ? (int)request('page') : 1;
        $payments=Auth::user()
        ->payments_asc
        ->groupBy([function($data) {
            $data->week=Carbon::parse($data->date)->format('Y-W');
            return $data->week;
        }, 'date'])
        ->reverse();
        /*
        $count = $payments->count();
        $page = $selectedPage <= ceil($count/2) ? $selectedPage : 1;
        return $payments->skip($count-(2*$page))->take(2)->reverse();
        */
        return $payments;
    }

    public function stats()
    {
        $stats = array();
        
        $stats['thisMonthIncome'] = round(Auth::user()->paymentsByDate(3)->where('is_income', '=', '1')->sum('value'), 2);
        $stats['thisMonthExpenses'] = abs(round(Auth::user()->paymentsByDate(3)->where('is_income', '=', '0')->sum('value'), 2));
        $stats['lastMonthIncome'] = round(Auth::user()->paymentsByDate(4)->where('is_income', '=', '1')->sum('value'), 2);
        $stats['lastMonthExpenses'] = abs(round(Auth::user()->paymentsByDate(4)->where('is_income', '=', '0')->sum('value'), 2));
        
        return $stats;
    }

    public function balance()
    {
        return Auth::user()->payments->sum('value');
    }

    public function seedPayments()
    {
        $userid = 1;
        for($i = 1; $i < 20; $i++) {
            $payment = new \App\Payment();
            $payment->fk_user_id = $userid;
            $payment->caption = "seed";
            $payment->is_income = 0;
            $timestamp = rand( strtotime("-2 month"), strtotime("now") );
            $random_Date = date("Y-m-d", $timestamp);
            $payment->date = $random_Date;
            $payment->value = -mt_rand(10, 1000) / 10;
            $payment->fk_payment_type_id = mt_rand(2, 12);
            $payment->save();
        }
        for($i = 1; $i < 6; $i++) {
            $payment = new \App\Payment();
            $payment->fk_user_id = $userid;
            $payment->caption = "seed";
            $payment->is_income = 1;
            $timestamp = rand( strtotime("-2 month"), strtotime("now") );
            $random_Date = date("Y-m-d", $timestamp);
            $payment->date = $random_Date;
            $payment->value = mt_rand(10, 4000) / 10;
            $payment->fk_payment_type_id = 1;
            $payment->save();
        }
    }
}
