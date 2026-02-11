@extends('admin.layout')

@section('title', 'Contacts')
@section('page-title', 'Contact Messages')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Contact Messages</h2>
    <div>
        <span class="badge bg-danger me-2">{{ $contacts->where('is_read', false)->count() }} Unread</span>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>Status</th>
                <th>Name</th>
                <th>Email</th>
                <th>Subject</th>
                <th>Message</th>
                <th>Date</th>
                <th style="width: 150px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($contacts as $contact)
            <tr class="{{ !$contact->is_read ? 'table-warning' : '' }}">
                <td>
                    @if($contact->is_read)
                    <i class="fas fa-envelope-open text-success" title="Read"></i>
                    @else
                    <i class="fas fa-envelope text-danger" title="Unread"></i>
                    @endif
                </td>
                <td>
                    <strong>{{ $contact->name }}</strong>
                    @if($contact->phone)
                    <br><small class="text-muted">{{ $contact->phone }}</small>
                    @endif
                </td>
                <td>
                    <a href="mailto:{{ $contact->email }}">{{ $contact->email }}</a>
                </td>
                <td>{{ $contact->subject ?? '-' }}</td>
                <td>
                    <div style="max-width: 300px;">
                        {{ Str::limit($contact->message, 100) }}
                    </div>
                </td>
                <td>
                    <small>{{ $contact->created_at->format('M d, Y') }}</small><br>
                    <small class="text-muted">{{ $contact->created_at->format('h:i A') }}</small>
                </td>
                <td>
                    <div class="btn-group">
                        <a href="{{ route('admin.contacts.show', $contact) }}" 
                           class="btn btn-sm btn-primary" 
                           title="View">
                            <i class="fas fa-eye"></i>
                        </a>
                        @if(!$contact->is_read)
                        <form action="{{ route('admin.contacts.mark-read', $contact) }}" 
                              method="POST" 
                              class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success" title="Mark as Read">
                                <i class="fas fa-check"></i>
                            </button>
                        </form>
                        @endif
                        <form action="{{ route('admin.contacts.destroy', $contact) }}" 
                              method="POST" 
                              class="d-inline" 
                              onsubmit="return confirm('Delete this message?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center py-5 text-muted">
                    <i class="fas fa-envelope fa-3x mb-3 d-block" style="opacity: 0.3;"></i>
                    No contact messages yet.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-3">
    {{ $contacts->links() }}
</div>
@endsection
