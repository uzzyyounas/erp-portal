<footer class="footer">
    <div class="container">
        <div class="row">
            <!-- About Column -->
            <div class="col-lg-4 col-md-6 mb-4">
                <h5 class="mb-3">
                    <i class="fas fa-hard-hat text-primary"></i>
                    {{ env('BUSINESS_NAME', 'Professional Welding Services') }}
                </h5>
                <p>Expert welding and fabrication services with certified professionals. We deliver quality craftsmanship and reliable solutions for all your metalworking needs.</p>
                <div class="social-links mt-3">
                    <a href="#" target="_blank" title="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" target="_blank" title="Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#" target="_blank" title="LinkedIn">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                    <a href="#" target="_blank" title="YouTube">
                        <i class="fab fa-youtube"></i>
                    </a>
                </div>
            </div>

            <!-- Quick Links Column -->
            <div class="col-lg-2 col-md-6 mb-4">
                <h5 class="mb-3">Quick Links</h5>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <a href="{{ route('home') }}" class="text-decoration-none">
                            <i class="fas fa-angle-right text-primary me-2"></i>Home
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="{{ route('about') }}" class="text-decoration-none">
                            <i class="fas fa-angle-right text-primary me-2"></i>About Us
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="{{ route('services.index') }}" class="text-decoration-none">
                            <i class="fas fa-angle-right text-primary me-2"></i>Services
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="{{ route('portfolio.index') }}" class="text-decoration-none">
                            <i class="fas fa-angle-right text-primary me-2"></i>Portfolio
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="{{ route('contact') }}" class="text-decoration-none">
                            <i class="fas fa-angle-right text-primary me-2"></i>Contact
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Services Column -->
            <div class="col-lg-3 col-md-6 mb-4">
                <h5 class="mb-3">Our Services</h5>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="fas fa-fire text-primary me-2"></i>MIG Welding
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-fire text-primary me-2"></i>TIG Welding
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-fire text-primary me-2"></i>Stick Welding
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-fire text-primary me-2"></i>Metal Fabrication
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-fire text-primary me-2"></i>Structural Welding
                    </li>
                </ul>
            </div>

            <!-- Contact Column -->
            <div class="col-lg-3 col-md-6 mb-4">
                <h5 class="mb-3">Contact Info</h5>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="fas fa-map-marker-alt text-primary me-2"></i>
                        {{ env('BUSINESS_ADDRESS', '123 Industrial Ave, Your City') }}
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-phone text-primary me-2"></i>
                        <a href="tel:{{ str_replace([' ', '-', '(', ')'], '', env('BUSINESS_PHONE')) }}" class="text-decoration-none">
                            {{ env('BUSINESS_PHONE', '+1 (555) 123-4567') }}
                        </a>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-envelope text-primary me-2"></i>
                        <a href="mailto:{{ env('CONTACT_EMAIL') }}" class="text-decoration-none">
                            {{ env('CONTACT_EMAIL', 'info@weldingpro.com') }}
                        </a>
                    </li>
                    <li class="mb-2">
                        <i class="fab fa-whatsapp text-success me-2"></i>
                        <a href="https://wa.me/{{ str_replace(['+', ' ', '-'], '', env('WHATSAPP_NUMBER')) }}"
                           class="text-decoration-none"
                           target="_blank">
                            WhatsApp Chat
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <hr class="my-4" style="border-color: rgba(255, 255, 255, 0.1);">

        <div class="row">
            <div class="col-md-6 text-center text-md-start">
                <p class="mb-0">
                    &copy; {{ date('Y') }} {{ config('app.name') }}. All Rights Reserved.
                </p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <p class="mb-0">
                    Powered by Uzzytech
                </p>
            </div>
        </div>
    </div>
</footer>
