<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fk_payment_type_id', 'caption', 'description',  
        'is_income', 'date', 'value',
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
