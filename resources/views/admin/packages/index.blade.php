@extends('layouts.admin')

@section('title', 'Package Inventory')

@push('styles')
<style>
    .packages-page { --ink:#172033; --muted:#718096; --line:#e8edf5; --soft:#f7f8fc; --brand:#5a52e5; }
    .packages-page .page-kicker { color:var(--brand); font-size:.72rem; font-weight:800; letter-spacing:.13em; text-transform:uppercase; }
    .packages-page .page-title { color:var(--ink); font-size:clamp(1.55rem,3vw,2.15rem); font-weight:800; letter-spacing:-.045em; }
    .packages-page .create-btn { background:var(--brand); border:0; border-radius:14px; box-shadow:0 10px 24px rgba(90,82,229,.22); color:#fff; font-weight:700; padding:.8rem 1.15rem; }
    .packages-page .create-btn:hover { background:#4c44d3; color:#fff; transform:translateY(-1px); }
    .summary-card { align-items:center; background:#fff; border:1px solid var(--line); border-radius:18px; display:flex; gap:1rem; padding:1.15rem; height:100%; }
    .summary-icon { align-items:center; border-radius:14px; display:flex; flex:0 0 46px; height:46px; justify-content:center; width:46px; }
    .summary-value { color:var(--ink); font-size:1.3rem; font-weight:800; line-height:1.1; }
    .summary-label { color:var(--muted); font-size:.76rem; font-weight:600; margin-top:.3rem; }
    .toolbar { background:#fff; border:1px solid var(--line); border-radius:18px; padding:.8rem; }
    .search-wrap { position:relative; }
    .search-wrap i { color:#98a2b3; left:1rem; position:absolute; top:50%; transform:translateY(-50%); }
    .search-wrap input { background:var(--soft); border:1px solid transparent; border-radius:12px; min-height:44px; padding-left:2.65rem; }
    .search-wrap input:focus { background:#fff; border-color:rgba(90,82,229,.35); box-shadow:0 0 0 4px rgba(90,82,229,.08); }
    .filter-chip { background:#fff; border:1px solid var(--line); border-radius:12px; color:#64748b; font-size:.79rem; font-weight:700; padding:.65rem .9rem; white-space:nowrap; }
    .filter-chip.active,.filter-chip:hover { background:#efeffd; border-color:#d9d7fa; color:var(--brand); }
    .package-card { background:#fff; border:1px solid var(--line); border-radius:20px; height:100%; overflow:hidden; transition:.25s ease; }
    .package-card:hover { border-color:#d8d5fa; box-shadow:0 16px 35px rgba(23,32,51,.09); transform:translateY(-4px); }
    .package-media { aspect-ratio:16/10; background:#edf0f6; overflow:hidden; position:relative; }
    .package-media img { height:100%; object-fit:cover; transition:transform .45s ease; width:100%; }
    .package-card:hover .package-media img { transform:scale(1.035); }
    .status-pill { align-items:center; backdrop-filter:blur(10px); background:rgba(255,255,255,.9); border-radius:999px; display:flex; font-size:.68rem; font-weight:800; gap:.4rem; left:.8rem; padding:.42rem .68rem; position:absolute; top:.8rem; }
    .status-dot { border-radius:50%; height:7px; width:7px; }
    .card-menu { background:rgba(255,255,255,.92); border:0; border-radius:11px; color:var(--ink); height:36px; position:absolute; right:.8rem; top:.8rem; width:36px; }
    .package-body { padding:1.15rem; }
    .package-category { color:var(--brand); font-size:.68rem; font-weight:800; letter-spacing:.08em; text-transform:uppercase; }
    .package-name { color:var(--ink); font-size:1rem; font-weight:800; line-height:1.35; margin:.35rem 0 .45rem; }
    .package-place { color:var(--muted); font-size:.77rem; min-height:1.25rem; }
    .package-footer { align-items:end; border-top:1px solid var(--line); display:flex; justify-content:space-between; margin-top:1rem; padding-top:1rem; }
    .price-label { color:#98a2b3; font-size:.66rem; font-weight:700; text-transform:uppercase; }
    .package-price { color:var(--ink); font-size:1.1rem; font-weight:800; }
    .duration { background:var(--soft); border-radius:9px; color:#64748b; font-size:.7rem; font-weight:700; padding:.42rem .58rem; }
    .state-panel { background:#fff; border:1px dashed #d7deea; border-radius:20px; color:var(--muted); padding:4rem 1rem; text-align:center; }
    .state-icon { align-items:center; background:#efeffd; border-radius:18px; color:var(--brand); display:inline-flex; font-size:1.4rem; height:56px; justify-content:center; margin-bottom:1rem; width:56px; }
    @media (max-width:767.98px) { .packages-page { padding:.25rem!important; } .summary-card { padding:.9rem; } .toolbar-scroll { overflow-x:auto; padding-bottom:.2rem; } }
</style>
@endpush

@section('content')
<div class="packages-page py-3 py-lg-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div><div class="page-kicker mb-2">Catalog management</div><h1 class="page-title mb-1">Package inventory</h1><p class="text-muted mb-0">Create, publish and manage every travel experience.</p></div>
        <a href="{{ route('admin.packages.create') }}" class="btn create-btn"><i class="bi bi-plus-lg me-2"></i>New package</a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3"><div class="summary-card"><div class="summary-icon bg-primary-subtle text-primary"><i class="bi bi-collection"></i></div><div><div class="summary-value" id="stat-total">—</div><div class="summary-label">Total packages</div></div></div></div>
        <div class="col-6 col-xl-3"><div class="summary-card"><div class="summary-icon bg-success-subtle text-success"><i class="bi bi-broadcast"></i></div><div><div class="summary-value" id="stat-active">—</div><div class="summary-label">Published</div></div></div></div>
        <div class="col-6 col-xl-3"><div class="summary-card"><div class="summary-icon bg-warning-subtle text-warning"><i class="bi bi-stars"></i></div><div><div class="summary-value" id="stat-featured">—</div><div class="summary-label">Featured</div></div></div></div>
        <div class="col-6 col-xl-3"><div class="summary-card"><div class="summary-icon bg-info-subtle text-info"><i class="bi bi-wallet2"></i></div><div><div class="summary-value" id="stat-value">—</div><div class="summary-label">Catalog value</div></div></div></div>
    </div>

    <div class="toolbar mb-4">
        <div class="row g-2 align-items-center">
            <div class="col-lg-5"><div class="search-wrap"><i class="bi bi-search"></i><input id="pkg-search" type="search" class="form-control" placeholder="Search packages or destinations…" autocomplete="off"></div></div>
            <div class="col-lg-7"><div class="d-flex gap-2 justify-content-lg-end toolbar-scroll" id="category-filters">
                <button class="filter-chip active" data-filter="all">All</button><button class="filter-chip" data-filter="Honeymoon">Honeymoon</button><button class="filter-chip" data-filter="Budget">Budget</button><button class="filter-chip" data-filter="Standard">Standard</button><button class="filter-chip" data-filter="Premium">Premium</button><button class="filter-chip" data-filter="Platinum">Platinum</button>
            </div></div>
        </div>
    </div>

    <div class="row g-3 g-xl-4" id="package-grid" aria-live="polite">
        @for($i=0;$i<4;$i++)<div class="col-sm-6 col-xl-3"><div class="package-card placeholder-glow"><div class="package-media placeholder"></div><div class="package-body"><span class="placeholder col-4"></span><h3 class="placeholder col-10 mt-3"></h3><span class="placeholder col-7"></span><div class="package-footer"><span class="placeholder col-4"></span><span class="placeholder col-3"></span></div></div></div></div>@endfor
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
(() => {
    const endpoint = @json(route('admin.packages.index'));
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
    let packages = [], activeFilter = 'all', query = '';
    const escapeHtml = value => $('<div>').text(value ?? '').html();
    const money = value => new Intl.NumberFormat('en-MY', { maximumFractionDigits: 0 }).format(Number(value) || 0);
    const imageUrl = pkg => {
        if (!pkg.image) return `https://placehold.co/800x500/f1f3f9/667085?text=${encodeURIComponent(pkg.name || 'Package')}`;
        let url = String(pkg.image).replace(/https?:\/\/webcms\.tourliz\.com\/packages\//g,'https://img.tourliz.com/packages/').replace(/https?:\/\/webcms\.tourliz\.com\/images\//g,'https://img.tourliz.com/images/');
        return /^(https?:)?\/\//.test(url) ? url : (/^(packages|images)\//.test(url) ? `https://img.tourliz.com/${url}` : `/${url.replace(/^\//,'')}`);
    };
    const statePanel = (icon,title,text,action='') => `<div class="col-12"><div class="state-panel"><div class="state-icon"><i class="bi ${icon}"></i></div><h5 class="fw-bold text-dark">${title}</h5><p class="mb-3">${text}</p>${action}</div></div>`;
    function render() {
        const shown = packages.filter(pkg => {
            const category = pkg.package_category || 'Standard';
            const place = (pkg.destinations || []).map(d => d.name).join(' ') || pkg.destination?.name || '';
            return (activeFilter === 'all' || category === activeFilter) && `${pkg.name} ${place}`.toLowerCase().includes(query);
        });
        if (!shown.length) { $('#package-grid').html(statePanel('bi-search','No packages found','Try another search or category.')); return; }
        $('#package-grid').html(shown.map(pkg => {
            const live = pkg.status === 'active';
            const place = (pkg.destinations || []).map(d => d.name).join(', ') || pkg.destination?.name || 'Multiple destinations';
            return `<div class="col-sm-6 col-xl-3"><article class="package-card">
                <div class="package-media"><img src="${escapeHtml(imageUrl(pkg))}" alt="${escapeHtml(pkg.name)}" loading="lazy" onerror="this.src='https://placehold.co/800x500/f1f3f9/667085?text=Package'">
                    <span class="status-pill"><span class="status-dot ${live?'bg-success':'bg-secondary'}"></span>${live?'Published':'Draft'}</span>
                    <div class="dropdown"><button class="card-menu" data-bs-toggle="dropdown" aria-label="Package actions"><i class="bi bi-three-dots"></i></button><ul class="dropdown-menu dropdown-menu-end border-0 shadow rounded-3 p-2">
                        <li><a class="dropdown-item rounded-2" href="/admin/packages/${Number(pkg.id)}/edit"><i class="bi bi-pencil me-2"></i>Edit</a></li>
                        <li><button class="dropdown-item rounded-2 text-primary js-duplicate" data-id="${Number(pkg.id)}"><i class="bi bi-copy me-2"></i>Duplicate</button></li><li><hr class="dropdown-divider"></li>
                        <li><button class="dropdown-item rounded-2 text-danger js-delete" data-id="${Number(pkg.id)}"><i class="bi bi-trash3 me-2"></i>Delete</button></li></ul></div></div>
                <div class="package-body"><div class="package-category">${escapeHtml(pkg.package_category || 'Standard')}</div><h2 class="package-name text-truncate">${escapeHtml(pkg.name)}</h2><div class="package-place text-truncate"><i class="bi bi-geo-alt me-1"></i>${escapeHtml(place)}</div>
                    <div class="package-footer"><div><div class="price-label">Starting from</div><div class="package-price">RM ${money(pkg.price)}</div></div><span class="duration"><i class="bi bi-clock me-1"></i>${escapeHtml(pkg.duration || 'Flexible')}</span></div></div></article></div>`;
        }).join(''));
    }
    function updateStats() {
        $('#stat-total').text(packages.length); $('#stat-active').text(packages.filter(p=>p.status==='active').length); $('#stat-featured').text(packages.filter(p=>p.featured).length);
        $('#stat-value').text('RM '+money(packages.reduce((sum,p)=>sum+(Number(p.price)||0),0)));
    }
    function loadPackages() {
        $.get(`${endpoint}?_t=${Date.now()}`).done(res => { packages = res.data || res || []; updateStats(); render(); }).fail(() => $('#package-grid').html(statePanel('bi-cloud-slash','Unable to load packages','Check your connection and try again.',`<button class="btn btn-primary rounded-pill px-4" id="retry-packages">Try again</button>`)));
    }
    $('#pkg-search').on('input', e => { query=e.target.value.trim().toLowerCase(); render(); });
    $('#category-filters').on('click','.filter-chip',function(){ $('.filter-chip').removeClass('active'); $(this).addClass('active'); activeFilter=$(this).data('filter'); render(); });
    $('#package-grid').on('click','#retry-packages',loadPackages).on('click','.js-delete',function(){ const id=$(this).data('id'); Swal.fire({title:'Delete this package?',text:'It will be removed from the active inventory.',icon:'warning',showCancelButton:true,confirmButtonText:'Delete package',confirmButtonColor:'#dc3545'}).then(r=>{if(r.isConfirmed) $.ajax({url:`/admin/packages/${id}`,type:'DELETE',headers:{'X-CSRF-TOKEN':csrf}}).done(()=>{packages=packages.filter(p=>p.id!=id);updateStats();render();Swal.fire('Deleted','The package was removed.','success');}).fail(xhr=>Swal.fire('Could not delete',xhr.responseJSON?.message||'Please try again.','error'));}); }).on('click','.js-duplicate',function(){ const id=$(this).data('id'); Swal.fire({title:'Duplicate package?',text:'A draft copy will be created.',icon:'question',showCancelButton:true,confirmButtonText:'Create copy'}).then(r=>{if(r.isConfirmed) $.post({url:`/admin/packages/${id}/duplicate`,headers:{'X-CSRF-TOKEN':csrf}}).done(()=>{Swal.fire('Copy created','The duplicate is ready to edit.','success');loadPackages();}).fail(xhr=>Swal.fire('Could not duplicate',xhr.responseJSON?.message||'Please try again.','error'));}); });
    loadPackages();
})();
</script>
@endpush
