<?php
// app/Models/CustomerLedger.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerLedger extends Model
{
    use \App\Traits\BelongsToBranch;

    protected $guarded = [];


    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
