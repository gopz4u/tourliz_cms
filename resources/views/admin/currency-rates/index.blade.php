@extends('layouts.admin')

@section('title', 'Currency Exchange Rates')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1 class="mb-0"><i class="bi bi-currency-exchange"></i> Currency Exchange Rates</h1>
        <p class="text-muted mb-0">Manage currency exchange rates (Base Currency: MYR)</p>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> <strong>Base Currency:</strong> MYR (Malaysian Ringgit). All exchange rates are relative to 1 Unit of the currency.
            <br>Example: If INR rate is 0.0571, it means 1 INR = 0.0571 MYR. If USD rate is 4.74, it means 1 USD = 4.74 MYR.
        </div>
        
        <form id="currency-rates-form">
            <div class="table-responsive">
                <table class="table table-hover" id="rates-table">
                    <thead>
                        <tr>
                            <th>Currency Code</th>
                            <th>Currency Name</th>
                            <th>Value in MYR (for 1 unit)</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="5" class="text-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Save All Changes
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        loadRates();
    });
    
    function loadRates() {
        $.ajax({
            url: '{{ route("admin.currency-rates.index") }}',
            type: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            success: function(response) {
                const rates = response.data || response;
                const tbody = $('#rates-table tbody');
                tbody.empty();
                
                if (!Array.isArray(rates) || rates.length === 0) {
                    tbody.append('<tr><td colspan="5" class="text-center">No currency rates found</td></tr>');
                    return;
                }
                
                rates.forEach(function(rate) {
                    const code = rate.code || rate.currency_code;
                    const name = rate.name || rate.currency_name;
                    const exchange_rate = rate.exchange_rate || rate.rate_to_inr || rate.rate || 1.0;
                    
                    const isBaseCurrency = code === 'MYR';
                    const row = `
                        <tr>
                            <td><strong>${code || 'N/A'}</strong>${isBaseCurrency ? ' <span class="badge bg-success">Base</span>' : ''}</td>
                            <td>${name || '-'}</td>
                            <td>
                                ${isBaseCurrency ? 
                                    '<input type="number" step="0.0001" class="form-control" value="1.0000" readonly style="background-color: #e9ecef;">' :
                                    `<input type="number" step="0.0001" class="form-control rate-input" data-id="${rate.id}" value="${exchange_rate}" name="rates[${rate.id}][exchange_rate]">`
                                }
                            </td>
                            <td>
                                ${isBaseCurrency ? 
                                    '<span class="badge bg-success">Always Active</span>' :
                                    `<div class="form-check form-switch">
                                        <input class="form-check-input status-switch" type="checkbox" data-id="${rate.id}" ${rate.is_active ? 'checked' : ''}>
                                    </div>`
                                }
                            </td>
                            <td>
                                ${isBaseCurrency ? 
                                    '<span class="text-muted">Cannot delete base currency</span>' :
                                    `<button type="button" onclick="deleteRate(${rate.id})" class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>`
                                }
                            </td>
                        </tr>
                    `;
                    tbody.append(row);
                });
            },
            error: function(xhr) {
                console.error('Error loading rates:', xhr);
                $('#rates-table tbody').html('<tr><td colspan="5" class="text-center text-danger">Error loading currency rates</td></tr>');
            }
        });
    }
    
    // Handle status toggle
    $(document).on('change', '.status-switch', function() {
        const rateId = $(this).data('id');
        const isActive = $(this).is(':checked') ? 1 : 0;
        
        $.ajax({
            url: `/admin/currency-rates/${rateId}`,
            type: 'PUT',
            data: {
                is_active: isActive,
                _method: 'PUT'
            },
            success: function() {
                // Optionally show success message
            },
            error: function() {
                alert('Error updating currency status');
                // Reload to revert
                loadRates();
            }
        });
    });
    
    $('#currency-rates-form').on('submit', function(e) {
        e.preventDefault();
        
        const rates = [];
        $('.rate-input').each(function() {
            const id = $(this).data('id');
            const rate = $(this).val();
            if (id && rate) {
                rates.push({
                    id: id,
                    exchange_rate: parseFloat(rate)
                });
            }
        });
        
        if (rates.length === 0) {
            alert('No rates to update');
            return;
        }
        
        $.ajax({
            url: '{{ route("admin.currency-rates.bulk-update") }}',
            type: 'POST',
            data: {
                rates: rates
            },
            success: function() {
                alert('Exchange rates updated successfully!');
                loadRates();
            },
            error: function(xhr) {
                let errorMsg = 'Error updating rates';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                alert(errorMsg);
            }
        });
    });
    
    function deleteRate(id) {
        if (!confirm('Are you sure you want to delete this currency rate?')) return;
        
        $.ajax({
            url: `/admin/currency-rates/${id}`,
            type: 'DELETE',
            success: function() {
                loadRates();
                alert('Currency rate deleted successfully');
            },
            error: function(xhr) {
                let errorMsg = 'Error deleting rate';
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMsg = xhr.responseJSON.error;
                }
                alert(errorMsg);
            }
        });
    }
</script>
@endpush
@endsection

