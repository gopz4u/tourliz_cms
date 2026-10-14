@extends('layouts.admin')

@section('title', 'Manual Currency Rates')

@section('content')
<div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h1 class="mb-1"><i class="bi bi-currency-exchange text-primary"></i> Manual Currency Rates</h1>
        <p class="text-muted mb-0">Control the rates used by B2C and B2B customer proposals.</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCurrencyModal">
        <i class="bi bi-plus-circle me-1"></i> Add Currency
    </button>
</div>

<div class="alert alert-primary border-0 shadow-sm">
    <div class="d-flex gap-3">
        <i class="bi bi-info-circle-fill fs-4"></i>
        <div>
            <strong>Enter the MYR value of one unit of currency.</strong>
            <div class="small mt-1">Example: USD 4.740000 means <b>1 USD = MYR 4.74</b>. A proposal converts MYR to USD using the reciprocal rate. Existing saved proposals keep their locked rate; changing this table affects a proposal when its currency is selected again.</div>
        </div>
    </div>
</div>

<div id="rate-alert"></div>
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <form id="currency-rates-form">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="rates-table">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Currency</th>
                            <th>Name</th>
                            <th style="min-width:220px">1 Currency Unit = MYR</th>
                            <th>Proposal conversion</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody><tr><td colspan="6" class="text-center py-5"><div class="spinner-border text-primary"></div></td></tr></tbody>
                </table>
            </div>
            <div class="p-3 border-top d-flex justify-content-end">
                <button type="submit" class="btn btn-success" id="save-rates"><i class="bi bi-check2-circle me-1"></i> Save Manual Rates</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="addCurrencyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
        <form id="add-currency-form">
            <div class="modal-header"><h5 class="modal-title">Add Currency</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-4"><label class="form-label">Code</label><input name="code" class="form-control text-uppercase" maxlength="3" placeholder="USD" required></div>
                    <div class="col-8"><label class="form-label">Currency name</label><input name="name" class="form-control" placeholder="US Dollar"></div>
                    <div class="col-12"><label class="form-label">Value in MYR</label><div class="input-group"><span class="input-group-text">1 unit = MYR</span><input name="exchange_rate" type="number" min="0.00000001" step="0.00000001" class="form-control" required></div></div>
                    <div class="col-12"><div class="form-check form-switch"><input name="is_active" value="1" class="form-check-input" type="checkbox" checked id="new-active"><label class="form-check-label" for="new-active">Available in currency selectors</label></div></div>
                </div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary" type="submit">Add Currency</button></div>
        </form>
    </div></div>
</div>
@endsection

@push('scripts')
<script>
const ratesIndexUrl = @json(route('admin.currency-rates.index'));
const ratesBulkUrl = @json(route('admin.currency-rates.bulk-update'));
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

function escapeHtml(value) { return $('<div>').text(value ?? '').html(); }
function showRateAlert(message, type = 'success') {
    $('#rate-alert').html(`<div class="alert alert-${type} alert-dismissible fade show"><i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-1"></i>${escapeHtml(message)}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>`);
}
function errorMessage(xhr) {
    const errors = xhr.responseJSON?.errors;
    return errors ? Object.values(errors).flat().join(' ') : (xhr.responseJSON?.message || xhr.responseJSON?.error || 'Unable to complete this action.');
}
function loadRates() {
    $.ajax({url: ratesIndexUrl, headers: {'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}}).done(response => {
        const rates = response.data || response;
        const tbody = $('#rates-table tbody').empty();
        if (!Array.isArray(rates) || !rates.length) return tbody.html('<tr><td colspan="6" class="text-center py-5 text-muted">No currencies found.</td></tr>');
        rates.forEach(rate => {
            const code = String(rate.code || rate.currency_code || '').toUpperCase();
            const value = Number(rate.exchange_rate || rate.rate_to_inr || 1);
            const base = code === 'MYR';
            const proposalRate = base ? 1 : (1 / value);
            tbody.append(`<tr>
                <td class="ps-4"><strong>${escapeHtml(code)}</strong> ${base ? '<span class="badge bg-primary ms-1">Base</span>' : ''}</td>
                <td>${escapeHtml(rate.name || rate.currency_name || '—')}</td>
                <td><div class="input-group input-group-sm"><span class="input-group-text">MYR</span><input type="number" min="0.00000001" step="0.00000001" class="form-control rate-input" data-id="${rate.id}" value="${base ? '1.00000000' : value.toFixed(8)}" ${base ? 'readonly' : ''}></div></td>
                <td><small class="text-muted">1 MYR = <b>${proposalRate.toFixed(6)} ${escapeHtml(code)}</b></small></td>
                <td>${base ? '<span class="badge bg-success">Always active</span>' : `<div class="form-check form-switch"><input class="form-check-input status-switch" type="checkbox" data-id="${rate.id}" ${rate.is_active ? 'checked' : ''}></div>`}</td>
                <td class="text-end pe-4">${base ? '' : `<button type="button" class="btn btn-sm btn-outline-danger delete-rate" data-id="${rate.id}" data-code="${escapeHtml(code)}"><i class="bi bi-trash"></i></button>`}</td>
            </tr>`);
        });
    }).fail(xhr => showRateAlert(errorMessage(xhr), 'danger'));
}

$(function() { loadRates(); });

$('#currency-rates-form').on('submit', function(e) {
    e.preventDefault();
    const rates = $('.rate-input').map(function(){ return {id: $(this).data('id'), exchange_rate: Number($(this).val())}; }).get();
    const button = $('#save-rates').prop('disabled', true);
    $.post(ratesBulkUrl, {_token: csrfToken, rates}).done(response => { showRateAlert(response.message); loadRates(); }).fail(xhr => showRateAlert(errorMessage(xhr), 'danger')).always(() => button.prop('disabled', false));
});

$(document).on('change', '.status-switch', function() {
    const input = $(this);
    $.ajax({url: `${ratesIndexUrl}/${input.data('id')}`, type:'PUT', data:{_token:csrfToken, is_active:input.is(':checked') ? 1 : 0}})
        .done(() => showRateAlert('Currency status updated.')).fail(xhr => { input.prop('checked', !input.is(':checked')); showRateAlert(errorMessage(xhr), 'danger'); });
});

$('#add-currency-form').on('submit', function(e) {
    e.preventDefault();
    const form = $(this);
    $.post(ratesIndexUrl, form.serialize() + '&_token=' + encodeURIComponent(csrfToken)).done(() => {
        bootstrap.Modal.getInstance(document.getElementById('addCurrencyModal')).hide(); form[0].reset(); $('#new-active').prop('checked', true); showRateAlert('Currency added successfully.'); loadRates();
    }).fail(xhr => showRateAlert(errorMessage(xhr), 'danger'));
});

$(document).on('click', '.delete-rate', function() {
    const id = $(this).data('id'), code = $(this).data('code');
    if (!confirm(`Delete ${code} from the currency list?`)) return;
    $.ajax({url:`${ratesIndexUrl}/${id}`, type:'DELETE', data:{_token:csrfToken}}).done(() => { showRateAlert(`${code} deleted.`); loadRates(); }).fail(xhr => showRateAlert(errorMessage(xhr), 'danger'));
});
</script>
@endpush
