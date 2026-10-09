<?php

// app/Models/CustomerPayment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerPayment extends Model
{
    use \App\Traits\BelongsToBranch;

    protected $fillable = [
        'customer_id',
        'admin_or_user_id',
        'amount',
        'payment_method',
        'payment_date',
        'note',
        'branch_id',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
