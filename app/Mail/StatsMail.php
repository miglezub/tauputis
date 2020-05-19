<?php

namespace App\Mail;

use App\Cart;
use App\Payment_type;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StatsMail extends Mailable
{
    use Queueable, SerializesModels;
    private $userid = -1;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($id)
    {
        $this->userid=$id;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $user = User::find($this->userid);
        if($user) {
            $email = $user->value('email');
            $username = $user->value('name');
            $expenses = abs($user->payments
                ->whereBetween('date', [date("Y-m-d", strtotime('first day of last month')), 
                date("Y-m-d", strtotime('last day of last month'))])
                ->where('is_income', '=', '0')->sum('value'));
            $income = $user->payments
                ->whereBetween('date', [date("Y-m-d", strtotime('first day of last month')), 
                date("Y-m-d", strtotime('last day of last month'))])
                ->where('is_income', '=', '1')->sum('value');
            $carts = $user->carts()->orderBy('last_month_value', 'desc')->get(['last_month_value', 'fk_type']);
            $payment_types = Payment_type::all();
            //$expenses = 230;
            //$income = 220;
            return $this->from('tauputis@noreply.com')
                ->subject('Mėnesio ataskaita ' . date("Y-m", strtotime('first day of last month')))
                ->markdown('emails.statsMonthly', compact('username', 'email', 'expenses', 'income', 'carts', 'payment_types'));
        }
    }
}
