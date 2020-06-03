<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function payments()
    {
        return $this->hasMany(Payment::class, 'fk_user_id')->orderBy('date', 'desc');
    }

    public function payments_asc()
    {
        return $this->hasMany(Payment::class, 'fk_user_id')->orderBy('date', 'asc');
    }

    public function payments_desc_type()
    {
        return $this->hasMany(Payment::class, 'fk_user_id')->orderBy('fk_payment_type_id', 'desc');
    }

    public function paymentsByType(int $type, bool $desc = true)
    {
        if($desc)
            $payments=$this->payments();
        else
            $payments=$this->payments_asc();
        return $payments
            ->where('fk_payment_type_id', $type);
    }

    public function paymentsByDate(int $date, bool $desc = true)
    {
        //dd(date("Y-m-d", strtotime('monday last week')));
        if($desc)
            $payments=$this->payments();
        else
            $payments=$this->payments_asc();
        switch($date) {
            case "0":
                return $payments;
            case "1":
                return $payments
                    ->whereBetween('date', [date("Y-m-d", strtotime('monday this week')), 
                    date("Y-m-d", strtotime('sunday this week'))]);
            case "2":
                return $payments
                    ->whereBetween('date', [date("Y-m-d", strtotime('monday last week')), 
                    date("Y-m-d", strtotime('sunday last week'))]);
            case "3":
                return $payments
                    ->whereBetween('date', [date("Y-m-01"), 
                    date("Y-m-t")]);
            case "4":
                return $payments
                    ->whereBetween('date', [date("Y-m-d", strtotime('first day of last month')), 
                    date("Y-m-d", strtotime('last day of last month'))]);
            case "5":
                return $payments
                    ->whereBetween('date', [date("Y-01-01"), 
                    date("Y-12-t")]);
            case "6":
                return $payments
                    ->whereBetween('date', [date("Y-01-01", strtotime("last year")), 
                    date("Y-12-t", strtotime("last year"))]);
            default: return $payments;
        }
    }

    public function paymentsByDateType(int $date, int $type, bool $desc = true)
    {
        $paymentsByDate=$this->paymentsByDate($date, $desc);
        //$paymentsByType=$this->paymentsByType($type);
        return $paymentsByDate->where('fk_payment_type_id', $type);
    }

    public function carts()
    {
        return $this->hasMany(Cart::class, 'fk_user_id');
    }

    public function updateCarts()
    {
        $carts = $this->carts;
        foreach( $carts as $cart) {
            if(strtotime($cart->balance_update) < strtotime('first day of this month') || 
                $cart->balance_update == null) {
                    $lastMonthValue = $this->payments()
                    ->where('fk_payment_type_id', $cart->fk_type)
                    ->whereBetween('date', [date("Y-m-d", strtotime('first day of last month')), 
                        date("Y-m-d", strtotime('last day of last month'))])
                    ->sum('value');
                    $cart->last_month_value = abs($lastMonthValue);
                    $cart->balance_update = date("Y-m-d");
                    $cart->save();
            }
            else break;
        }
    }

    public function monthlyBalanceByType()
    {
        $balances=array();
        $payments=$this->payments->whereBetween('date', [date("Y-m-01"), 
            date("Y-m-t")])->groupBy('fk_payment_type_id');
        
        foreach($payments->keys() as $key) {
            $balances[$key] = -$payments[$key]->sum('value');
        }
        return json_encode($balances);
    }

    public function periodicPayments()
    {
        return $this->hasMany(PeriodicPayment::class, 'fk_user_id');
    }
}
