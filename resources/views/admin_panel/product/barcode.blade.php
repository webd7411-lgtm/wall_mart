<!DOCTYPE html>
<html>
<head>
    <title>{{ $product->item_name }} – Barcodes</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: Arial, sans-serif;
            background: #f3f4f6;
            margin: 0;
            padding: 20px;
        }
        h2 {
            text-align: center;
            margin-bottom: 16px;
            font-size: 20px;
            color: #1e293b;
        }
        .actions {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .btn-print-all {
            padding: 9px 22px;
            background: #2563eb;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 2px 4px rgba(37,99,235,0.2);
        }
        .btn-print-all:hover { background: #1d4ed8; }

        .btn-print-selected {
            padding: 9px 22px;
            background: #7c3aed;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 2px 4px rgba(124,58,237,0.2);
        }
        .btn-print-selected:hover { background: #6d28d9; }

        .select-all-wrap {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 14px;
            font-weight: 600;
            color: #374151;
            background: #fff;
            padding: 8px 14px;
            border-radius: 6px;
            border: 1px solid #d1d5db;
            cursor: pointer;
        }
        .select-all-wrap input {
            cursor: pointer;
            width: 16px;
            height: 16px;
        }

        .grid {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            justify-content: center;
        }
        .barcode-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            position: relative;
        }
        .card-select-wrap {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 12px;
            font-weight: 600;
            color: #4b5563;
            cursor: pointer;
            user-select: none;
        }
        .card-select-wrap input {
            cursor: pointer;
            width: 15px;
            height: 15px;
        }
        .card-btns {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        /* Optimized for Zebra & Thermal Barcode Roll Printers (e.g. 50mm x 25mm / 38mm x 25mm) */
        .label {
            border: 1px dashed #777;
            padding: 6px 8px;
            width: 260px;
            max-width: 100%;
            margin: 0 auto;
            text-align: center;
            background: #fff;
            box-sizing: border-box;
            border-radius: 3px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
            page-break-inside: avoid;
            overflow: hidden;
        }
        .product-name {
            font-size: 13px;
            font-weight: 700;
            color: #000;
            line-height: 1.15;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-bottom: 2px;
            text-align: center;
        }
        .variant-info {
            font-size: 10px;
            font-weight: 600;
            color: #333;
            line-height: 1.1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-bottom: 2px;
            text-align: center;
        }
        .barcode-wrap {
            margin: 3px auto;
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            overflow: hidden;
        }
        .barcode-wrap img, .barcode-img {
            display: block;
            margin: 0 auto;
            max-width: 100%;
            height: 26px;
            image-rendering: -webkit-optimize-contrast;
            image-rendering: pixelated;
        }
        .barcode-wrap > div {
            display: inline-block !important;
            margin: 0 auto !important;
            position: relative !important;
        }
        .barcode-number {
            font-size: 10px;
            font-weight: 700;
            color: #000;
            letter-spacing: 1px;
            line-height: 1.1;
            margin-top: 2px;
            margin-bottom: 2px;
            text-align: center;
        }
        .price {
            font-size: 12px;
            font-weight: 800;
            color: #000;
            line-height: 1.1;
            margin-top: 2px;
            text-align: center;
        }
        .btn-print-single {
            padding: 4px 10px;
            background: #059669;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 3px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.08);
        }
        .btn-print-single:hover { background: #047857; }

        @media print {
            @page {
                /* size: 50.8mm 55.0mm; */
                margin: 0mm;
            }
            html, body {
                background: #fff !important;
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
            }
            .actions, h2, .card-btns, .card-select-wrap {
                display: none !important;
            }
            .grid {
                display: block !important;
                gap: 0 !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
            }
            .barcode-card {
                margin: 0 auto !important;
                padding: 0 !important;
                width: 100% !important;
                page-break-after: always;
                page-break-inside: avoid;
                display: block !important;
                text-align: center !important;
            }
            .label {
                border: none !important;
                box-shadow: none !important;
                margin: 0 auto !important;
                padding: 2mm 0 !important;
                width: 100% !important;
                max-width: 50mm !important;
                text-align: center !important;
                page-break-inside: avoid;
            }
            .barcode-wrap {
                margin: 1mm auto !important;
                width: 100% !important;
                text-align: center !important;
                display: block !important;
            }
            .barcode-wrap img, .barcode-img {
                display: block !important;
                margin: 0 auto !important;
                max-width: 90% !important;
            }

            /* Single or Selected print mode */
            body.print-selective-mode .barcode-card {
                display: none !important;
            }
            body.print-selective-mode .barcode-card.printing-this {
                display: block !important;
                page-break-after: always;
            }
        }
    </style>
</head>
<body>

<h2>{{ $product->item_name }} — Variant Barcodes</h2>

<div class="actions">
    <label class="select-all-wrap">
        <input type="checkbox" id="selectAll" onchange="toggleSelectAll(this)">
        Select All
    </label>
    <button class="btn-print-selected" onclick="printSelected()">
        🖨️ Print Selected
    </button>
    <button class="btn-print-all" onclick="printAll()">
        🖨️ Print All Barcodes
    </button>
</div>

<div class="grid">
@forelse ($variants as $index => $variant)
    <div class="barcode-card" id="card-{{ $index }}">
        <div class="label" id="label-{{ $index }}">
            <div class="product-name">{{ $product->item_name }}</div>
            <div class="variant-info">
                {{ $variant['name'] ?? '' }}
                @if(!empty($variant['size']) && $variant['size'] !== '-') | {{ $variant['size'] }} @endif
                @if(!empty($variant['color']) && $variant['color'] !== '-') | {{ $variant['color'] }} @endif
            </div>
            <div class="barcode-wrap">
                @if(!empty($variant['barcode']))
                    <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($variant['barcode'], 'C128', 2, 30) }}" alt="{{ $variant['barcode'] }}" class="barcode-img">
                @else
                    <span style="color:#ef4444;font-size:10px;">No barcode</span>
                @endif
            </div>
            <div class="barcode-number">{{ $variant['barcode'] ?? '—' }}</div>
            <!-- <div class="price">PKR: {{ number_format((float)($variant['sale_price'] ?? 0)) }}</div> -->
        </div>
        <div class="card-btns">
            <label class="card-select-wrap">
                <input type="checkbox" class="barcode-check" value="card-{{ $index }}" onchange="updateSelectAllState()">
                Select
            </label>
            <button type="button" class="btn-print-single" onclick="printSingleCard('card-{{ $index }}')">
                🖨️ Print
            </button>
        </div>
    </div>
@empty
    {{-- Fallback: product has no variants --}}
    <div class="barcode-card" id="card-main">
        <div class="label" id="label-main">
            <div class="product-name">{{ $product->item_name }}</div>
            <div class="barcode-wrap">
                @php $fallbackCode = $product->barcode_path ?: $product->item_code; @endphp
                @if(!empty($fallbackCode))
                    <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($fallbackCode, 'C128', 2, 30) }}" alt="{{ $fallbackCode }}" class="barcode-img">
                @else
                    <span style="color:#ef4444;font-size:10px;">No barcode</span>
                @endif
            </div>
            <div class="barcode-number">{{ $product->barcode_path ?? $product->item_code }}</div>
            <!-- <div class="price">PKR: {{ number_format((float)($product->sale_price_per_piece ?: $product->sale_price_per_box ?: 0)) }}</div> -->
        </div>
        <div class="card-btns">
            <label class="card-select-wrap">
                <input type="checkbox" class="barcode-check" value="card-main" onchange="updateSelectAllState()">
                Select
            </label>
            <button type="button" class="btn-print-single" onclick="printSingleCard('card-main')">
                🖨️ Print
            </button>
        </div>
    </div>
@endforelse
</div>

<script>
function printAll() {
    document.body.classList.remove('print-selective-mode');
    document.querySelectorAll('.barcode-card').forEach(c => c.classList.remove('printing-this'));
    window.print();
}

function printSingleCard(cardId) {
    const targetCard = document.getElementById(cardId);
    if (!targetCard) return;

    document.querySelectorAll('.barcode-card').forEach(c => c.classList.remove('printing-this'));
    targetCard.classList.add('printing-this');
    document.body.classList.add('print-selective-mode');

    window.print();

    setTimeout(() => {
        document.body.classList.remove('print-selective-mode');
        targetCard.classList.remove('printing-this');
    }, 500);
}

function toggleSelectAll(masterCheckbox) {
    const checkboxes = document.querySelectorAll('.barcode-check');
    checkboxes.forEach(cb => cb.checked = masterCheckbox.checked);
}

function updateSelectAllState() {
    const checkboxes = document.querySelectorAll('.barcode-check');
    const checked = document.querySelectorAll('.barcode-check:checked');
    const master = document.getElementById('selectAll');
    if (master) {
        master.checked = checkboxes.length > 0 && checkboxes.length === checked.length;
    }
}

function printSelected() {
    const checked = document.querySelectorAll('.barcode-check:checked');
    if (checked.length === 0) {
        alert('Please select at least one barcode to print!');
        return;
    }

    document.querySelectorAll('.barcode-card').forEach(c => c.classList.remove('printing-this'));

    checked.forEach(cb => {
        const card = document.getElementById(cb.value);
        if (card) {
            card.classList.add('printing-this');
        }
    });

    document.body.classList.add('print-selective-mode');
    window.print();

    setTimeout(() => {
        document.body.classList.remove('print-selective-mode');
        document.querySelectorAll('.barcode-card').forEach(c => c.classList.remove('printing-this'));
    }, 500);
}
</script>

</body>
</html>
