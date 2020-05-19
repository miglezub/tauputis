<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment_type extends Model
{
    protected $guarded=[];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
