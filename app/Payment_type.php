<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment_type extends Model
{
    protected $guarded=[];
    public $timestamps = false;
    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
