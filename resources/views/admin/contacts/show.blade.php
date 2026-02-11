@extends('admin.layout')

@section('title', 'Contact Message')
@section('page-title', 'Contact Message Details')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    @if(!$contact->is_read)
                    <span class="badge bg-danger me-2">Unread</span>
                    @endif
                    Message from {{ $contact->name }}
                </h5>
                <div class="btn-group">
                    @if(!$contact->is_read)
                    <form action="{{ route('admin.contacts.mark-read', $contact) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-success">
                            <i class="fas fa-check me-1"></i>Mark as Read
                        </button>
                    </form>
                    @endif
                    <a href="{{ route('admin.contacts.index') }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Back to List
                    </a>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Contact Information</h6>
                        <p class="mb-2">
                            <i class="fas fa-user text-primary me-2"></i>
                            <strong>{{ $contact->name }}</strong>
                        </p>
                        <p class="mb-2">
                            <i class="fas fa-envelope text-primary me-2"></i>
                            <a href="mailto:{{ $contact->email }}">{{ $contact->email }}</a>
                        </p>
                        @if($contact->phone)
                        <p class="mb-2">
                            <i class="fas fa-phone text-primary me-2"></i>
                            <a href="tel:{{ $contact->phone }}">{{ $contact->phone }}</a>
                        </p>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Message Details</h6>
                        @if($contact->subject)
                        <p class="mb-2">
                            <i class="fas fa-tag text-primary me-2"></i>
                            <strong>Subject:</strong> {{ $contact->subject }}
                        </p>
                        @endif
                        <p class="mb-2">
                            <i class="fas fa-calendar text-primary me-2"></i>
                            <strong>Received:</strong> {{ $contact->created_at->format('M d, Y h:i A') }}
                        </p>
                        @if($contact->attachment)
                        <p class="mb-2">
                            <i class="fas fa-paperclip text-primary me-2"></i>
                            <strong>Attachment:</strong> 
                            <a href="{{ asset('storage/' . $contact->attachment) }}" target="_blank">
                                Download <i class="fas fa-download ms-1"></i>
                            </a>
                        </p>
                        @endif
                    </div>
                </div>

                <hr>

                <div class="mt-4">
                    <h6 class="text-muted mb-3">Message</h6>
                    <div class="bg-light p-4 rounded">
                        <p class="mb-0" style="white-space: pre-wrap;">{{ $contact->message }}</p>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-white">
                <div class="d-flex justify-content-between">
                    <a href="mailto:{{ $contact->email }}?subject=Re: {{ $contact->subject }}" 
                       class="btn btn-primary">
                        <i class="fas fa-reply me-2"></i>Reply via Email
                    </a>
                    <form action="{{ route('admin.contacts.destroy', $contact) }}" 
                          method="POST" 
                          onsubmit="return confirm('Delete this message?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-2"></i>Delete Message
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h6 class="mb-0">Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="mailto:{{ $contact->email }}" class="btn btn-outline-primary">
                        <i class="fas fa-envelope me-2"></i>Send Email
                    </a>
                    @if($contact->phone)
                    <a href="tel:{{ $contact->phone }}" class="btn btn-outline-success">
                        <i class="fas fa-phone me-2"></i>Call
                    </a>
                    <a href="https://wa.me/{{ str_replace(['+', ' ', '-'], '', $contact->phone) }}" 
                       target="_blank"
                       class="btn btn-outline-success">
                        <i class="fab fa-whatsapp me-2"></i>WhatsApp
                    </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="mb-3">Message Info</h6>
                <div class="small">
                    <p class="mb-2">
                        <strong>Status:</strong> 
                        @if($contact->is_read)
                        <span class="badge bg-success">Read</span>
                        @else
                        <span class="badge bg-danger">Unread</span>
                        @endif
                    </p>
                    @if($contact->is_read && $contact->read_at)
                    <p class="mb-2">
                        <strong>Read at:</strong> {{ $contact->read_at->format('M d, Y h:i A') }}
                    </p>
                    @endif
                    <p class="mb-2">
                        <strong>Received:</strong> {{ $contact->created_at->diffForHumans() }}
                    </p>
                    <p class="mb-0">
                        <strong>ID:</strong> #{{ $contact->id }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
