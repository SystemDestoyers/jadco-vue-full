<!-- Contact Section -->
<section id="contact" class="contact-section section">
    @php
    // Get contact content from the sections collection
    $contactSection = isset($sections) ? $sections->where('name', 'contact')->first() : null;
    
    // Check if content is already an array or needs to be decoded
    if ($contactSection) {
        if (is_string($contactSection->content)) {
            $contactContent = json_decode($contactSection->content, true);
        } else {
            $contactContent = $contactSection->content;
        }
    } else {
        $contactContent = [];
    }
    @endphp
    <div class="container">
        <div class="row">
            <div class="contact-top">
                <a href="{{ url('/') }}" class="footer-logo">
                    <img src="{{ asset($contactContent['logo'] ?? 'images/logo.png') }}" alt="JADCO Logo" class="logo">
                </a>
            </div>
            <div class="col-lg-3 col-sm-3">
                <div class="contact-info">
                    <div class="locations">
                        @if(isset($contactContent['locations']) && is_array($contactContent['locations']))
                            @foreach($contactContent['locations'] as $index => $location)
                                <div class="location-item {{ $index > 0 ? 'mt-4' : '' }}">
                                    <h4 class="location-title">{{ $location['title'] ?? '' }}</h4>
                                    <p class="location-address">
                                        {!! $location['address'] ?? '' !!}
                                        @if(isset($location['contacts']) && is_array($location['contacts']))
                                            <br>
                                            @foreach($location['contacts'] as $contact)
                                                <span class="contact-label">{{ $contact['label'] ?? '' }}:</span>
                                                <span class="contact-value">
                                                    @if(($contact['type'] ?? '') == 'whatsapp')
                                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $contact['value'] ?? '') }}" class="whatsapp-link" target="_blank">{{ $contact['value'] ?? '' }}</a>
                                                    @elseif(($contact['type'] ?? '') == 'email')
                                                        <a href="mailto:{{ $contact['value'] ?? '' }}">{{ $contact['value'] ?? '' }}</a>
                                                    @else
                                                        {{ $contact['value'] ?? '' }}
                                                    @endif
                                                </span><br>
                                            @endforeach
                                        @endif
                                    </p>
                                </div>
                            @endforeach
                        @else
                            <div class="location-item">
                                <h4 class="location-title">Saudi Arabia</h4>
                                <p class="location-address">
                                    Level 7, Building 4.07, Zone 4<br>
                                    King Abdullah Financial District<br>
                                    (KAFD)<br>
                                    Riyadh 13519, Saudi Arabia.<br>
                                    <span class="contact-label">Tel:</span>
                                    <span class="contact-value">
                                        <a href="https://wa.me/966115256175" class="whatsapp-link" target="_blank">(+966) 115256175</a></span><br>
                                    <span class="contact-label">Mobile:</span> <span class="contact-value"><a
                                            href="https://wa.me/966569292048" class="whatsapp-link" target="_blank">(+966) 569292048</a></span><br>
                                    <span class="contact-label">Email: </span> 
                                    <span class="contact-value" style="padding-left: 5px">
                                        <a href="mailto:jad@jadco.co">jad@jadco.co</a>
                                    </span>
                                </p>
                            </div>

                            <div class="location-item mt-4">
                                <h4 class="location-title">USA</h4>
                                <p class="location-address">
                                    3972 Barranca Parkway,<br>
                                    Ste J139, Irvine, CA 92606
                                </p>
                            </div>

                            <div class="location-item mt-4">
                                <h4 class="location-title">UAE</h4>
                                <p class="location-address">
                                    A1, Dubai Digital Park, Dubai<br>
                                    Silicon Oasis, Dubai,<br>
                                    United Arab Emirates.
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-9 col-sm-9">
                @if(isset($contactContent['tagline']))
                    <h3 class="contact-tagline">{!! $contactContent['tagline'] !!}</h3>
                @endif
                @if(isset($contactContent['heading']))
                    <h2 class="let-talk">{!! $contactContent['heading'] !!}</h2>
                @endif

                <div class="contact-form">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    <form id="contactForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="text" class="form-control" id="firstName" name="first_name"
                                        placeholder=" " required>
                                    @if(isset($contactContent['form']['labels']['firstName']))
                                        <label for="firstName" class="form-label">{!! $contactContent['form']['labels']['firstName'] !!}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="text" class="form-control" id="lastName" name="last_name"
                                        placeholder=" " required>
                                    @if(isset($contactContent['form']['labels']['lastName']))
                                        <label for="lastName" class="form-label">{!! $contactContent['form']['labels']['lastName'] !!}</label>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="email" class="form-control" id="email" name="email"
                                        placeholder=" " required>
                                    @if(isset($contactContent['form']['labels']['email']))
                                        <label for="email" class="form-label">{!! $contactContent['form']['labels']['email'] !!}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="tel" class="form-control" id="phone" name="phone"
                                        placeholder=" ">
                                    @if(isset($contactContent['form']['labels']['phone']))
                                        <label for="phone" class="form-label">{!! $contactContent['form']['labels']['phone'] !!}</label>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <textarea class="form-control" id="message" name="message" rows="4" placeholder=" " required></textarea>
                            @if(isset($contactContent['form']['labels']['message']))
                                <label for="message" class="form-label">{!! $contactContent['form']['labels']['message'] !!}</label>
                            @endif
                        </div>
                        <div class="text-start">
                            <button type="submit" class="btn btn-send">
                                @if(isset($contactContent['form']['submitButton']))
                                    {!! $contactContent['form']['submitButton'] !!}
                                @endif
                                <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </form>

                    <div id="formSuccess" class="alert alert-success alert-dismissible fade" role="alert" style="display: none;">
                        Thank you for your message! We will get back to you soon.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>

                    <div id="formError" class="alert alert-danger alert-dismissible fade" role="alert" style="display: none;">
                        Sorry, there was a problem submitting your message. Please try again later.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>

                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            console.log('DOM loaded - initializing contact form');
                            
                            // Get elements
                            const form = document.getElementById('contactForm');
                            let successAlert = document.getElementById('formSuccess');
                            let errorAlert = document.getElementById('formError');
                            
                            // Create alerts if they don't exist
                            if (!successAlert) {
                                console.log('Success alert not found, creating it');
                                successAlert = document.createElement('div');
                                successAlert.id = 'formSuccess';
                                successAlert.className = 'alert alert-success alert-dismissible fade';
                                successAlert.role = 'alert';
                                successAlert.style.display = 'none';
                                successAlert.innerHTML = 'Thank you for your message! We will get back to you soon. <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
                                form.parentNode.insertBefore(successAlert, form.nextSibling);
                            }
                            
                            if (!errorAlert) {
                                console.log('Error alert not found, creating it');
                                errorAlert = document.createElement('div');
                                errorAlert.id = 'formError';
                                errorAlert.className = 'alert alert-danger alert-dismissible fade';
                                errorAlert.role = 'alert';
                                errorAlert.style.display = 'none';
                                errorAlert.innerHTML = 'Sorry, there was a problem submitting your message. Please try again later. <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
                                form.parentNode.insertBefore(errorAlert, successAlert.nextSibling);
                            }
                            
                            if (!form) {
                                console.error('Contact form not found!');
                                return;
                            }
                            
                            form.addEventListener('submit', function(e) {
                                console.log('Form submitted');
                                e.preventDefault();
                                
                                // Show loading indicator
                                const submitButton = form.querySelector('button[type="submit"]');
                                const originalButtonHtml = submitButton.innerHTML;
                                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
                                submitButton.disabled = true;
                                
                                // Get form data
                                const formData = new FormData(form);
                                
                                // Create XMLHttpRequest
                                const xhr = new XMLHttpRequest();
                                xhr.open('POST', '{{ route("contact.submit") }}', true);
                                
                                // Get CSRF token safely
                                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                                if (csrfToken) {
                                    xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken.getAttribute('content'));
                                } else {
                                    // If no CSRF token, include it from the form
                                    const csrfInput = form.querySelector('input[name="_token"]');
                                    if (csrfInput) {
                                        xhr.setRequestHeader('X-CSRF-TOKEN', csrfInput.value);
                                        console.log('Using CSRF token from form:', csrfInput.value);
                                    } else {
                                        console.warn('No CSRF token found!');
                                    }
                                }
                                
                                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                                xhr.setRequestHeader('Content-Type', 'application/json;charset=UTF-8');
                                
                                xhr.onload = function() {
                                    console.log('Got response:', xhr.status, xhr.responseText);
                                    submitButton.innerHTML = originalButtonHtml;
                                    submitButton.disabled = false;
                                    
                                    if (xhr.status === 200) {
                                        try {
                                            const response = JSON.parse(xhr.responseText);
                                            
                                            if (response.success) {
                                                // Show success message
                                                successAlert.style.display = 'block';
                                                successAlert.classList.add('show');
                                                
                                                // Reset form
                                                form.reset();
                                            } else {
                                                // Show error message
                                                errorAlert.style.display = 'block';
                                                errorAlert.classList.add('show');
                                                console.error('Server error:', response.message);
                                            }
                                        } catch (e) {
                                            console.error('Invalid JSON response', e);
                                            console.error('Response text:', xhr.responseText);
                                            errorAlert.style.display = 'block';
                                            errorAlert.classList.add('show');
                                        }
                                    } else {
                                        // Show error message
                                        errorAlert.style.display = 'block';
                                        errorAlert.classList.add('show');
                                        console.error('Request failed with status:', xhr.status);
                                    }
                                };
                                
                                xhr.onerror = function() {
                                    console.error('XHR error occurred');
                                    submitButton.innerHTML = originalButtonHtml;
                                    submitButton.disabled = false;
                                    errorAlert.style.display = 'block';
                                    errorAlert.classList.add('show');
                                    console.error('Request failed');
                                };
                                
                                // Convert FormData to standard form data format
                                const data = {};
                                formData.forEach((value, key) => {
                                    data[key] = value;
                                    console.log('Form data:', key, value);
                                });
                                
                                // Send the request
                                console.log('Sending form data', data);
                                xhr.send(JSON.stringify(data));
                            });
                            
                            // Hide alerts when close button is clicked
                            document.querySelectorAll('.alert .btn-close').forEach(function(button) {
                                button.addEventListener('click', function() {
                                    this.closest('.alert').style.display = 'none';
                                    this.closest('.alert').classList.remove('show');
                                });
                            });
                        });
                    </script>
                </div>
            </div>


        </div>
        <!-- Footer -->
        <div class="row mt-3 end-footer">
            <div class="col-lg-3 col-sm-3"></div>
            <div class="col-lg-9 col-sm-9">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="social-links">
                        @if(isset($contactContent['socialLinks']) && is_array($contactContent['socialLinks']))
                            @foreach($contactContent['socialLinks'] as $social)
                                <a href="{{ $social['url'] ?? '#' }}" class="social-link"><i class="{{ $social['icon'] ?? 'fab fa-link' }}"></i> {{ $social['title'] ?? '' }}</a>
                            @endforeach
                        @else
                            <a href="#" class="social-link"><i class="fab fa-youtube"></i> YouTube</a>
                            <a href="#" class="social-link"><i class="fab fa-linkedin"></i> LinkedIn</a>
                        @endif
                    </div>
                    <div>
                        <p class="copyright"> {{ date('Y') }} <span class="jadco-shine">JADCO</span>. 
                            @if(isset($contactContent['copyright']))
                                {!! $contactContent['copyright'] !!}
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
</section>
