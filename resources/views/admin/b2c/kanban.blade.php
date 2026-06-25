@extends('layouts.admin')

@section('title', 'B2C Pipeline — Kanban')

@push('styles')
<style>
    /* ── Layout ───────────────────────────────────────────── */
    .kanban-wrapper {
        overflow-x: auto;
        padding-bottom: 2rem;
    }
    .kanban-board {
        display: flex;
        gap: 1rem;
        min-width: max-content;
        align-items: flex-start;
    }

    /* ── Column ───────────────────────────────────────────── */
    .kanban-col {
        width: 272px;
        flex-shrink: 0;
        background: #f0f2f5;
        border-radius: 12px;
        display: flex;
        flex-direction: column;
        max-height: calc(100vh - 220px);
    }
    .kanban-col-header {
        padding: 12px 14px 10px;
        border-radius: 12px 12px 0 0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: sticky;
        top: 0;
        z-index: 2;
    }
    .kanban-col-header .col-title {
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: 0.05em;
        text-transform: uppercase;
    }
    .kanban-col-header .col-count {
        font-size: 0.72rem;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 20px;
        background: rgba(255,255,255,0.35);
    }

    /* Column colour accents */
    .col-leads    .kanban-col-header { background: #6366f1; color: #fff; }
    .col-quoted   .kanban-col-header { background: #f59e0b; color: #fff; }
    .col-interested .kanban-col-header { background: #3b82f6; color: #fff; }
    .col-confirmed  .kanban-col-header { background: #10b981; color: #fff; }
    .col-travelled  .kanban-col-header { background: #6b7280; color: #fff; }

    .col-leads    { border-top: 3px solid #6366f1; }
    .col-quoted   { border-top: 3px solid #f59e0b; }
    .col-interested { border-top: 3px solid #3b82f6; }
    .col-confirmed  { border-top: 3px solid #10b981; }
    .col-travelled  { border-top: 3px solid #6b7280; }

    /* ── Scroll body ──────────────────────────────────────── */
    .kanban-cards {
        padding: 10px 8px 12px;
        overflow-y: auto;
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 8px;
        min-height: 80px;
    }

    /* ── Card ─────────────────────────────────────────────── */
    .kanban-card {
        background: #fff;
        border-radius: 9px;
        padding: 12px 13px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08), 0 1px 2px rgba(0,0,0,0.04);
        cursor: grab;
        transition: box-shadow .15s, transform .15s;
        border: 1.5px solid transparent;
        position: relative;
    }
    .kanban-card:hover {
        box-shadow: 0 4px 14px rgba(0,0,0,0.13);
        transform: translateY(-1px);
        border-color: #e2e8f0;
    }
    .kanban-card.sortable-ghost {
        opacity: 0.4;
        background: #e0e7ff;
        border: 2px dashed #6366f1;
    }
    .kanban-card.sortable-drag {
        box-shadow: 0 10px 30px rgba(0,0,0,0.18);
        cursor: grabbing;
    }

    .card-id {
        font-size: 0.63rem;
        font-weight: 700;
        color: #6366f1;
        letter-spacing: 0.04em;
        margin-bottom: 4px;
    }
    .card-name {
        font-size: 0.85rem;
        font-weight: 700;
        color: #1e293b;
        line-height: 1.2;
        margin-bottom: 3px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .card-dest {
        font-size: 0.72rem;
        color: #64748b;
        margin-bottom: 6px;
    }
    .card-meta {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
        margin-top: 6px;
    }
    .card-price {
        font-size: 0.75rem;
        font-weight: 700;
        color: #0f172a;
    }
    .card-pax {
        font-size: 0.68rem;
        color: #64748b;
    }
    .card-source {
        font-size: 0.6rem;
        padding: 1px 6px;
        border-radius: 4px;
        background: #f1f5f9;
        color: #475569;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        border: 1px solid #e2e8f0;
    }
    .card-phone {
        font-size: 0.68rem;
        color: #475569;
    }
    .card-actions {
        position: absolute;
        top: 8px;
        right: 8px;
        display: flex;
        gap: 4px;
        opacity: 0;
        transition: opacity .15s;
    }
    .kanban-card:hover .card-actions { opacity: 1; }
    .card-actions a, .card-actions button {
        width: 24px;
        height: 24px;
        border-radius: 5px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #475569;
        cursor: pointer;
        text-decoration: none;
        transition: background .12s;
    }
    .card-actions a:hover { background: #e0e7ff; color: #4338ca; }
    .card-actions button:hover { background: #fee2e2; color: #dc2626; border-color: #fca5a5; }

    .card-date {
        font-size: 0.63rem;
        color: #94a3b8;
        margin-top: 4px;
    }
    .card-followup {
        font-size: 0.65rem;
        color: #ef4444;
        font-weight: 600;
        margin-top: 2px;
    }

    /* ── Empty state ──────────────────────────────────────── */
    .kanban-empty {
        text-align: center;
        padding: 20px 10px;
        color: #94a3b8;
        font-size: 0.75rem;
        border: 2px dashed #e2e8f0;
        border-radius: 8px;
        user-select: none;
        pointer-events: none;
    }

    /* ── Toast ────────────────────────────────────────────── */
    #kanban-toast {
        position: fixed;
        bottom: 28px;
        right: 28px;
        z-index: 9999;
        padding: 10px 18px;
        border-radius: 8px;
        background: #1e293b;
        color: #fff;
        font-size: 0.82rem;
        font-weight: 600;
        box-shadow: 0 4px 18px rgba(0,0,0,0.25);
        opacity: 0;
        transform: translateY(10px);
        transition: opacity .25s, transform .25s;
        pointer-events: none;
    }
    #kanban-toast.show {
        opacity: 1;
        transform: translateY(0);
    }
    #kanban-toast.error { background: #dc2626; }

    /* ── Loading overlay on card ──────────────────────────── */
    .card-saving::after {
        content: '';
        position: absolute;
        inset: 0;
        background: rgba(255,255,255,0.7);
        border-radius: 9px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>
@endpush

@section('content')
<div class="page-header mb-3">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h2><i class="bi bi-kanban me-2"></i>B2C Pipeline — Kanban</h2>
            <p class="text-muted mb-0 small">Drag cards between columns to update lead stage instantly</p>
        </div>
        <div class="d-flex gap-2 align-items-center">
            <!-- View Toggle -->
            <div class="btn-group" role="group">
                <a href="{{ route('admin.b2c-itineraries.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-table me-1"></i>Table
                </a>
                <a href="{{ route('admin.b2c-itineraries.kanban') }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-kanban me-1"></i>Kanban
                </a>
            </div>
            <a href="{{ route('admin.b2c-itineraries.create') }}" class="btn btn-sm btn-success">
                <i class="bi bi-person-plus me-1"></i>New Lead
            </a>
        </div>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- Stats strip --}}
<div class="d-flex gap-3 mb-3 flex-wrap">
    @php
    $stageConfig = [
        'leads'      => ['label' => 'Leads',      'icon' => 'bi-person-plus',      'color' => '#6366f1'],
        'quoted'     => ['label' => 'Quoted',     'icon' => 'bi-file-earmark-text', 'color' => '#f59e0b'],
        'interested' => ['label' => 'Interested', 'icon' => 'bi-heart',            'color' => '#3b82f6'],
        'confirmed'  => ['label' => 'Confirmed',  'icon' => 'bi-check-circle',     'color' => '#10b981'],
        'travelled'  => ['label' => 'Travelled',  'icon' => 'bi-airplane',         'color' => '#6b7280'],
    ];
    @endphp
    @foreach($stageConfig as $key => $cfg)
    <div class="d-flex align-items-center gap-2 px-3 py-2 rounded-3 bg-white shadow-sm border"
         style="min-width:120px;">
        <i class="bi {{ $cfg['icon'] }}" style="color:{{ $cfg['color'] }};font-size:1.1rem;"></i>
        <div>
            <div class="fw-bold" style="font-size:1.1rem;line-height:1;">{{ $columns[$key]->count() }}</div>
            <div style="font-size:0.65rem;color:#64748b;text-transform:uppercase;letter-spacing:.04em;">{{ $cfg['label'] }}</div>
        </div>
    </div>
    @endforeach
</div>

<div class="kanban-wrapper">
    <div class="kanban-board">

        @foreach($stageConfig as $stageKey => $cfg)
        <div class="kanban-col col-{{ $stageKey }}">
            <div class="kanban-col-header">
                <span class="col-title"><i class="bi {{ $cfg['icon'] }} me-1"></i>{{ $cfg['label'] }}</span>
                <span class="col-count" id="count-{{ $stageKey }}">{{ $columns[$stageKey]->count() }}</span>
            </div>

            <div class="kanban-cards"
                 id="col-{{ $stageKey }}"
                 data-stage="{{ $stageKey }}">

                @forelse($columns[$stageKey] as $lead)
                <div class="kanban-card"
                     data-id="{{ $lead->id }}"
                     data-stage="{{ $stageKey }}"
                     id="card-{{ $lead->id }}">

                    {{-- Quick action buttons --}}
                    <div class="card-actions">
                        <a href="{{ route('admin.b2c-itineraries.edit', $lead->id) }}"
                           title="Edit" onclick="event.stopPropagation()">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <a href="{{ route('admin.b2c-itineraries.pdf', $lead->id) }}"
                           title="PDF" onclick="event.stopPropagation()">
                            <i class="bi bi-file-pdf"></i>
                        </a>
                        <form action="{{ route('admin.b2c-itineraries.destroy', $lead->id) }}"
                              method="POST" class="d-inline"
                              onsubmit="return confirm('Delete {{ addslashes($lead->client_name) }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" title="Delete"
                                    onclick="event.stopPropagation()">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>

                    <div class="card-id">{{ $lead->quote_id }}</div>
                    <div class="card-name" title="{{ $lead->client_name }}">{{ $lead->client_name }}</div>
                    <div class="card-dest">
                        <i class="bi bi-geo-alt" style="font-size:.65rem;"></i>
                        {{ $lead->destination->name ?? '—' }}
                        &bull; {{ $lead->duration_days }}D
                    </div>

                    @if($lead->phone)
                    <div class="card-phone"><i class="bi bi-telephone" style="font-size:.6rem;"></i> {{ $lead->phone }}</div>
                    @endif

                    <div class="card-meta">
                        <span class="card-price">{{ $lead->currency }} {{ number_format($lead->total_price, 0) }}</span>
                        <span class="card-pax"><i class="bi bi-people"></i> {{ $lead->adults }}A
                            @if($lead->children_2_6 > 0) +{{ $lead->children_2_6 }}C @endif
                        </span>
                        <span class="card-source">{{ str_replace('_', ' ', $lead->lead_source ?? '—') }}</span>
                    </div>

                    @if($lead->next_followup_date)
                    <div class="card-followup">
                        <i class="bi bi-alarm"></i> Followup: {{ $lead->next_followup_date->format('d M') }}
                    </div>
                    @endif

                    <div class="card-date">Updated {{ $lead->updated_at->diffForHumans() }}</div>
                </div>
                @empty
                <div class="kanban-empty" id="empty-{{ $stageKey }}">
                    <i class="bi bi-inbox" style="font-size:1.5rem;display:block;margin-bottom:6px;"></i>
                    No leads here yet
                </div>
                @endforelse

            </div>
        </div>
        @endforeach

    </div>
</div>

{{-- Toast notification --}}
<div id="kanban-toast"></div>
@endsection

@push('scripts')
{{-- SortableJS via CDN --}}
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>

<script>
(function () {
    'use strict';

    const CSRF    = '{{ csrf_token() }}';
    const UPDATE_URL = (id) => `/admin/b2c-itineraries/${id}/status`;
    const toast   = document.getElementById('kanban-toast');
    let toastTimer;

    function showToast(msg, isError = false) {
        clearTimeout(toastTimer);
        toast.textContent = msg;
        toast.className = 'show' + (isError ? ' error' : '');
        toastTimer = setTimeout(() => { toast.className = ''; }, 3000);
    }

    function updateCount(stageKey) {
        const col   = document.getElementById('col-' + stageKey);
        const count = col.querySelectorAll('.kanban-card').length;
        document.getElementById('count-' + stageKey).textContent = count;

        // Show/hide empty placeholder
        const emptyEl = document.getElementById('empty-' + stageKey);
        if (emptyEl) emptyEl.style.display = count === 0 ? '' : 'none';
    }

    function ensureEmptyEl(stageKey) {
        let emptyEl = document.getElementById('empty-' + stageKey);
        if (!emptyEl) {
            const col = document.getElementById('col-' + stageKey);
            emptyEl = document.createElement('div');
            emptyEl.className = 'kanban-empty';
            emptyEl.id = 'empty-' + stageKey;
            emptyEl.innerHTML = '<i class="bi bi-inbox" style="font-size:1.5rem;display:block;margin-bottom:6px;"></i>No leads here yet';
            col.appendChild(emptyEl);
        }
    }

    // Init sortable on every column
    document.querySelectorAll('.kanban-cards').forEach(function (el) {
        Sortable.create(el, {
            group:     'leads',
            animation: 150,
            ghostClass: 'sortable-ghost',
            dragClass:  'sortable-drag',
            handle:     '.kanban-card',

            onEnd: function (evt) {
                const card      = evt.item;
                const newStage  = evt.to.dataset.stage;
                const oldStage  = evt.from.dataset.stage;
                const cardId    = card.dataset.id;

                if (newStage === oldStage) return; // No stage change, skip

                // Optimistic UI – update data attr
                card.dataset.stage = newStage;

                // Update counts
                updateCount(newStage);
                updateCount(oldStage);
                ensureEmptyEl(oldStage);

                // Persist via AJAX
                card.classList.add('card-saving');

                fetch(UPDATE_URL(cardId), {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ status: newStage }),
                })
                .then(r => r.json())
                .then(data => {
                    card.classList.remove('card-saving');
                    if (data.success) {
                        showToast('✓ Moved to ' + newStage.charAt(0).toUpperCase() + newStage.slice(1));
                    } else {
                        showToast('⚠ Failed to save', true);
                    }
                })
                .catch(() => {
                    card.classList.remove('card-saving');
                    showToast('⚠ Network error — status not saved', true);
                });
            }
        });
    });

    // Initialise all empty-state visibility
    document.querySelectorAll('.kanban-cards').forEach(function (col) {
        const stageKey = col.dataset.stage;
        ensureEmptyEl(stageKey);
        updateCount(stageKey);
    });
})();
</script>
@endpush
