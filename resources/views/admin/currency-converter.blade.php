@extends('layouts.admin')

@section('title', 'Currency Converter Tool')

@section('content')
<div class="page-header">
    <h1 class="mb-0"><i class="bi bi-calculator"></i> Currency Converter</h1>
    <p class="text-muted mb-0">Real-time conversion based on MYR base rates</p>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header bg-transparent">
                <h5 class="mb-0">Convert Amount</h5>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <label class="form-label text-muted small text-uppercase fw-bold">From Currency</label>
                    <div class="input-group input-group-lg">
                        <select class="form-select" id="from-currency" style="max-width: 120px; border-radius: 12px 0 0 12px;">
                            @foreach($rates as $rate)
                                @php
                                    $code = $rate->code ?? $rate->currency_code;
                                    $rateVal = $rate->exchange_rate ?? $rate->rate_to_inr ?? 1.0;
                                @endphp
                                <option value="{{ $code }}" data-rate="{{ $rateVal }}" {{ $code == 'MYR' ? 'selected' : '' }}>
                                    {{ $code }}
                                </option>
                            @endforeach
                        </select>
                        <input type="number" class="form-control" id="from-amount" value="100.00" step="0.01" style="border-radius: 0 12px 12px 0;">
                    </div>
                </div>

                <div class="text-center mb-4">
                    <button class="btn btn-outline-primary rounded-circle p-3" id="swap-currencies">
                        <i class="bi bi-arrow-down-up fs-4"></i>
                    </button>
                </div>

                <div class="mb-4">
                    <label class="form-label text-muted small text-uppercase fw-bold">To Currency</label>
                    <div class="input-group input-group-lg">
                        <select class="form-select" id="to-currency" style="max-width: 120px; border-radius: 12px 0 0 12px;">
                            @foreach($rates as $rate)
                                @php
                                    $code = $rate->code ?? $rate->currency_code;
                                    $rateVal = $rate->exchange_rate ?? $rate->rate_to_inr ?? 1.0;
                                @endphp
                                <option value="{{ $code }}" data-rate="{{ $rateVal }}" {{ $code == 'INR' ? 'selected' : '' }}>
                                    {{ $code }}
                                </option>
                            @endforeach
                        </select>
                        <input type="text" class="form-control fw-bold bg-light" id="to-amount" readonly style="border-radius: 0 12px 12px 0;">
                    </div>
                </div>

                <div class="alert alert-primary border-0 rounded-4 p-4 mb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-uppercase small fw-bold opacity-75 mb-1">Current Rate</div>
                            <h4 class="mb-0" id="rate-display">1 MYR = 17.51 INR</h4>
                        </div>
                        <div class="text-end">
                            <div class="text-uppercase small fw-bold opacity-75 mb-1">Last Updated</div>
                            <div class="small">{{ now()->format('d M Y, H:i') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header bg-transparent">
                <h5 class="mb-0">Quick Multi-Currency Preview (Base: MYR)</h5>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <label class="form-label text-muted small text-uppercase fw-bold">Base Amount (MYR)</label>
                    <div class="input-group input-group-lg">
                        <span class="input-group-text bg-primary text-white border-0" style="border-radius: 12px 0 0 12px;">RM</span>
                        <input type="number" class="form-control" id="base-myr-amount" value="100.00" step="0.01" style="border-radius: 0 12px 12px 0;">
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="quick-preview-table">
                        <thead>
                            <tr>
                                <th>Currency</th>
                                <th class="text-end">Amount</th>
                                <th class="text-end">Rate (to MYR)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rates as $rate)
                                @php
                                    $code = $rate->code ?? $rate->currency_code;
                                    $name = $rate->name ?? $rate->currency_name;
                                    $rateVal = $rate->exchange_rate ?? $rate->rate_to_inr ?? 1.0;
                                @endphp
                                @if($code != 'MYR')
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="fs-4 me-2">{{ $rate->flag_emoji }}</span>
                                                <div>
                                                    <div class="fw-bold">{{ $code }}</div>
                                                    <div class="small text-muted">{{ $name }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end fw-bold text-primary preview-amount" data-rate="{{ $rateVal }}">
                                            {{ number_format(100 / $rateVal, 2) }}
                                        </td>
                                        <td class="text-end text-muted small">
                                            1 {{ $code }} = {{ $rateVal }} MYR
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Initial Calculation
        calculateConversion();
        updateQuickPreview();

        $('#from-amount, #from-currency, #to-currency').on('input change', function() {
            calculateConversion();
        });

        $('#base-myr-amount').on('input', function() {
            updateQuickPreview();
        });

        $('#swap-currencies').on('click', function() {
            const fromCurr = $('#from-currency').val();
            const toCurr = $('#to-currency').val();
            
            $('#from-currency').val(toCurr);
            $('#to-currency').val(fromCurr);
            
            calculateConversion();
        });

        function calculateConversion() {
            const amount = parseFloat($('#from-amount').val()) || 0;
            const fromRate = parseFloat($('#from-currency option:selected').data('rate')) || 1;
            const toRate = parseFloat($('#to-currency option:selected').data('rate')) || 1;
            
            // Logic: BaseValue = Amount * Rate, TargetValue = BaseValue / TargetRate
            const amountInMYR = amount * fromRate;
            const converted = amountInMYR / toRate;
            
            $('#to-amount').val(converted.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
            
            const unitRate = fromRate / toRate;
            $('#rate-display').text(`1 ${$('#from-currency').val()} = ${unitRate.toFixed(4)} ${$('#to-currency').val()}`);
        }

        function updateQuickPreview() {
            const myrAmount = parseFloat($('#base-myr-amount').val()) || 0;
            
            $('.preview-amount').each(function() {
                const rate = parseFloat($(this).data('rate')) || 1;
                const converted = myrAmount / rate;
                $(this).text(converted.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
            });
        }
    });
</script>
@endpush
@endsection
