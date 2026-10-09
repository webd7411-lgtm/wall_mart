<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory, \App\Traits\BelongsToBranch;

    protected $fillable = ['name', 'email', 'phone', 'address', 'opening_balance', 'branch_id'];

      public function ledger()
    {
        return $this->hasOne(VendorLedger::class);
    }
}
