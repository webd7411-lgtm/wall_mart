<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$sale = App\Models\Sale::where('reference', 'like', '%MFC-3E4767%')
    ->orWhere('invoice_no', 'like', '%MFC-3E4767%')
    ->first();

if($sale) {
    echo "ID: {$sale->id}\n";
    echo "Sale Status: " . $sale->sale_status . "\n";
    echo "Order Status: " . $sale->order_status . "\n";
} else {
    echo "Sale not found\n";
}
