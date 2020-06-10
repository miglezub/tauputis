<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable=['monthly_goal', 'fk_type', 'transfer_balance', 'fk_user_id', 'last_month_value'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function type()
    {
        return $this->belongsTo(Payment_type::class);
    }
}
