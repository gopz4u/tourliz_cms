@extends('layouts.admin')
@section('title', 'Edit Group Departure')
@section('content')
<div class="page-header d-flex justify-content-between align-items-center"><div><h2>Edit Group Departure</h2><p class="text-muted mb-0">Update announced dates, rates and availability.</p></div><a href="{{ route('admin.group-departures.show', $groupDeparture) }}" class="btn btn-outline-secondary">Back</a></div>
@if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<form method="POST" action="{{ route('admin.group-departures.update', $groupDeparture) }}"><div class="card"><div class="card-body">@csrf @method('PUT') @include('admin.group-departures._form')</div><div class="card-footer bg-white text-end"><button class="btn btn-primary">Save Changes</button></div></div></form>
@endsection
