@extends('admin_panel.layout.app')

@section('content')

<div class="main-content">
    <div class="main-content-inner">
        <div class="container-fluid">

            <div class="page-header row align-items-center mb-4">
                <div class="page-title col-lg-6 col-12">
                    <h4 class="font-weight-bold text-dark mb-1">Customer Payments & Recoveries</h4>
                    <p class="text-muted mb-0">Manage customer receivables, cash recoveries & balance adjustments</p>
                </div>
                <div class="page-btn d-flex justify-content-lg-end justify-content-start col-lg-6 col-12 mt-3 mt-lg-0">
                    @canany(['customer.payments.create', 'customers.create'])
                        <button type="button" class="btn btn-primary font-weight-bold px-3 py-2" data-toggle="modal" data-target="#paymentModal" data-bs-toggle="modal" data-bs-target="#paymentModal" onclick="openPaymentModal()">
                            <i class="fas fa-plus-circle mr-1"></i> Add Payment
                        </button>
                    @endcanany
                </div>
            </div>

            @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong><i class="fas fa-check-circle mr-1"></i> Success!</strong> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif

            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover datanew">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                    <th>Method</th>
                                    <th>Note</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($payments as $key => $p)
                                <tr>
                                    <td>{{ $key+1 }}</td>
                                    <td>
                                        <strong>{{ $p->customer->customer_name ?? 'N/A' }}</strong>
                                        @if(!empty($p->customer->customer_mobile))
                                            <br><small class="text-muted"><i class="fas fa-phone-alt fa-xs"></i> {{ $p->customer->customer_mobile }}</small>
                                        @endif
                                    </td>
                                    <td><strong class="text-success">Rs. {{ number_format($p->amount, 2) }}</strong></td>
                                    <td>{{ $p->payment_date }}</td>
                                    <td><span class="badge badge-light border px-2 py-1">{{ $p->payment_method ?? 'Cash' }}</span></td>
                                    <td>{{ $p->note ?: '-' }}</td>
                                    <td class="text-center">
                                        @canany(['customer.payments.delete', 'customers.delete'])
                                            <form action="{{ route('customer.payments.destroy', $p->id) }}" method="POST" style="display:inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure to delete this payment?')">
                                                    <i class="fas fa-trash-alt"></i> Delete
                                                </button>
                                            </form>
                                        @endcanany
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">No payment records found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form action="{{ route('customer.payments.store') }}" method="POST">
            @csrf
            <div class="modal-content shadow border-0" style="border-radius: 12px; overflow: hidden;">
                <div class="modal-header bg-primary text-white py-3">
                    <h5 class="modal-title text-white font-weight-bold" id="paymentModalLabel">
                        <i class="fas fa-money-bill-wave mr-1"></i> Add Customer Payment
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" onclick="closePaymentModal()" aria-label="Close" style="opacity: 0.9;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="form-group mb-3">
                        <label class="font-weight-bold">Select Customer <span class="text-danger">*</span></label>
                        <select name="customer_id" id="payment_customer_id" class="form-control" required onchange="fetchCustomerBalance(this.value)">
                            <option value="">-- Choose Customer --</option>
                            @foreach($customers as $c)
                            <option value="{{ $c->id }}">
                                {{ $c->customer_name }} {{ !empty($c->customer_mobile) ? '('.$c->customer_mobile.')' : '' }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold">Outstanding Balance (Udhar / Khata)</label>
                        <input type="text" id="customer_balance" class="form-control font-weight-bold" readonly placeholder="Customer select karein..." style="background-color: #f8fafc;">
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold">Adjustment Type <span class="text-danger">*</span></label>
                        <select name="adjustment_type" class="form-control" required>
                            <option value="minus" selected>- Minus (Payment Received / Wasooli)</option>
                            <option value="plus">+ Plus (Outstanding Increased / Udhar Izafa)</option>
                        </select>
                        <small class="text-muted d-block mt-1">Payment received hone par <strong>Minus</strong> rakhein taake udhar kam ho.</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label class="font-weight-bold">Payment Date <span class="text-danger">*</span></label>
                            <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label class="font-weight-bold">Amount (Rs.) <span class="text-danger">*</span></label>
                            <input type="number" name="amount" step="any" min="0.01" class="form-control font-weight-bold text-success" placeholder="0.00" required>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold">Payment Method</label>
                        <select name="payment_method" class="form-control">
                            <option value="Cash" selected>Cash</option>
                            <option value="Bank / Card">Bank / Card</option>
                            <option value="Online / EasyPaisa / JazzCash">Online / EasyPaisa / JazzCash</option>
                            <option value="Cheque">Cheque</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div class="form-group mb-2">
                        <label class="font-weight-bold">Note / Remarks</label>
                        <textarea name="note" class="form-control" rows="2" placeholder="Optional notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal" onclick="closePaymentModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary font-weight-bold"><i class="fas fa-check mr-1"></i> Save Payment</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function openPaymentModal() {
        clearPaymentForm();
        if (window.jQuery && typeof $('#paymentModal').modal === 'function') {
            $('#paymentModal').modal('show');
        } else {
            var m = document.getElementById('paymentModal');
            if (m) {
                m.classList.add('show');
                m.style.display = 'block';
                m.removeAttribute('aria-hidden');
                if (!document.querySelector('.modal-backdrop')) {
                    var bd = document.createElement('div');
                    bd.className = 'modal-backdrop fade show';
                    document.body.appendChild(bd);
                }
                document.body.classList.add('modal-open');
            }
        }
    }

    function closePaymentModal() {
        if (window.jQuery && typeof $('#paymentModal').modal === 'function') {
            $('#paymentModal').modal('hide');
        } else {
            var m = document.getElementById('paymentModal');
            if (m) {
                m.classList.remove('show');
                m.style.display = 'none';
                m.setAttribute('aria-hidden', 'true');
                var bd = document.querySelector('.modal-backdrop');
                if (bd) bd.remove();
                document.body.classList.remove('modal-open');
            }
        }
    }

    function clearPaymentForm() {
        $('#paymentModal select[name="customer_id"]').val('');
        $('#paymentModal input[name="payment_date"]').val('{{ date('Y-m-d') }}');
        $('#paymentModal input[name="amount"]').val('');
        $('#paymentModal select[name="adjustment_type"]').val('minus');
        $('#paymentModal select[name="payment_method"]').val('Cash');
        $('#paymentModal textarea[name="note"]').val('');
        $('#customer_balance').val('').css('color', '#1e293b');
    }

    function fetchCustomerBalance(customerId) {
        if (!customerId) {
            $('#customer_balance').val('').css('color', '#1e293b');
            return;
        }
        $('#customer_balance').val('Loading balance...').css('color', '#64748b');
        $.ajax({
            url: '{{ url("/customer/ledger") }}/' + customerId,
            method: 'GET',
            success: function(response) {
                if (response && response.closing_balance !== undefined) {
                    var bal = parseFloat(response.closing_balance) || 0;
                    var sign = bal > 0 ? 'Rs. ' : (bal < 0 ? '-Rs. ' : 'Rs. ');
                    var display = sign + Math.abs(bal).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
                    if (bal > 0) {
                        display += ' (Udhar / Payable)';
                        $('#customer_balance').css('color', '#dc2626');
                    } else if (bal < 0) {
                        display += ' (Advance)';
                        $('#customer_balance').css('color', '#16a34a');
                    } else {
                        display += ' (Settled / 0.00)';
                        $('#customer_balance').css('color', '#475569');
                    }
                    $('#customer_balance').val(display);
                } else {
                    $('#customer_balance').val('Rs. 0.00').css('color', '#475569');
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", error);
                $('#customer_balance').val('Rs. 0.00').css('color', '#475569');
            }
        });
    }

    $(document).ready(function() {
        if ($.fn.DataTable && !$.fn.DataTable.isDataTable('.datanew')) {
            $('.datanew').DataTable();
        }
    });
</script>
@endpush