<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToBranch;

class RestaurantTable extends Model
{
    use HasFactory, BelongsToBranch;

    protected $table = 'restaurant_tables';

    protected $fillable = [
        'branch_id',
        'table_no',
        'floor_area',
        'capacity',
        'status',
        'is_active',
        'sort_order',
        'notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'capacity' => 'integer',
        'sort_order' => 'integer',
    ];

    /**
     * Relationship to Branch.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    /**
     * Scope for active tables.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Relationship to any active running sale on this table.
     */
    public function activeSale()
    {
        return $this->hasOne(Sale::class, 'table_no', 'table_no')
            ->where('order_status', 'running')
            ->where('sale_status', 'booked')
            ->when($this->branch_id, function ($q) {
                $q->where('branch_id', $this->branch_id);
            });
    }
}
