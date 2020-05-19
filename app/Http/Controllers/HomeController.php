<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Charts\DemoChart;
use App\Payment;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        //dd(Auth::user());
        $chart1=$this->createLineChart();
        $chart2=$this->createPieChart(0, "Mokėjimų kiekis pagal kategorijas");
        $chart3=$this->createPieChart(1, "Išlaidų sumos pagal kategorijas");
        
        return view('home', compact('chart1', 'chart2', 'chart3'));
    }    

    public function createPieChart($type, $title)
    {
        $dataGrouped=Auth::user()->payments_desc_type->groupBy('fk_payment_type_id');
        //$datax=$dataGrouped->keys();
        $datay=array();
        $dataxtypes=array();
        $i=0;
        //dd($dataGrouped->keys());
        foreach($dataGrouped->keys() as $key) {
            //dd($key);
            if($type==0 ) {
                array_push($datay, $dataGrouped->values()[$i++]->count());
                $payment_type=\App\Payment_type::where('id', $key)->first();
                array_push($dataxtypes, $payment_type->name);
            }
            else if($type==1 && $key!=1) {
                array_push($datay, abs($dataGrouped->values()[$i++]->sum('value')));
                $payment_type=\App\Payment_type::where('id', $key)->first();
                array_push($dataxtypes, $payment_type->name);
            }
            else
                $i++;
        }
        //dd($datay);
        $chart2=new DemoChart;
        $chart2->labels($dataxtypes);
        $colors=array("#B35D64", "#FFB8BE", "#FF9EA7", '#6BB87D', '#9EFFB5', 
            '#B3465B', '#FFF98F', '#FF9EB1', '#9EE4FF', '#3E91B3', "#FFB8BE", "#FF9EA7");
        $chart2->dataset("Mokėjimai", 'pie', $datay)
            ->backgroundcolor($colors);
        $chart2->minimalist(true)
            ->title($title)
            ->displayLegend(true);
        return $chart2;
    }

    public function createLineChart()
    {
        $chart1=new DemoChart;

        $datax=Auth::user()->payments_asc
            ->map(function($item){
                return $item->date;
            });
        $dataz=Auth::user()->payments_asc
        ->map(function($item){
            return $item->fk_payment_type_id;
        });
        
        $datay=array();
        $lastValue=0;
        //dd(Auth::user()->payments_asc);
        $payments=Auth::user()->payments_asc;
        foreach($payments as $payment) {
            $value=$payment->value+$lastValue;
            array_push($datay, $value);
            $lastValue=$value;
        }
        
        $chart1->labels($datax->values(), $dataz->values());
        //$chart1->labels($dataz->values());
        $chart1->dataset('Išlaidos / Įplaukos', 'line', $datay)
            ->color("#007bff")
            ->fill(true);
        return $chart1;
    }
}
