@extends('admin.layout')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<!-- Statistics Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center">
                <div class="stat-icon">
                    <i class="fas fa-fire"></i>
                </div>
                <div class="ms-3">
                    <h3 class="mb-0">{{ $stats['services'] }}</h3>
                    <p class="text-muted mb-0">Services</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center">
                <div class="stat-icon">
                    <i class="fas fa-briefcase"></i>
                </div>
                <div class="ms-3">
                    <h3 class="mb-0">{{ $stats['portfolios'] }}</h3>
                    <p class="text-muted mb-0">Portfolio Items</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center">
                <div class="stat-icon">
                    <i class="fas fa-blog"></i>
                </div>
                <div class="ms-3">
                    <h3 class="mb-0">{{ $stats['blog_posts'] }}</h3>
                    <p class="text-muted mb-0">Blog Posts</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center">
                <div class="stat-icon">
                    <i class="fas fa-envelope"></i>
                </div>
                <div class="ms-3">
                    <h3 class="mb-0">{{ $stats['unread_contacts'] }}</h3>
                    <p class="text-muted mb-0">Unread Messages</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Recent Contacts -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0">Recent Contacts</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Subject</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentContacts as $contact)
                            <tr>
                                <td>{{ $contact->name }}</td>
                                <td>{{ $contact->email }}</td>
                                <td>{{ Str::limit($contact->subject ?? 'No subject', 30) }}</td>
                                <td>{{ $contact->created_at->diffForHumans() }}</td>
                                <td>
                                    @if($contact->is_read)
                                    <span class="badge bg-success">Read</span>
                                    @else
                                    <span class="badge badge-unread">Unread</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.contacts.show', $contact) }}" class="btn btn-sm btn-primary">
                                        View
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">No contacts yet</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white">
                <a href="{{ route('admin.contacts.index') }}" class="btn btn-sm btn-outline-primary">
                    View All Contacts
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0">Quick Stats</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <span>Total Categories:</span>
                    <strong>{{ $stats['categories'] }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span>Total Testimonials:</span>
                    <strong>{{ $stats['testimonials'] }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span>Published Posts:</span>
                    <strong>{{ $stats['published_posts'] }}</strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span>Total Contacts:</span>
                    <strong>{{ $stats['contacts'] }}</strong>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.services.create') }}" class="btn btn-outline-primary">
                        <i class="fas fa-plus me-2"></i>Add Service
                    </a>
                    <a href="{{ route('admin.portfolios.create') }}" class="btn btn-outline-primary">
                        <i class="fas fa-plus me-2"></i>Add Portfolio
                    </a>
                    <a href="{{ route('admin.blog.create') }}" class="btn btn-outline-primary">
                        <i class="fas fa-plus me-2"></i>New Blog Post
                    </a>
                    <a href="{{ route('home') }}" target="_blank" class="btn btn-outline-secondary">
                        <i class="fas fa-eye me-2"></i>View Website
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
