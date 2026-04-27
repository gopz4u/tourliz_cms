@extends('layouts.admin')

@section('title', 'Manage Entry Tickets')

@section('content')
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2><i class="bi bi-ticket-perforated me-2"></i>Entry Tickets Master</h2>
                <p class="text-muted mb-0">Manage attraction ticket pricing</p>
            </div>
            <a href="{{ route('admin.entry-tickets.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-2"></i>Add Ticket
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th width="50">#</th>
                            <th>Attraction Name</th>
                            <th>Destination</th>
                            <th>Adult Price</th>
                            <th>Child Price</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tickets as $ticket)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td><strong>{{ $ticket->attraction_name }}</strong></td>
                                <td>{{ $ticket->destination->name ?? 'N/A' }}</td>
                                <td>$ {{ number_format($ticket->adult_price, 2) }}</td>
                                <td>{{ $ticket->child_price ? '$ ' . number_format($ticket->child_price, 2) : 'N/A' }}</td>
                                <td>
                                    @if($ticket->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.entry-tickets.edit', $ticket->id) }}"
                                        class="btn btn-sm btn-outline-primary me-1">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.entry-tickets.destroy', $ticket->id) }}" method="POST"
                                        class="d-inline" onsubmit="return confirm('Delete this ticket?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">No entry tickets found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $tickets->links() }}
        </div>
    </div>
@endsection