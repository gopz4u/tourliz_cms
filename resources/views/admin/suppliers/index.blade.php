@extends('layouts.admin')

@section('title', $filterType ? $filterType . ' Suppliers' : 'Suppliers & Vendors')

@section('content')
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2>
                    <i class="bi bi-shop me-2"></i>
                    @if($filterType)
                        <span class="badge text-white me-1" style="background:var(--primary); font-size:0.85rem;">{{ $filterType }}</span>
                    @endif
                    Suppliers & Vendors
                </h2>
                <p class="text-muted mb-0">Manage your network of hotels, transport, and activity partners.</p>
            </div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createSupplierModal">
                <i class="bi bi-plus-lg me-1"></i> Add Supplier
            </button>
        </div>
    </div>

    {{-- Category filter tabs --}}
    <div class="mb-3 d-flex flex-wrap gap-2">
        <a href="{{ route('admin.suppliers.index') }}" class="btn btn-sm {{ !$filterType ? 'btn-primary' : 'btn-outline-secondary' }}">
            <i class="bi bi-grid"></i> All
        </a>
        <a href="{{ route('admin.suppliers.index') }}?type=Hotel" class="btn btn-sm {{ $filterType == 'Hotel' ? 'btn-primary' : 'btn-outline-secondary' }}">
            <i class="bi bi-building"></i> Hotel
        </a>
        <a href="{{ route('admin.suppliers.index') }}?type=Transport" class="btn btn-sm {{ $filterType == 'Transport' ? 'btn-warning text-white' : 'btn-outline-secondary' }}">
            <i class="bi bi-truck"></i> Transport
        </a>
        <a href="{{ route('admin.suppliers.index') }}?type=Activity" class="btn btn-sm {{ $filterType == 'Activity' ? 'btn-success' : 'btn-outline-secondary' }}">
            <i class="bi bi-lightning"></i> Activity
        </a>
        <a href="{{ route('admin.suppliers.index') }}?type=Agent" class="btn btn-sm {{ $filterType == 'Agent' ? 'btn-info text-white' : 'btn-outline-secondary' }}">
            <i class="bi bi-person-badge"></i> Agent
        </a>
        <a href="{{ route('admin.suppliers.index') }}?type=Other" class="btn btn-sm {{ $filterType == 'Other' ? 'btn-secondary' : 'btn-outline-secondary' }}">
            <i class="bi bi-three-dots"></i> Other
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="suppliersTable">
                    <thead class="bg-light">
                        <tr>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Contact</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Populated via JS/AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <div class="modal fade" id="createSupplierModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Supplier</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.suppliers.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Company Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label">Type</label>
                                <select name="type" class="form-select" required>
                                    <option value="Hotel">Hotel</option>
                                    <option value="Transport">Transport</option>
                                    <option value="Activity">Activity / Tickets</option>
                                    <option value="Agent">Agent / Partner</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Destination (City)</label>
                                <select name="destination_id" class="form-select">
                                    <option value="">Global / All</option>
                                    @foreach(\App\Models\Destination::orderBy('city')->get() as $dest)
                                        <option value="{{ $dest->id }}">{{ $dest->city }} ({{ $dest->name }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label">Contact Person</label>
                                <input type="text" name="contact_person" class="form-control">
                            </div>
                            <div class="col-6">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" class="form-control">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control">
                        </div>

                        <h6 class="mt-4 mb-3 border-bottom pb-2">Banking Details (Optional)</h6>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label small text-muted">Bank Name</label>
                                <input type="text" name="bank_name" class="form-control form-control-sm">
                            </div>
                            <div class="col-6">
                                <label class="form-label small text-muted">Swift / IFSC</label>
                                <input type="text" name="swift_ifsc" class="form-control form-control-sm">
                            </div>
                            <div class="col-12">
                                <label class="form-label small text-muted">Account Number</label>
                                <input type="text" name="account_number" class="form-control form-control-sm">
                            </div>
                            <div class="col-12">
                                <label class="form-label small text-muted">Account Name</label>
                                <input type="text" name="account_name" class="form-control form-control-sm">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Supplier</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal Template (Dynamic) -->
    <div class="modal fade" id="editSupplierModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Supplier</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="editSupplierBody">
                    <!-- Loaded via AJAX -->
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                loadSuppliersTable();
            });

            function loadSuppliersTable() {
                const tbody = document.querySelector('#suppliersTable tbody');
                tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4"><div class="spinner-border text-primary"></div></td></tr>';

                const filterType = '{{ $filterType }}';
                const url = filterType ? `{{ route('admin.suppliers.index') }}?type=${filterType}` : '{{ route("admin.suppliers.index") }}';

                fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(res => res.json())
                    .then(data => {
                        tbody.innerHTML = '';
                        if (data.length === 0) {
                            tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-muted">No suppliers found. Add one to get started.</td></tr>';
                            return;
                        }

                        data.forEach(s => {
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                    <td class="fw-bold">${s.name}</td>
                                    <td><span class="badge bg-light text-dark border">${s.type}</span></td>
                                    <td>
                                        <div class="small">${s.contact_person || '-'}</div>
                                        <div class="text-muted small">${s.phone || ''}</div>
                                    </td>
                                    <td>${s.destination_id ? '<span class="badge bg-info bg-opacity-10 text-info">Specific</span>' : '<span class="badge bg-secondary bg-opacity-10 text-secondary">Global</span>'}</td>
                                    <td>
                                        ${s.is_active ? '<span class="status-badge bg-success">Active</span>' : '<span class="status-badge bg-danger">Inactive</span>'}
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-link text-primary" onclick="editSupplier(${s.id})"><i class="bi bi-pencil"></i></button>
                                        <button class="btn btn-sm btn-link text-danger" onclick="deleteSupplier(${s.id})"><i class="bi bi-trash"></i></button>
                                    </td>
                                `;
                            tbody.appendChild(tr);
                        });
                    });
            }

            function deleteSupplier(id) {
                if (!confirm('Delete this supplier?')) return;

                fetch(`/admin/suppliers/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                }).then(res => res.json()).then(data => {
                    if (data.success) loadSuppliersTable();
                });
            }

            function editSupplier(id) {
                // Ideally load a form via AJAX or reuse the create form populated with data
                // For now, let's keep it simple as the user didn't ask for full edit implementation yet
                alert('Edit functionality can be implemented similar to create.');
            }
        </script>
    @endpush
@endsection