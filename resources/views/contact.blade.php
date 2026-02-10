@extends('layouts.app')

@section('title', 'Contact Us')

@section('content')
    <section class="py-5 bg-dark text-white">
        <div class="container text-center">
            <h1 class="display-4">Contact Us</h1>
            <p class="lead">Get in Touch for a Free Quote</p>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <div class="row">
                <!-- Contact Form -->
                <div class="col-lg-8 mb-4" data-aos="fade-right">
                    <div class="card border-0 shadow">
                        <div class="card-body p-4">
                            <h3 class="mb-4">Send Us a Message</h3>

                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            <form action="{{ route('contact.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label">Full Name *</label>
                                        <input type="text"
                                               class="form-control @error('name') is-invalid @enderror"
                                               id="name"
                                               name="name"
                                               value="{{ old('name') }}"
                                               required>
                                        @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">Email Address *</label>
                                        <input type="email"
                                               class="form-control @error('email') is-invalid @enderror"
                                               id="email"
                                               name="email"
                                               value="{{ old('email') }}"
                                               required>
                                        @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="phone" class="form-label">Phone Number</label>
                                        <input type="tel"
                                               class="form-control @error('phone') is-invalid @enderror"
                                               id="phone"
                                               name="phone"
                                               value="{{ old('phone') }}">
                                        @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="subject" class="form-label">Subject</label>
                                        <input type="text"
                                               class="form-control @error('subject') is-invalid @enderror"
                                               id="subject"
                                               name="subject"
                                               value="{{ old('subject') }}">
                                        @error('subject')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="message" class="form-label">Message *</label>
                                    <textarea class="form-control @error('message') is-invalid @enderror"
                                              id="message"
                                              name="message"
                                              rows="5"
                                              required>{{ old('message') }}</textarea>
                                    @error('message')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="attachment" class="form-label">Attachment (Optional)</label>
                                    <input type="file"
                                           class="form-control @error('attachment') is-invalid @enderror"
                                           id="attachment"
                                           name="attachment">
                                    <small class="text-muted">Upload drawings, images, or documents (Max 5MB)</small>
                                    @error('attachment')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <button type="submit" class="btn btn-primary-custom">
                                    <i class="fas fa-paper-plane me-2"></i>Send Message
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Contact Info -->
                <div class="col-lg-4" data-aos="fade-left">
                    <div class="card border-0 shadow mb-4">
                        <div class="card-body p-4">
                            <h5 class="mb-4">Contact Information</h5>
                            <div class="mb-3">
                                <i class="fas fa-map-marker-alt text-primary me-3"></i>
                                <strong>Address:</strong><br>
                                <span class="text-muted ms-4">{{ env('BUSINESS_ADDRESS') }}</span>
                            </div>
                            <div class="mb-3">
                                <i class="fas fa-phone text-primary me-3"></i>
                                <strong>Phone:</strong><br>
                                <a href="tel:{{ str_replace([' ', '-', '(', ')'], '', env('BUSINESS_PHONE')) }}"
                                   class="text-muted ms-4">
                                    {{ env('BUSINESS_PHONE') }}
                                </a>
                            </div>
                            <div class="mb-3">
                                <i class="fas fa-envelope text-primary me-3"></i>
                                <strong>Email:</strong><br>
                                <a href="mailto:{{ env('CONTACT_EMAIL') }}" class="text-muted ms-4">
                                    {{ env('CONTACT_EMAIL') }}
                                </a>
                            </div>
                            <div class="mb-3">
                                <i class="fab fa-whatsapp text-success me-3"></i>
                                <strong>WhatsApp:</strong><br>
                                <a href="https://wa.me/{{ str_replace(['+', ' ', '-'], '', env('WHATSAPP_NUMBER')) }}"
                                   target="_blank"
                                   class="text-muted ms-4">
                                    {{ env('WHATSAPP_NUMBER') }}
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow">
                        <div class="card-body p-4">
                            <h5 class="mb-3">Business Hours</h5>
                            <p class="mb-2"><strong>Monday - Thursday:</strong> 8:00 AM - 6:00 PM</p>
                            <p class="mb-2"><strong>Saturday - Sunday:</strong> 8:00 AM - 6:00 PM</p>
                            <p class="mb-0"><strong>Friday:</strong> Closed</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Map -->
            <div class="row mt-5">
                <div class="col-12" data-aos="fade-up">
                    <div class="card border-0 shadow">
                        <div class="card-body p-0">
                            <iframe
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3322.083070256584!2d73.11361987301636!3d33.62908803371136!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x38dfeb1646b1a581%3A0xc27c069b3e4ede8e!2sKhanna%20Pul%2C%20Khanna%20Islamabad%2C%20Pakistan!5e0!3m2!1sen!2s!4v1770709900974!5m2!1sen!2s"
                                width="100%" height="400" style="border:0;" allowfullscreen="" loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
