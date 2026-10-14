@extends('layouts.admin')
@section('title', $groupDeparture->title)
@section('content')
<div class="page-header d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div><div class="d-flex gap-2 mb-2"><span class="badge bg-primary">{{ strtoupper($groupDeparture->status) }}</span><span class="badge bg-light text-dark border">{{ $groupDeparture->departure_date->format('d M Y') }}</span></div><h2>{{ $groupDeparture->title }}</h2><p class="text-muted mb-0">Based on {{ $groupDeparture->fixedItinerary->title }}</p></div>
    <div class="d-flex gap-2"><a href="{{ route('admin.group-departures.itinerary',$groupDeparture) }}" class="btn btn-outline-danger"><i class="bi bi-download me-1"></i> Itinerary PDF</a><a href="{{ route('admin.group-departures.whatsapp',$groupDeparture) }}" class="btn btn-outline-success"><i class="bi bi-whatsapp me-1"></i>WhatsApp</a><a href="{{ route('admin.group-departures.edit',$groupDeparture) }}" class="btn btn-primary"><i class="bi bi-pencil me-1"></i>Edit Trip</a><a href="{{ route('admin.group-departures.index') }}" class="btn btn-outline-secondary">Back</a></div>
</div>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

<div class="row g-3 mb-3">
    <div class="col-md-3"><div class="card h-100"><div class="card-body"><div class="small text-muted">Adult rate</div><h4 class="text-primary mb-0">{{ $groupDeparture->currency }} {{ number_format($groupDeparture->adult_rate,2) }}</h4></div></div></div>
    <div class="col-md-3"><div class="card h-100"><div class="card-body"><div class="small text-muted">Child rate</div><h4 class="mb-0">{{ $groupDeparture->child_rate ? $groupDeparture->currency.' '.number_format($groupDeparture->child_rate,2) : 'Not set' }}</h4></div></div></div>
    <div class="col-md-3"><div class="card h-100"><div class="card-body"><div class="small text-muted">Booked</div><h4 class="mb-0">{{ $groupDeparture->booked_pax }} / {{ $groupDeparture->capacity }} seats</h4></div></div></div>
    <div class="col-md-3"><div class="card h-100"><div class="card-body"><div class="small text-muted">Available</div><h4 class="text-success mb-0">{{ $groupDeparture->available_seats }} seats</h4></div></div></div>
</div>

<div class="row g-3">
<div class="col-xl-8">
    <div class="card"><div class="card-header d-flex justify-content-between align-items-center"><strong><i class="bi bi-people me-2 text-primary"></i>Customer Roster</strong><span class="badge bg-light text-dark border">{{ $groupDeparture->customers->count() }} bookings</span></div><div class="table-responsive"><table class="table align-middle"><thead><tr><th>Customer</th><th>Pax / Rate</th><th>Payment</th><th>Booking</th><th>Balance</th><th></th></tr></thead><tbody>
    @forelse($groupDeparture->customers as $customer)
    <tr><td><strong>{{ $customer->name }}</strong><div class="small text-muted">{{ $customer->phone ?: $customer->email ?: 'No contact' }}</div></td><td>{{ $customer->pax }} × {{ $groupDeparture->currency }} {{ number_format($customer->rate,2) }}<div class="small text-muted">{{ ucfirst($customer->customer_type) }}</div></td><td colspan="2"><form method="POST" action="{{ route('admin.group-departures.customers.update',[$groupDeparture,$customer]) }}" class="d-flex gap-1">@csrf @method('PATCH')<input type="number" step="0.01" min="0" name="amount_paid" value="{{ $customer->amount_paid }}" class="form-control form-control-sm" style="width:105px"><select name="payment_status" class="form-select form-select-sm">@foreach(['pending','partial','paid'] as $s)<option {{ $customer->payment_status===$s?'selected':'' }}>{{ $s }}</option>@endforeach</select><select name="booking_status" class="form-select form-select-sm">@foreach(['reserved','confirmed','cancelled'] as $s)<option {{ $customer->booking_status===$s?'selected':'' }}>{{ $s }}</option>@endforeach</select><button class="btn btn-sm btn-outline-primary"><i class="bi bi-check"></i></button></form></td><td><strong>{{ $groupDeparture->currency }} {{ number_format($customer->balance,2) }}</strong></td><td><form method="POST" action="{{ route('admin.group-departures.customers.destroy',[$groupDeparture,$customer]) }}" onsubmit="return confirm('Remove this customer?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form></td></tr>
    @empty<tr><td colspan="6" class="text-center py-4 text-muted">No customers added yet.</td></tr>@endforelse
    </tbody></table></div></div>
</div>
<div class="col-xl-4">
    <div class="card"><div class="card-header"><strong><i class="bi bi-person-plus me-2 text-success"></i>Add Customer</strong></div><div class="card-body"><form method="POST" action="{{ route('admin.group-departures.customers.store',$groupDeparture) }}">@csrf
        <div class="mb-2"><label class="form-label">Customer name *</label><input name="name" class="form-control" required value="{{ old('name') }}"></div>
        <div class="row g-2"><div class="col-6"><label class="form-label">Phone</label><input name="phone" class="form-control" value="{{ old('phone') }}"></div><div class="col-6"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="{{ old('email') }}"></div></div>
        <div class="row g-2 mt-1"><div class="col-6"><label class="form-label">Type</label><select name="customer_type" class="form-select" id="customerType"><option value="adult">Adult</option><option value="child">Child</option></select></div><div class="col-6"><label class="form-label">Pax *</label><input type="number" min="1" max="{{ max(1,$groupDeparture->available_seats) }}" name="pax" value="1" class="form-control" required></div></div>
        <div class="row g-2 mt-1"><div class="col-6"><label class="form-label">Rate / pax *</label><input type="number" step="0.01" min="0" name="rate" id="customerRate" value="{{ $groupDeparture->adult_rate }}" class="form-control" required data-adult="{{ $groupDeparture->adult_rate }}" data-child="{{ $groupDeparture->child_rate ?: $groupDeparture->adult_rate }}"></div><div class="col-6"><label class="form-label">Paid</label><input type="number" step="0.01" min="0" name="amount_paid" value="0" class="form-control"></div></div>
        <input type="hidden" name="payment_status" value="pending"><input type="hidden" name="booking_status" value="reserved">
        <div class="mt-2"><label class="form-label">Passport number</label><input name="passport_number" class="form-control"></div><div class="mt-2"><label class="form-label">Notes</label><textarea name="notes" class="form-control" rows="2"></textarea></div>
        <button class="btn btn-success w-100 mt-3" {{ $groupDeparture->available_seats < 1 ? 'disabled' : '' }}><i class="bi bi-person-check me-1"></i>Add to Trip</button>
    </form></div></div>
</div></div>
@push('scripts')<script>document.getElementById('customerType')?.addEventListener('change',function(){const rate=document.getElementById('customerRate');rate.value=this.value==='child'?rate.dataset.child:rate.dataset.adult;});</script>@endpush
@endsection
