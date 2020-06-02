<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PeriodicPayment extends Model
{
    protected $fillable = [
        'fk_payment_type_id', 'caption', 'description',  
        'is_income', 'periodic_type', 'periodic_day', 'value',
    ];

    public function payment_type()
    {
        return $this->belongsTo(Payment_type::class, 'fk_payment_type_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
