<?php
use App\Models\Destination;
use App\Models\Country;

$masterCountries = Country::where('status', true)->pluck('name')->toArray();
$destinationCountries = Destination::whereNotNull('country')->where('country', '!=', '')->distinct()->pluck('country')->toArray();
$countries = collect(array_merge($masterCountries, $destinationCountries))->unique()->sort()->values();

echo json_encode($countries);
