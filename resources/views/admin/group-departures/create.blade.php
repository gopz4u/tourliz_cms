@extends('layouts.admin')
@section('title', 'Announce Group Departure')
@section('content')
<div class="page-header d-flex justify-content-between align-items-center"><div><h2><i class="bi bi-calendar2-plus text-primary me-2"></i>Announce Group Departure</h2><p class="text-muted mb-0">Turn a fixed itinerary into a dated, bookable group trip.</p></div><a href="{{ route('admin.group-departures.index') }}" class="btn btn-outline-secondary">Back</a></div>
@if($errors->any())<div class="alert alert-danger"><strong>Please correct the highlighted details.</strong><ul class="mb-0 mt-1">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<form method="POST" action="{{ route('admin.group-departures.store') }}"><div class="card"><div class="card-body">@csrf @include('admin.group-departures._form')</div><div class="card-footer bg-white text-end"><button class="btn btn-primary"><i class="bi bi-megaphone me-1"></i> Announce Trip</button></div></div></form>
@endsection
