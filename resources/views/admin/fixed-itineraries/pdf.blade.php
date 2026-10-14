<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $itinerary->title }} | Tourliz Holidays</title>
    <style>
        :root { --navy:#10233f; --blue:#1769e0; --cyan:#22b8cf; --gold:#f5a623; --ink:#26364d; --muted:#6f7d90; --line:#dfe7f1; --soft:#f4f8fd; --green:#14966d; --red:#dc5260; }
        * { box-sizing:border-box; }
        body { margin:0; padding:0; background:#edf2f8; color:var(--ink); font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:1.55; }
        .document { width:100%; max-width:850px; margin:24px auto; background:#fff; box-shadow:0 12px 40px rgba(16,35,63,.12); }
        .hero { position:relative; overflow:hidden; padding:30px 34px 27px; color:#fff; background:linear-gradient(125deg, #0d2443 0%, #1553a6 62%, #1da7c3 100%); }
        .hero:after { content:""; position:absolute; width:260px; height:260px; right:-95px; top:-130px; border:42px solid rgba(255,255,255,.08); border-radius:50%; }
        .brand-row { position:relative; z-index:1; display:table; width:100%; }
        .brand-logo, .brand-meta { display:table-cell; vertical-align:middle; }
        .brand-logo img { display:block; width:168px; max-height:62px; object-fit:contain; object-position:left center; background:#fff; padding:8px 12px; border-radius:9px; }
        .brand-meta { text-align:right; font-size:10px; line-height:1.7; color:rgba(255,255,255,.82); }
        .eyebrow { position:relative; z-index:1; margin-top:29px; color:#9fe9f5; font-size:10px; font-weight:bold; letter-spacing:2px; text-transform:uppercase; }
        .hero h1 { position:relative; z-index:1; max-width:690px; margin:5px 0 8px; font-size:29px; line-height:1.18; letter-spacing:-.5px; }
        .hero-subtitle { position:relative; z-index:1; max-width:650px; margin:0; color:rgba(255,255,255,.82); font-size:12px; }
        .content { padding:25px 34px 32px; }
        .summary { width:100%; margin:-43px 0 25px; position:relative; z-index:2; border-collapse:separate; border-spacing:0; background:#fff; border:1px solid var(--line); border-radius:12px; box-shadow:0 8px 24px rgba(16,35,63,.1); overflow:hidden; }
        .summary td { width:25%; padding:14px 15px; border-right:1px solid var(--line); vertical-align:top; }
        .summary td:last-child { border-right:0; }
        .summary-icon { font-size:18px; line-height:1; color:var(--blue); }
        .summary-label { margin-top:5px; color:var(--muted); font-size:8px; font-weight:bold; letter-spacing:.8px; text-transform:uppercase; }
        .summary-value { margin-top:2px; color:var(--navy); font-size:11px; font-weight:bold; }
        .section { margin-top:25px; }
        .section-title { display:table; width:100%; margin:0 0 13px; border-bottom:1px solid var(--line); padding-bottom:8px; color:var(--navy); }
        .section-title span { display:table-cell; vertical-align:middle; font-size:17px; font-weight:bold; }
        .section-title small { display:table-cell; vertical-align:middle; text-align:right; color:var(--muted); font-size:9px; font-weight:normal; letter-spacing:.6px; text-transform:uppercase; }
        .intro { margin:0; padding:16px 18px; border-left:4px solid var(--cyan); background:var(--soft); color:#42536a; border-radius:0 9px 9px 0; }
        .intro strong { display:block; margin-bottom:5px; color:var(--navy); font-size:13px; }
        .timeline { margin-top:5px; }
        .day { page-break-inside:avoid; position:relative; margin:0 0 14px; padding:0 0 0 54px; }
        .day-badge { position:absolute; left:0; top:0; width:39px; height:39px; padding-top:6px; border-radius:50%; background:var(--blue); color:#fff; text-align:center; box-shadow:0 0 0 5px #eaf2ff; font-size:8px; font-weight:bold; line-height:1.1; }
        .day-badge b { display:block; font-size:14px; }
        .day-card { border:1px solid var(--line); border-radius:10px; overflow:hidden; }
        .day-heading { padding:10px 13px; background:#f7faff; border-bottom:1px solid var(--line); color:var(--navy); font-size:13px; font-weight:bold; }
        .day-body { padding:11px 13px 12px; color:#4c5b70; }
        .day-description { margin-bottom:8px; }
        .item { display:inline-block; margin:3px 4px 3px 0; padding:4px 7px; background:#f3f6fa; border:1px solid #e2e8f0; border-radius:5px; color:#45566d; font-size:9px; }
        .item b { color:var(--blue); }
        .overnight { margin-top:8px; color:var(--navy); font-size:10px; font-weight:bold; }
        .overnight span { color:var(--gold); font-size:13px; }
        .two-columns { width:100%; border-collapse:separate; border-spacing:10px 0; margin:0 -10px; }
        .two-columns td { width:50%; vertical-align:top; }
        .list-card { page-break-inside:avoid; border:1px solid var(--line); border-radius:10px; overflow:hidden; }
        .list-head { padding:10px 13px; font-size:12px; font-weight:bold; }
        .list-head.included { background:#eaf8f3; color:var(--green); }
        .list-head.excluded { background:#fff0f1; color:var(--red); }
        .list-card ul { margin:0; padding:11px 15px 11px 29px; }
        .list-card li { margin-bottom:5px; color:#506078; }
        .terms { page-break-inside:avoid; padding:14px 16px; background:#fffaf0; border:1px solid #f7dfad; border-radius:9px; color:#665735; font-size:10px; }
        .price-box { page-break-inside:avoid; margin-top:25px; padding:18px 20px; background:linear-gradient(125deg, #0e2544, #1769e0); border-radius:12px; color:#fff; }
        .price-table { width:100%; border-collapse:collapse; }
        .price-main { width:58%; vertical-align:middle; }
        .price-label { color:#a9d7ff; font-size:9px; font-weight:bold; letter-spacing:1px; text-transform:uppercase; }
        .price-value { margin-top:2px; font-size:25px; font-weight:bold; }
        .price-alt { vertical-align:middle; padding-left:18px; border-left:1px solid rgba(255,255,255,.25); color:rgba(255,255,255,.8); font-size:10px; }
        .price-alt strong { color:#fff; }
        .footer { padding:14px 34px 18px; border-top:1px solid var(--line); color:var(--muted); text-align:center; font-size:9px; }
        .footer strong { color:var(--navy); }
        @media print { @page { size:A4; margin:9mm; } body { background:#fff; -webkit-print-color-adjust:exact; print-color-adjust:exact; } .document { max-width:none; margin:0; box-shadow:none; } .hero { padding:24px 28px 23px; } .content { padding:22px 28px 25px; } .footer { padding:12px 28px 4px; } }
    </style>
</head>
<body>
<div class="document">
    <header class="hero">
        <div class="brand-row">
            <div class="brand-logo"><img src="{{ !empty($pdfMode) ? public_path('img/tourliz_logo.png') : asset('img/tourliz_logo.png') }}" alt="Tourliz Holidays"></div>
            <div class="brand-meta">Kuala Lumpur, Malaysia<br>hello@tourliz.com &nbsp; | &nbsp; www.tourliz.com</div>
        </div>
        <div class="eyebrow">Curated Travel Proposal</div>
        <h1>{{ $itinerary->title }}</h1>
        <p class="hero-subtitle">@if(!empty($itinerary->client_name))Prepared especially for <strong>{{ $itinerary->client_name }}</strong> — @endif A thoughtfully planned journey for a seamless and memorable travel experience.</p>
    </header>

    <main class="content">
        <table class="summary">
            <tr>
                <td><div class="summary-icon">&#128197;</div><div class="summary-label">Duration</div><div class="summary-value">{{ $itinerary->duration }}</div></td>
                <td><div class="summary-icon">&#128205;</div><div class="summary-label">Destination</div><div class="summary-value">{{ $itinerary->destination->name ?? 'Multiple Destinations' }}</div></td>
                @if(!empty($itinerary->travelers))
                    <td><div class="summary-icon">&#128101;</div><div class="summary-label">Travelers</div><div class="summary-value">{{ $itinerary->travelers }}</div></td>
                @else
                    <td><div class="summary-icon">&#127758;</div><div class="summary-label">Countries</div><div class="summary-value">{{ $itinerary->countries->count() ? $itinerary->countries->pluck('name')->join(', ') : 'Malaysia' }}</div></td>
                @endif
                <td><div class="summary-icon">&#128196;</div><div class="summary-label">{{ !empty($itinerary->start_date) ? 'Tour Starts' : 'Issued' }}</div><div class="summary-value">{{ !empty($itinerary->start_date) ? \Carbon\Carbon::parse($itinerary->start_date)->format('d M Y') : now()->format('d M Y') }}</div></td>
            </tr>
        </table>

        @if($itinerary->short_description || $itinerary->itinerary_description)
            <section class="section">
                <div class="section-title"><span>Journey Overview</span><small>Your experience at a glance</small></div>
                <div class="intro">
                    @if($itinerary->short_description)<strong>{{ $itinerary->short_description }}</strong>@endif
                    @if($itinerary->itinerary_description){!! nl2br(e($itinerary->itinerary_description)) !!}@endif
                </div>
            </section>
        @endif

        @if(is_array($itinerary->days) && count($itinerary->days) > 0)
            <section class="section">
                <div class="section-title"><span>Day-by-Day Itinerary</span><small>{{ count($itinerary->days) }} planned days</small></div>
                <div class="timeline">
                    @foreach($itinerary->days as $index => $rawDay)
                        @php
                            $day = (array) $rawDay;
                            $dayNum = $day['day_number'] ?? ($index + 1);
                            $dayItems = is_array($day['items'] ?? null) ? $day['items'] : [];
                        @endphp
                        <article class="day">
                            <div class="day-badge">DAY<b>{{ $dayNum }}</b></div>
                            <div class="day-card">
                                <div class="day-heading">{{ $day['title'] ?? 'Day ' . $dayNum }}</div>
                                <div class="day-body">
                                    @if(!empty($day['description']))<div class="day-description">{!! nl2br(e($day['description'])) !!}</div>@endif
                                    @foreach($dayItems as $rawItem)
                                        @php $item = (array) $rawItem; @endphp
                                        <span class="item"><b>{{ ($item['item_type'] ?? '') === 'spot' ? '📍' : strtoupper($item['item_type'] ?? 'ITEM') }}</b> &nbsp; {{ $item['title'] ?? '' }}@if(!empty($item['room_type'])) · {{ $item['room_type'] }}@endif</span>
                                    @endforeach
                                    @if(!empty($day['overnight_location']))<div class="overnight"><span>&#9790;</span> Overnight in {{ $day['overnight_location'] }}</div>@endif
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>
        @endif

        @if(!empty($itinerary->inclusions) || !empty($itinerary->exclusions))
            <section class="section">
                <div class="section-title"><span>What’s Included</span><small>Package coverage</small></div>
                <table class="two-columns"><tr>
                    <td><div class="list-card"><div class="list-head included">&#10003; Package Inclusions</div><ul>@forelse($itinerary->inclusions ?? [] as $inc)<li>{{ $inc }}</li>@empty<li>As specified in the itinerary</li>@endforelse</ul></div></td>
                    <td><div class="list-card"><div class="list-head excluded">&#10005; Package Exclusions</div><ul>@forelse($itinerary->exclusions ?? [] as $exc)<li>{{ $exc }}</li>@empty<li>Items not expressly listed</li>@endforelse</ul></div></td>
                </tr></table>
            </section>
        @endif

        @if(!empty($itinerary->terms_conditions))
            <section class="section"><div class="section-title"><span>Important Information</span><small>Please review</small></div><div class="terms">{!! nl2br(e($itinerary->terms_conditions)) !!}</div></section>
        @endif

        <div class="price-box">
            <table class="price-table"><tr>
                <td class="price-main"><div class="price-label">Total Package Price</div><div class="price-value">{{ $itinerary->currency ?: 'MYR' }} {{ number_format($itinerary->fixed_price, 2) }}</div></td>
                <td class="price-alt"><strong>Customer quotation</strong><br>All customer-facing prices are shown in {{ $itinerary->currency ?: 'MYR' }}.<br>@if(!empty($itinerary->quote_id))Ref: {{ $itinerary->quote_id }}@endif</td>
            </tr></table>
        </div>
    </main>

    <footer class="footer"><strong>Thank you for choosing Tourliz Holidays.</strong><br>This proposal was generated by Tourliz CMS. Rates and availability are subject to confirmation.</footer>
</div>

@if(empty($pdfMode))<script>window.onload = function () { window.print(); };</script>@endif
</body>
</html>
