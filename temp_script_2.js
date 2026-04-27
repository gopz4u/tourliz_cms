
        document.addEventListener('DOMContentLoaded', function () {
            loadExpenses();
            // loadSuppliers(); // Moved to loadExpenses success callback to avoid race condition
            // Sync Pax to items
            const syncPax = () => {
                const adults = parseInt(document.getElementById('pax-adults').value || 1);
                const childS = parseInt(document.getElementById('pax-child-small').value || 0);
                const childL = parseInt(document.getElementById('pax-child-large').value || 0);

                itinerary.forEach(day => {
                    const keys = ['activities', 'places', 'transport', 'meals', 'hotels'];
                    keys.forEach(k => {
                        if (day[k]) {
                            day[k].forEach(item => {
                                if (item.entry_ticket) {
                                    item.entry_ticket.adult_qty = adults;
                                    item.entry_ticket.child_2_6_qty = childS;
                                    item.entry_ticket.child_6_11_qty = childL;
                                }
                                if (k === 'meals') {
                                    item.quantity = adults;
                                }
                            });
                        }
                    });
                });
                renderBuilder();
            };

            document.getElementById('pax-adults').addEventListener('change', syncPax);
            document.getElementById('pax-child-small').addEventListener('change', syncPax);
            document.getElementById('pax-child-large').addEventListener('change', syncPax);
            document.getElementById('markup-percentage').addEventListener('change', calculateDynamicTotal);
            document.getElementById('trip-duration').addEventListener('change', calculateDynamicTotal);
        });

        function loadSuppliers() {
            $.get(" "BLADE_VAR" ", { place_id: " "BLADE_VAR" " }, function (data) {
                allSuppliers = data; // Store globally

                // Initialize filtered dropdown based on default/current category
                const currentCat = $('#expense-category').val();
                filterSuppliersByCategory(currentCat);

                // Populate Main proposal checkboxes (Sidebar) - Keep all
                const checkboxContainer = $('#vendor-checkboxes');
                checkboxContainer.empty();

                const currentVendorId = parseInt(" "BLADE_VAR" ");

                data.forEach(s => {
                    const isChecked = ((s.id == currentVendorId) || (currentExpenses && currentExpenses.some(e => e.supplier_id == s.id))) ? 'checked' : '';
                    const cbHtml = `
                                                                                                                <div class="form-check small mb-1">
                                                                                                                    <input class="form-check-input vendor-cb" type="checkbox" value="${s.id}" id="vcb-${s.id}" ${isChecked}>
                                                                                                                    <label class="form-check-label text-truncate d-block" for="vcb-${s.id}" title="${s.name}">
                                                                                                                        ${s.name} <span class="text-muted" style="font-size: 0.6rem;">(${s.type})</span>
                                                                                                                    </label>
                                                                                                                </div>
                                                                                                            `;
                    checkboxContainer.append(cbHtml);
                });

                if (data.length === 0) {
                    checkboxContainer.html('<div class="text-muted small py-1">No vendors found</div>');
                }
            });
        }

        function filterSuppliersByCategory(category) {
            const expenseSelect = $('#expense-supplier-id');
            const currentVal = expenseSelect.val(); // Preserve if possible
            expenseSelect.find('option:not(:first)').remove(); // Keep default Select Partner option

            if (!allSuppliers || allSuppliers.length === 0) return;

            // Map Category names to Supplier Types if needed, or loosely match
            // Categories: Hotel, Transport, Activity, Meal, Agent, Other
            // Supplier Types (DB): Hotel, Transport, Activity, Agent, Restaurant, etc.

            const filtered = allSuppliers.filter(s => {
                const sType = (s.type || '').toLowerCase();
                const cat = (category || '').toLowerCase();

                if (cat === 'hotel') return sType === 'hotel';
                if (cat === 'transport') return sType === 'transport' || sType === 'driver' || sType === 'taxi';
                if (cat === 'activity') return sType === 'activity' || sType === 'attraction';
                if (cat === 'meal') return sType === 'restaurant' || sType === 'meal';
                if (cat === 'agent') return sType === 'agent';
                // For 'Other' or unmatched, maybe show all? Or just 'Other' types?
                // Let's show all if 'Other' is selected to be safe, or just specific ones?
                // User asked "based upon vendor service", implies strict filtering.
                if (cat === 'other') return true;

                return false;
            });

            // If strict filtering yields nothing, or if logic fails, maybe show all? 
            // Let's stick to strict but fallback to showing all if 'Other' or empty?
            // Actually, let's append matching ones.

            (filtered.length > 0 ? filtered : allSuppliers).forEach(s => {
                expenseSelect.append(`<option value="${s.id}">${s.name} (${s.type})</option>`);
            });

            if (currentVal) expenseSelect.val(currentVal);

            // Also call toggleVehicleTypeField for the category change
            toggleVehicleTypeField(category);
        }

        // Event listener for category change
        $(document).on('change', '#expense-category', function () {
            filterSuppliersByCategory(this.value);
        });

        function quickSaveSupplier() {
            const formData = $('#quickSupplierForm').serialize();
            $.post(" "BLADE_VAR" ", formData, function (res) {
                if (res.success) {
                    $('#newSupplierModal').modal('hide');
                    $('#expenseModal').modal('show');
                    $('#quickSupplierForm')[0].reset();
                    loadSuppliers();
                }
            });
        }



        function downloadVendorPdf(id) {
            window.open(`/admin/expenses/${id}/pdf-vendor`, '_blank');
        }

        function loadExpenses() {
            $.get(" "BLADE_VAR" ", {
                itinerary_id: " "BLADE_VAR" ",
                itinerary_type: 'b2b'
            }, function (data) {
                currentExpenses = data; // Store for Modal

                const tbody = $('#expense-table tbody');
                tbody.empty();
                let total = 0;

                data.forEach(exp => {
                    total += parseFloat(exp.amount);
                    const vendorName = exp.supplier ? `<br><small class="text-primary fw-bold">${exp.supplier.name}</small>` : '';

                    let actions = '';
                    if (exp.supplier) {
                        actions = `
                                                                                                                                                                                                                                                                                                                                                                    <button class="btn btn-link text-success p-0 me-2" onclick="shareVendorWhatsapp(${exp.id})" title="WhatsApp Vendor">
                                                                                                                                                                                                                                                                                                                                                                        <i class="bi bi-whatsapp"></i>
                                                                                                                                                                                                                                                                                                                                                                    </button>
                                                                                                                                                                                                                                                                                                                                                                    <button class="btn btn-link text-danger p-0 me-2" onclick="downloadVendorPdf(${exp.id})" title="PDF Voucher">
                                                                                                                                                                                                                                                                                                                                                                        <i class="bi bi-file-pdf"></i>
                                                                                                                                                                                                                                                                                                                                                                    </button>
                                                                                                                                                                                                                                                                                                                                                                 `;
                    }

                    tbody.append(`
                                                                                                                                                                                                                                                                                                                                                                            <tr>
                                                                                                                                                                                                                                                                                                                                                                                <td>
                                                                                                                                                                                                                                                                                                                                                                                    <div class="fw-bold" style="font-size: 0.75rem;">${exp.category}${vendorName}</div>
                                                                                                                                                                                                                                                                                                                                                                                    <div class="text-muted" style="font-size: 0.65rem;">${exp.description || ''}</div>
                                                                                                                                                                                                                                                                                                                                                                                </td>
                                                                                                                                                                                                                                                                                                                                                                                <td class="text-end fw-bold text-danger">-${exp.amount}</td>
                                                                                                                                                                                                                                                                                                                                                                                <td class="text-end">
                                                                                                                                                                                                                                                                                                                                                                                    ${actions}
                                                                                                                                                                                                                                                                                                                                                                                    <button class="btn btn-link text-muted p-0" onclick="deleteExpense(${exp.id})" title="Remove Cost">
                                                                                                                                                                                                                                                                                                                                                                                        <i class="bi bi-trash"></i>
                                                                                                                                                                                                                                                                                                                                                                                    </button>
                                                                                                                                                                                                                                                                                                                                                                                </td>
                                                                                                                                                                                                                                                                                                                                                                            </tr>
                                                                                                                                                                                                                                                                                                                                                                        `);
                });

                if (data.length === 0) {
                    tbody.append('<tr><td colspan="3" class="text-center text-muted py-2 small">No costs recorded</td></tr>');
                }

                updateFinancialSummary(total);
                loadSuppliers(); // Refresh checkboxes based on loaded expenses
            });
        }

        function saveExpense() {
            const formData = $('#expenseForm').serialize();
            $.post(" "BLADE_VAR" ", formData, function (res) {
                if (res.success) {
                    $('#expenseModal').modal('hide');
                    $('#expenseForm')[0].reset();
                    loadExpenses();
                }
            }).fail(function (xhr) {
                alert('Error: ' + (xhr.responseJSON?.message || 'Check inputs'));
            });
        }

        function deleteExpense(id) {
            if (!confirm('Remove this cost entry?')) return;
            $.ajax({
                url: `/admin/expenses/${id}`,
                type: 'DELETE',
                success: function (res) {
                    loadExpenses();
                }
            });
        }

        function updateFinancialSummary(actualCost) {
            const totalQuoted = parseFloat(" "BLADE_VAR" ");
            const profit = totalQuoted - actualCost;
            const currencyStr = " "BLADE_VAR"  ";

            $('#total-actual-cost').text(currencyStr + actualCost.toFixed(2));
            $('#summary-actual-cost').text(currencyStr + actualCost.toFixed(2));
            $('#actual-profit').text(currencyStr + profit.toFixed(2));

            const profitEl = $('#actual-profit');
            const marginEl = $('#actual-margin');
            const margin = totalQuoted > 0 ? (profit / totalQuoted) * 100 : 0;

            marginEl.text(margin.toFixed(2) + '%');

            if (profit >= 0) {
                profitEl.removeClass('text-danger text-dark').addClass('text-success');
                marginEl.removeClass('text-danger').addClass('text-success');
            } else {
                profitEl.removeClass('text-success text-dark').addClass('text-danger');
                marginEl.removeClass('text-success').addClass('text-danger');
            }
        }

        function toggleVehicleTypeField(category) {
            const field = document.getElementById('vehicle-type-field');
            if (category === 'Transport') {
                field.classList.remove('d-none');
            } else {
                field.classList.add('d-none');
            }
        }
    