<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceSeries extends Model
{
    use HasFactory, \App\Traits\BelongsToBranch;

    protected $table = 'invoice_series';

    protected $guarded = [];

    /**
     * Generate the next formatted invoice number for a given prefix.
     */
    public static function generateNextNo($prefix = null)
    {
        $series = null;

        if ($prefix) {
            $series = self::where('prefix', strtoupper(trim($prefix)))->first();
            if (!$series) {
                $series = self::withoutGlobalScopes()->where('prefix', strtoupper(trim($prefix)))->first();
            }
        }

        if (!$series) {
            $series = self::where('is_default', 1)->first() ?: self::first();
        }

        if (!$series) {
            $series = self::withoutGlobalScopes()->where('is_default', 1)->first() ?: self::withoutGlobalScopes()->first();
        }

        $pref = $series ? strtoupper($series->prefix) : 'INV';
        $padding = $series ? ($series->padding ?: 4) : 4;
        $startNum = $series ? (int) $series->next_number : 1;

        // Find highest existing numeric suffix across ALL branches to ensure global uniqueness
        $lastSale = Sale::withoutGlobalScopes()
            ->where('invoice_no', 'LIKE', $pref . '-%')
            ->orderBy('id', 'desc')
            ->first();

        $numFromSale = 0;
        if ($lastSale && $lastSale->invoice_no) {
            if (preg_match('/' . preg_quote($pref, '/') . '-(\d+)/i', $lastSale->invoice_no, $matches)) {
                $numFromSale = (int) $matches[1];
            }
        }

        $nextNum = max($startNum, $numFromSale + 1);

        // Loop until guaranteed unused unique candidate is found
        do {
            $candidate = $pref . '-' . str_pad($nextNum, $padding, '0', STR_PAD_LEFT);
            $exists = Sale::withoutGlobalScopes()->where('invoice_no', $candidate)->exists();
            if ($exists) {
                $nextNum++;
            }
        } while ($exists);

        return $candidate;
    }

    /**
     * Increment series counter after sale creation if applicable
     */
    public static function incrementCounterForInvoice($invoiceNo)
    {
        if (empty($invoiceNo)) return;

        if (preg_match('/^([A-Z0-9]+)-(\d+)$/i', trim($invoiceNo), $matches)) {
            $prefix = strtoupper($matches[1]);
            $number = (int) $matches[2];

            $series = self::where('prefix', $prefix)->first();
            if (!$series) {
                $series = self::withoutGlobalScopes()->where('prefix', $prefix)->first();
            }

            if ($series) {
                if ($number >= $series->next_number) {
                    $series->next_number = $number + 1;
                    $series->save();
                }
            } else {
                self::create([
                    'prefix' => $prefix,
                    'next_number' => $number + 1,
                    'padding' => strlen($matches[2]),
                    'is_default' => 0
                ]);
            }
        }
    }
}
