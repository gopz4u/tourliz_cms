<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $itinerary->title }} | Tourliz Holidays</title>
    <style>
        @page { size: A4 portrait; margin: 18mm 14mm 17mm; }
        * { box-sizing: border-box; }
        body { margin: 0; color: #24354b; font-family: DejaVu Sans, sans-serif; font-size: 9.5px; line-height: 1.45; }
        table { width: 100%; border-collapse: collapse; }
        .header { padding-bottom: 12px; border-bottom: 2px solid #163e73; }
        .header td { vertical-align: middle; }
        .logo { width: 145px; }
        .brand { text-align: right; color: #68788c; font-size: 8px; line-height: 1.65; }
        .brand strong { display: block; color: #163e73; font-size: 12px; }
        .hero { margin-top: 13px; padding: 18px 20px; background: #112b4d; color: #fff; }
        .kicker { color: #6bd4e9; font-size: 8px; font-weight: bold; letter-spacing: 1.5px; text-transform: uppercase; }
        h1 { margin: 5px 0 6px; font-size: 22px; line-height: 1.18; }
        .hero-copy { color: #d8e4f1; font-size: 10px; }
        .hero-copy strong { color: #fff; }
        .quote-ref { margin-top: 10px; color: #9fb8d4; font-size: 8px; }
        .facts { margin: 0 0 17px; border: 1px solid #dce5ef; border-top: 0; }
        .facts td { width: 25%; padding: 10px 11px; vertical-align: top; border-right: 1px solid #dce5ef; }
        .facts td:last-child { border-right: 0; }
        .fact-mark { display: inline-block; width: 17px; height: 17px; padding-top: 2px; margin-bottom: 4px; border-radius: 9px; background: #e9f3ff; color: #1768d3; text-align: center; font-size: 8px; font-weight: bold; }
        .label { color: #7a899b; font-size: 6.8px; font-weight: bold; letter-spacing: .65px; text-transform: uppercase; }
        .value { margin-top: 2px; color: #172c47; font-size: 9px; font-weight: bold; line-height: 1.3; }
        .section { margin-top: 17px; }
        .section-head { margin-bottom: 9px; padding-bottom: 6px; border-bottom: 1px solid #dce5ef; }
        .section-head td { vertical-align: bottom; }
        .section-title { color: #15365f; font-size: 14px; font-weight: bold; }
        .section-note { text-align: right; color: #8290a1; font-size: 7px; letter-spacing: .7px; text-transform: uppercase; }
        .overview { padding: 11px 13px; background: #f2f7fc; border-left: 3px solid #19abc6; color: #42566e; }
        .overview strong { color: #163e73; }
        .day { margin-bottom: 10px; page-break-inside: avoid; }
        .day-side { width: 47px; padding: 8px 5px; background: #1768d3; color: #fff; text-align: center; vertical-align: top; }
        .day-side small { display: block; font-size: 6px; letter-spacing: .8px; }
        .day-side b { font-size: 15px; line-height: 1.1; }
        .day-content { padding: 9px 12px; border: 1px solid #dce5ef; border-left: 0; vertical-align: top; }
        .day-title { margin-bottom: 5px; color: #17385f; font-size: 11px; font-weight: bold; }
        .day-description { margin-bottom: 6px; color: #65758a; }
        .item { margin: 3px 0; }
        .item-type { display: inline-block; min-width: 54px; color: #1768d3; font-size: 7px; font-weight: bold; letter-spacing: .4px; text-transform: uppercase; }
        .item-name { color: #31465f; font-weight: bold; }
        .overnight { margin-top: 6px; padding-top: 5px; border-top: 1px solid #e8edf3; color: #7e5b17; font-size: 8px; font-weight: bold; }
        .list-grid { border-spacing: 8px 0; border-collapse: separate; margin-left: -8px; width: calc(100% + 16px); }
        .list-grid td { width: 50%; padding: 0; vertical-align: top; }
        .list-card { border: 1px solid #dce5ef; page-break-inside: avoid; }
        .list-title { padding: 8px 10px; font-size: 9px; font-weight: bold; }
        .included { background: #eaf8f2; color: #147b5d; }
        .excluded { background: #fff0f1; color: #bd3f4d; }
        ul { margin: 0; padding: 9px 12px 9px 25px; }
        li { margin-bottom: 4px; color: #506278; }
        .terms { padding: 10px 12px; background: #fff9ec; border: 1px solid #f0d9a5; color: #665a40; page-break-inside: avoid; }
        .price { margin-top: 17px; background: #12355f; color: #fff; page-break-inside: avoid; }
        .price-main { width: 58%; padding: 14px 16px; vertical-align: middle; }
        .price-label { color: #8fdced; font-size: 7px; font-weight: bold; letter-spacing: 1px; text-transform: uppercase; }
        .price-value { margin-top: 2px; font-size: 22px; font-weight: bold; }
        .price-note { padding: 13px 15px; border-left: 1px solid #456484; color: #cbd9e7; vertical-align: middle; font-size: 8px; }
        .price-note strong { color: #fff; }
        .footer { margin-top: 15px; padding-top: 8px; border-top: 1px solid #dce5ef; color: #77879a; text-align: center; font-size: 7px; }
        .footer strong { color: #17385f; }
    </style>
</head>
<body>
    <table class="header"><tr>
        <td><img class="logo" src="{{ public_path('img/tourliz_logo.png') }}" alt="Tourliz Holidays"></td>
        <td class="brand"><strong>Tourliz Holidays</strong>Kuala Lumpur, Malaysia<br>www.tourliz.com &nbsp; | &nbsp; @tourliz</td>
    </tr></table>

    <div class="hero">
        <div class="kicker">Curated Travel Proposal</div>
        <h1>{{ $itinerary->title }}</h1>
        <div class="hero-copy">Prepared especially for <strong>{{ $itinerary->client_name ?: 'Valued Guest' }}</strong></div>
        @if(!empty($itinerary->quote_id))<div class="quote-ref">PROPOSAL REFERENCE &nbsp; {{ $itinerary->quote_id }}</div>@endif
    </div>

    <table class="facts"><tr>
        <td><span class="fact-mark">01</span><div class="label">Duration</div><div class="value">{{ $itinerary->duration }}</div></td>
        <td><span class="fact-mark">02</span><div class="label">Destination</div><div class="value">{{ $itinerary->destination->name ?? 'Multiple destinations' }}</div></td>
        <td><span class="fact-mark">03</span><div class="label">Travelers</div><div class="value">{{ $itinerary->travelers }}</div></td>
        <td><span class="fact-mark">04</span><div class="label">Tour Starts</div><div class="value">{{ !empty($itinerary->start_date) ? \Carbon\Carbon::parse($itinerary->start_date)->format('d M Y') : 'To be confirmed' }}</div></td>
    </tr></table>

    <div class="section">
        <table class="section-head"><tr><td class="section-title">Journey Overview</td><td class="section-note">Your experience at a glance</td></tr></table>
        <div class="overview"><strong>{{ $itinerary->short_description ?: 'A memorable journey, thoughtfully planned for you.' }}</strong></div>
    </div>

    @if(is_array($itinerary->days) && count($itinerary->days))
    <div class="section">
        <table class="section-head"><tr><td class="section-title">Day-by-Day Itinerary</td><td class="section-note">{{ count($itinerary->days) }} planned days</td></tr></table>
        @foreach($itinerary->days as $index => $rawDay)
            @php $day = (array) $rawDay; $number = $day['day_number'] ?? ($index + 1); @endphp
            <table class="day"><tr>
                <td class="day-side"><small>DAY</small><b>{{ $number }}</b></td>
                <td class="day-content">
                    <div class="day-title">{{ $day['title'] ?? ('Day '.$number) }}</div>
                    @if(!empty($day['description']))<div class="day-description">{!! nl2br(e($day['description'])) !!}</div>@endif
                    @forelse(($day['items'] ?? []) as $rawItem)
                        @php $item = (array) $rawItem; $type = strtoupper($item['item_type'] ?? 'Experience'); @endphp
                        <div class="item"><span class="item-type">{{ $type }}</span><span class="item-name">{{ $item['title'] ?? '' }}@if(!empty($item['room_type'])) &nbsp;—&nbsp; {{ $item['room_type'] }}@endif</span></div>
                    @empty
                        <div class="day-description">Leisure time and arrangements as discussed.</div>
                    @endforelse
                    @if(!empty($day['overnight_location']))<div class="overnight">OVERNIGHT &nbsp; {{ $day['overnight_location'] }}</div>@endif
                </td>
            </tr></table>
        @endforeach
    </div>
    @endif

    <div class="section">
        <table class="section-head"><tr><td class="section-title">Package Details</td><td class="section-note">What your proposal covers</td></tr></table>
        <table class="list-grid"><tr>
            <td><div class="list-card"><div class="list-title included">INCLUDED IN YOUR PACKAGE</div><ul>@foreach($itinerary->inclusions ?? [] as $item)<li>{{ $item }}</li>@endforeach</ul></div></td>
            <td><div class="list-card"><div class="list-title excluded">NOT INCLUDED</div><ul>@foreach($itinerary->exclusions ?? [] as $item)<li>{{ $item }}</li>@endforeach</ul></div></td>
        </tr></table>
    </div>

    @if(!empty($itinerary->terms_conditions))
    <div class="section"><table class="section-head"><tr><td class="section-title">Important Information</td><td class="section-note">Please review</td></tr></table><div class="terms">{{ $itinerary->terms_conditions }}</div></div>
    @endif

    <table class="price"><tr>
        <td class="price-main"><div class="price-label">Total Package Price</div><div class="price-value">{{ $itinerary->currency ?: 'MYR' }} {{ number_format((float) $itinerary->fixed_price, 2) }}</div></td>
        <td class="price-note"><strong>Your selected quotation currency</strong><br>Rates and availability remain subject to confirmation.<br>@if(!empty($itinerary->quote_id))Ref: {{ $itinerary->quote_id }}@endif</td>
    </tr></table>

    <div class="footer"><strong>Thank you for choosing Tourliz Holidays.</strong><br>Your travel partner for thoughtfully planned, memorable journeys.</div>
</body>
</html>
