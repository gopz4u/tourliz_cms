<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>{{ $groupDeparture->title }}</title>
<style>
    @page { margin: 24px 30px 38px; }
    * { box-sizing: border-box; }
    body { margin: 0; color: #26364d; font-family: DejaVu Sans, sans-serif; font-size: 10px; line-height: 1.5; }
    .top-line { height: 7px; background: #263b80; margin-bottom: 18px; }
    .header { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
    .header td { vertical-align: middle; }
    .logo { width: 165px; max-height: 62px; }
    .company { text-align: right; color: #68768a; font-size: 8px; line-height: 1.6; }
    .hero { background: #152c52; color: #ffffff; padding: 20px 22px; border-left: 7px solid #ef3340; }
    .hero-kicker { color: #79d6e8; font-size: 8px; font-weight: bold; letter-spacing: 1.8px; text-transform: uppercase; }
    .hero h1 { margin: 5px 0 6px; color: #ffffff; font-size: 23px; line-height: 1.22; }
    .hero p { margin: 0; color: #d8e5f6; font-size: 10px; }
    .summary { width: 100%; margin: 14px 0 20px; border-collapse: collapse; border: 1px solid #dce4ef; }
    .summary td { width: 20%; padding: 11px 9px; border-right: 1px solid #dce4ef; vertical-align: top; }
    .summary td:last-child { border-right: 0; }
    .mini-icon { display: inline-block; width: 19px; height: 19px; padding-top: 3px; margin-bottom: 4px; background: #eaf1ff; color: #245fc4; border-radius: 9px; text-align: center; font-size: 8px; font-weight: bold; }
    .label { color: #8490a1; font-size: 7px; font-weight: bold; letter-spacing: .6px; text-transform: uppercase; }
    .value { margin-top: 2px; color: #142b4c; font-size: 9px; font-weight: bold; }
    .section-title { width: 100%; margin: 20px 0 10px; padding-bottom: 6px; border-bottom: 2px solid #263b80; color: #142b4c; font-size: 14px; font-weight: bold; }
    .section-title span { float: right; padding-top: 4px; color: #8995a5; font-size: 7px; font-weight: normal; letter-spacing: .7px; text-transform: uppercase; }
    .overview { padding: 12px 14px; background: #f2f6fb; border-left: 4px solid #2aa8c7; color: #47576c; }
    .overview strong { display: block; margin-bottom: 4px; color: #142b4c; font-size: 11px; }
    .day { page-break-inside: avoid; width: 100%; margin-bottom: 11px; border-collapse: collapse; }
    .day-number { width: 52px; padding: 10px 6px; background: #263b80; color: #ffffff; text-align: center; vertical-align: top; }
    .day-number small { display: block; font-size: 7px; letter-spacing: .7px; }
    .day-number b { display: block; font-size: 17px; }
    .day-content { padding: 9px 12px; border: 1px solid #dce4ef; border-left: 0; vertical-align: top; }
    .day-title { margin-bottom: 5px; color: #142b4c; font-size: 11px; font-weight: bold; }
    .day-description { color: #526176; }
    .item-table { width: 100%; margin-top: 7px; border-collapse: collapse; }
    .item-table td { padding: 4px 6px; border-top: 1px solid #e8edf3; color: #4c5b70; font-size: 8px; }
    .item-type { width: 70px; color: #245fc4 !important; font-weight: bold; text-transform: uppercase; }
    .spot-mark { color: #ef3340; font-size: 11px; }
    .overnight { margin-top: 6px; color: #263b80; font-size: 8px; font-weight: bold; }
    .lists { width: 100%; border-collapse: separate; border-spacing: 7px 0; margin: 0 -7px; }
    .lists td { width: 50%; padding: 0; vertical-align: top; border: 1px solid #dce4ef; }
    .list-title { padding: 7px 10px; font-size: 9px; font-weight: bold; }
    .included { background: #e8f7f1; color: #13815f; }
    .excluded { background: #fff0f1; color: #bd3e4a; }
    .lists ul { margin: 8px 10px 9px 22px; padding: 0; color: #526176; }
    .price { page-break-inside: avoid; width: 100%; margin-top: 18px; border-collapse: collapse; background: #152c52; color: #ffffff; }
    .price td { padding: 14px 17px; vertical-align: middle; }
    .price-main { width: 50%; border-right: 1px solid #506682; }
    .price-label { color: #9acfe5; font-size: 7px; font-weight: bold; letter-spacing: 1px; text-transform: uppercase; }
    .price-value { margin-top: 2px; color: #ffffff; font-size: 20px; font-weight: bold; }
    .price-detail { color: #d8e5f6; font-size: 8px; line-height: 1.8; }
    .terms { page-break-inside: avoid; padding: 10px 12px; background: #fff9ed; border: 1px solid #f0dcae; color: #685b3b; font-size: 8px; }
    .footer { position: fixed; right: 0; bottom: -25px; left: 0; padding-top: 7px; border-top: 1px solid #dce4ef; color: #8793a3; text-align: center; font-size: 7px; }
    .footer strong { color: #263b80; }
</style>
</head>
<body>
@php
    $logoFile = public_path('img/tourliz_logo.png');
    $logoData = is_file($logoFile) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoFile)) : 'https://webcms.tourliz.com/img/tourliz_logo.png';
    $itinerary = $groupDeparture->fixedItinerary;
@endphp
<div class="top-line"></div>
<table class="header"><tr><td><img src="{{ $logoData }}" class="logo" alt="Tourliz"></td><td class="company"><strong>TOURLIZ HOLIDAYS</strong><br>Kuala Lumpur, Malaysia<br>hello@tourliz.com &nbsp; | &nbsp; www.tourliz.com</td></tr></table>
<div class="hero"><div class="hero-kicker">Group Departure Proposal</div><h1>{{ $groupDeparture->title }}</h1><p>A carefully planned group journey with confirmed departure details and transparent pricing.</p></div>
<table class="summary"><tr>
    <td><div class="mini-icon">D</div><div class="label">Departure</div><div class="value">{{ $groupDeparture->departure_date->format('d M Y') }}</div></td>
    <td><div class="mini-icon">R</div><div class="label">Return</div><div class="value">{{ $groupDeparture->return_date ? $groupDeparture->return_date->format('d M Y') : 'To be advised' }}</div></td>
    <td><div class="mini-icon">N</div><div class="label">Duration</div><div class="value">{{ $itinerary->duration }}</div></td>
    <td><div class="mini-icon">P</div><div class="label">Destination</div><div class="value">{{ $itinerary->destination->name ?? 'Multiple destinations' }}</div></td>
    <td><div class="mini-icon">S</div><div class="label">Group Size</div><div class="value">Maximum {{ $groupDeparture->capacity }} guests</div></td>
</tr></table>

@if($itinerary->short_description || $itinerary->itinerary_description)
<div class="section-title">Journey Overview <span>Experience at a glance</span></div>
<div class="overview">@if($itinerary->short_description)<strong>{{ $itinerary->short_description }}</strong>@endif @if($itinerary->itinerary_description){!! nl2br(e($itinerary->itinerary_description)) !!}@endif</div>
@endif

@if(is_array($itinerary->days) && count($itinerary->days))
<div class="section-title">Day-by-Day Itinerary <span>{{ count($itinerary->days) }} planned days</span></div>
@foreach($itinerary->days as $index => $rawDay)
@php $day=(array)$rawDay; $number=$day['day_number']??$index+1; $items=is_array($day['items']??null)?$day['items']:[]; @endphp
<table class="day"><tr><td class="day-number"><small>DAY</small><b>{{ $number }}</b></td><td class="day-content"><div class="day-title">{{ $day['title'] ?? 'Day '.$number }}</div>@if(!empty($day['description']))<div class="day-description">{!! nl2br(e($day['description'])) !!}</div>@endif
@if(count($items))<table class="item-table">@foreach($items as $rawItem)@php $item=(array)$rawItem; $isSpot=($item['item_type']??'')==='spot'; @endphp<tr><td class="item-type">{!! $isSpot ? '<span class="spot-mark">&#9679;</span> PLACE' : e(strtoupper($item['item_type']??'SERVICE')) !!}</td><td>{{ $item['title']??'' }}@if(!empty($item['room_type'])) — {{ $item['room_type'] }}@endif</td></tr>@endforeach</table>@endif
@if(!empty($day['overnight_location']))<div class="overnight">OVERNIGHT: {{ $day['overnight_location'] }}</div>@endif</td></tr></table>
@endforeach
@endif

@if(!empty($itinerary->inclusions) || !empty($itinerary->exclusions))
<div class="section-title">Package Coverage <span>What is and is not included</span></div>
<table class="lists"><tr><td><div class="list-title included">INCLUDED</div><ul>@foreach($itinerary->inclusions??[] as $item)<li>{{ $item }}</li>@endforeach</ul></td><td><div class="list-title excluded">NOT INCLUDED</div><ul>@foreach($itinerary->exclusions??[] as $item)<li>{{ $item }}</li>@endforeach</ul></td></tr></table>
@endif

<table class="price"><tr><td class="price-main"><div class="price-label">Group Trip Rate — Per Person</div><div class="price-value">{{ $groupDeparture->currency }} {{ number_format($groupDeparture->adult_rate,2) }}</div></td><td class="price-detail"><strong>Adult:</strong> {{ $groupDeparture->currency }} {{ number_format($groupDeparture->adult_rate,2) }}<br><strong>Child:</strong> {{ $groupDeparture->child_rate ? $groupDeparture->currency.' '.number_format($groupDeparture->child_rate,2) : 'Contact us' }}<br><strong>Status:</strong> {{ ucwords(str_replace('_',' ',$groupDeparture->status)) }}</td></tr></table>

@if($itinerary->terms_conditions)<div class="section-title">Important Information</div><div class="terms">{!! nl2br(e($itinerary->terms_conditions)) !!}</div>@endif
<div class="footer"><strong>TOURLIZ HOLIDAYS</strong> &nbsp; | &nbsp; Group departure proposal generated {{ now()->format('d M Y') }} &nbsp; | &nbsp; Rates and availability are subject to confirmation.</div>
</body>
</html>
