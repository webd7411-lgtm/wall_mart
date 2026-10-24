<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToBranch;

class Rider extends Model
{
    use HasFactory, BelongsToBranch;

    protected $table = 'riders';

    protected $fillable = [
        'branch_id',
        'name',
        'phone',
        'cnic',
        'vehicle_no',
        'duty_status',
        'is_active',
        'address',
        'notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relationship to Branch.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    /**
     * All delivery orders assigned to this rider.
     */
    public function deliveryOrders()
    {
        return $this->hasMany(Sale::class, 'rider_id');
    }

    /**
     * Currently active deliveries (on the road).
     */
    public function activeDeliveries()
    {
        return $this->hasMany(Sale::class, 'rider_id')->where('delivery_status', 'dispatched');
    }

    /**
     * Delivered orders where COD cash has not yet been settled/cleared at counter.
     */
    public function unsettledDeliveries()
    {
        return $this->hasMany(Sale::class, 'rider_id')
            ->where('delivery_status', 'delivered')
            ->where('rider_cash_settled', false);
    }

    /**
     * Total unsettled cash amount in rider's hand.
     */
    public function getCashInHandAttribute()
    {
        return (float) $this->unsettledDeliveries()->sum('total_bill_amount');
    }

    /**
     * Scope for active riders.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
