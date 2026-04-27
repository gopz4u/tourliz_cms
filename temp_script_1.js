
        let currentExpenses = [];
        let allSuppliers = []; // Store suppliers for dropdown

        function openVendorShareModal() {
            const container = document.getElementById('vendor-list-container');
            container.innerHTML = '';

            // Collect vendors from expenses
            const expenseVendors = currentExpenses.filter(e => e.supplier).map(e => ({
                id: e.supplier.id,
                name: e.supplier.name,
                type: e.supplier.type,
                category: e.category,
                description: e.description,
                expenseId: e.id,
                status: e.status
            }));

            // Collect vendors from checkboxes
            const selectedVendors = [];
            $('.vendor-cb:checked').each(function () {
                const id = $(this).val();
                const label = $(this).next('label').text().trim();
                const name = label.split(' (')[0];
                const typeMatch = label.match(/\(([^)]+)\)/);
                const type = typeMatch ? typeMatch[1] : '';

                if (!expenseVendors.some(ev => ev.id == id)) {
                    selectedVendors.push({ id, name, type, category: 'General', description: 'Selected for Proposal' });
                }
            });

            const allVendors = [...expenseVendors, ...selectedVendors];

            if (allVendors.length === 0) {
                container.innerHTML = '<div class="p-4 text-center text-muted">No vendors selected. Choose vendors in the left sidebar or add expenses.</div>';
            } else {
                allVendors.forEach(vendor => {
                    let statusBadge = '';
                    if (vendor.status) {
                        let statusColor = 'secondary';
                        if (vendor.status === 'confirmed') statusColor = 'success';
                        if (vendor.status === 'requested') statusColor = 'warning';
                        if (vendor.status === 'rejected') statusColor = 'danger';
                        statusBadge = `<span class="badge bg-${statusColor} ms-1 text-uppercase" style="font-size: 0.65rem;">${vendor.status}</span>`;
                    }

                    const item = document.createElement('div');
                    item.className = 'list-group-item p-3 d-flex justify-content-between align-items-center';
                    item.innerHTML = `
                                                                                                                                                                                                                                                        <div style="flex:1">
                                                                                                                                                                                                                                                            <div class="fw-bold">${vendor.name} 
                                                                                                                            <div style="flex:1">
                                                                                                                                <div class="fw-bold">${vendor.name} <span class="badge bg-light text-dark border ms-1">${vendor.category || vendor.type}</span>${statusBadge}</div>
                                                                                                                                <div class="text-muted small">${vendor.description || ''}</div>
                                                                                                                            </div>
                                                                                                                            <div class="d-flex gap-2">
                                                                                                                                 <button class="btn btn-danger btn-sm" onclick="downloadVendorPdf(${vendor.expenseId})" title="PDF Voucher" ${!vendor.expenseId ? 'disabled' : ''}>
                                                                                                                                     <i class="bi bi-file-earmark-pdf"></i>
                                                                                                                                 </button>
                                                                                                                                 <button class="btn btn-success btn-sm" onclick="shareVendorWhatsapp(${vendor.expenseId || 0}, ${vendor.id})" title="WhatsApp">
                                                                                                                                    <i class="bi bi-whatsapp"></i>
                                                                                                                                </button>
                                                                                                                            </div>
                                                                                                                        `;
                    container.appendChild(item);
                });
            }

            new bootstrap.Modal(document.getElementById('vendorShareModal')).show();
        }

        function updateExpenseStatus(id, status) {
            $.ajax({
                url: `/admin/expenses/${id}`,
                type: 'PUT',
                data: { status: status },
                success: function (res) {
                    // Update local data
                    const idx = currentExpenses.findIndex(e => e.id == id);
                    if (idx !== -1) currentExpenses[idx].status = status;
                    // Re-render modal to toggle Change Vendor button if needed
                    // For simply changing select color, we might want full refresh, but let's just re-open/render for now or simple alert
                    // Ideally we refresh the list status badge.
                    // Let's close and re-open to refresh the view or just refresh list
                    openVendorShareModal();
                }
            });
        }

        let editingExpenseId = null;
        function openChangeSupplierModal(expenseId) {
            editingExpenseId = expenseId;
            const exp = currentExpenses.find(e => e.id == expenseId);

            // populate modal
            const modal = new bootstrap.Modal(document.getElementById('changeSupplierModal'));

            // Populate select options
            const select = document.getElementById('change-supplier-select');
            select.innerHTML = '<option value="">Select New Vendor</option>';
            allSuppliers.forEach(s => {
                select.innerHTML += `<option value="${s.id}">${s.name} (${s.type})</option>`;
            });

            modal.show();
        }

        function saveChangedSupplier() {
            const newSupplierId = document.getElementById('change-supplier-select').value;
            if (!newSupplierId) return alert('Select a supplier');

            $.ajax({
                url: `/admin/expenses/${editingExpenseId}`,
                type: 'PUT',
                data: { supplier_id: newSupplierId, status: 'pending' }, // Reset to pending
                success: function (res) {
                    location.reload(); // Simplest way to reflect everything including main list
                }
            });
        }
    