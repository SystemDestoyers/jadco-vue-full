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
                                successAlert.innerHTML = `
                                    <strong>Thank you for your message!</strong> We will get back to you soon.
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    <div class="mt-3">
                                        <button type="button" class="btn btn-sm btn-outline-success send-another-btn">
                                            <i class="fas fa-paper-plane"></i> Send Another Message
                                        </button>
                                    </div>
                                `;
                                form.parentNode.insertBefore(successAlert, form.nextSibling);
                                
                                // Ensure close button has event listener
                                attachCloseButtonListeners();
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
                                
                                // Ensure close button has event listener
                                attachCloseButtonListeners();
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
                                
                                // Show loading overlay in the form
                                const loadingOverlay = document.getElementById('loadingOverlay');
                                console.log('Form container before loading:', form.parentNode);
                                if (loadingOverlay) {
                                    // Position the overlay on top of the form without hiding it
                                    loadingOverlay.style.display = 'flex';
                                    
                                    // Make sure form is still in the DOM but visually hidden by the overlay
                                    // We don't use display: none to avoid layout shifts
                                    form.style.opacity = '0.1';
                                    console.log('Loading overlay displayed, form opacity:', form.style.opacity);
                                }
                                
                                // Get form data
                                const formData = new FormData(form);
                                
                                // Format data like in Contact.vue
                                const data = {
                                    name: `${formData.get('first_name')} ${formData.get('last_name')}`,
                                    email: formData.get('email'),
                                    phone: formData.get('phone') || 'Not provided',
                                    message: formData.get('message'),
                                    saveToDatabase: true // Explicitly tell controller to save to database
                                };
                                
                                // Create XMLHttpRequest
                                const xhr = new XMLHttpRequest();
                                xhr.open('POST', '{{ route("contact.email") }}', true);
                                
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
                                    
                                    // Return button to original state
                                    submitButton.innerHTML = originalButtonHtml;
                                    submitButton.disabled = false;
                                    
                                    if (xhr.status === 200) {
                                        try {
                                            const response = JSON.parse(xhr.responseText);
                                            
                                            if (response.success) {
                                                // Reset form
                                                form.reset();
                                                
                                                // Keep showing the loading overlay for 2 seconds
                                                setTimeout(function() {
                                                    // Hide loading overlay
                                                    if (loadingOverlay) {
                                                        loadingOverlay.style.display = 'none';
                                                    }
                                                    
                                                    // Ensure form is fully visible
                                                    form.style.display = 'block';
                                                    form.style.opacity = '1';
                                                    console.log('Form should now be visible:', form.style.display, form.style.opacity);
                                                    
                                                    // Show inline success message in the form
                                                    const inlineSuccess = document.createElement('div');
                                                    inlineSuccess.className = 'alert alert-success mb-4';
                                                    inlineSuccess.innerHTML = '<i class="fas fa-check-circle"></i> Your message has been sent successfully!';
                                                    
                                                    // Insert at the top of the form
                                                    form.insertBefore(inlineSuccess, form.firstChild);
                                                    
                                                    // Remove the message after 5 seconds
                                                    setTimeout(function() {
                                                        inlineSuccess.remove();
                                                    }, 5000);
                                                    
                                                    // Focus on first input
                                                    const firstInput = form.querySelector('input[type="text"]');
                                                    if (firstInput) {
                                                        firstInput.focus();
                                                    }
                                                }, 2000);
                                            } else {
                                                // Hide loading overlay immediately
                                                if (loadingOverlay) {
                                                    loadingOverlay.style.display = 'none';
                                                }
                                                
                                                // Show error message
                                                errorAlert.style.display = 'block';
                                                errorAlert.classList.add('show');
                                                
                                                // Ensure close button works
                                                attachCloseButtonListeners();
                                                
                                                console.error('Server error:', response.message);
                                            }
                                        } catch (e) {
                                            // Hide loading overlay immediately
                                            if (loadingOverlay) {
                                                loadingOverlay.style.display = 'none';
                                            }
                                            
                                            console.error('Invalid JSON response', e);
                                            console.error('Response text:', xhr.responseText);
                                            errorAlert.style.display = 'block';
                                            errorAlert.classList.add('show');
                                        }
                                    } else {
                                        // Hide loading overlay immediately
                                        if (loadingOverlay) {
                                            loadingOverlay.style.display = 'none';
                                        }
                                        
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
                                    
                                    // Hide loading overlay immediately
                                    if (loadingOverlay) {
                                        loadingOverlay.style.display = 'none';
                                    }
                                    
                                    // Ensure form is fully visible
                                    form.style.display = 'block';
                                    form.style.opacity = '1';
                                    
                                    errorAlert.style.display = 'block';
                                    errorAlert.classList.add('show');
                                    console.error('Request failed');
                                };
                                
                                // Send the request
                                console.log('Sending form data', data);
                                xhr.send(JSON.stringify(data));
                            });
                            
                            // Hide alerts when close button is clicked
                            document.querySelectorAll('.alert .btn-close').forEach(function(button) {
                                button.addEventListener('click', function() {
                                    console.log('Close button clicked');
                                    const alert = this.closest('.alert');
                                    
                                    // Manually hide the alert since data-bs-dismiss might not be working
                                    alert.style.display = 'none';
                                    alert.classList.remove('show');
                                    
                                    // If this was the success alert, make sure the form is visible again
                                    if (alert.id === 'formSuccess') {
                                        form.style.display = 'block';
                                        
                                        // Focus on first input
                                        const firstInput = form.querySelector('input[type="text"]');
                                        if (firstInput) {
                                            firstInput.focus();
                                        }
                                    }
                                });
                            });

                            // Re-attach event listeners when new alerts are created
                            function attachCloseButtonListeners() {
                                document.querySelectorAll('.alert .btn-close').forEach(function(button) {
                                    // Remove existing listeners to prevent duplicates
                                    button.removeEventListener('click', handleCloseButtonClick);
                                    // Add new listener
                                    button.addEventListener('click', handleCloseButtonClick);
                                });
                            }

                            function handleCloseButtonClick() {
                                console.log('Close button clicked');
                                const alert = this.closest('.alert');
                                
                                // Manually hide the alert
                                alert.style.display = 'none';
                                alert.classList.remove('show');
                                
                                // If this was the success alert, make sure the form is visible again
                                if (alert.id === 'formSuccess') {
                                    const contactForm = document.getElementById('contactForm');
                                    if (contactForm) {
                                        contactForm.style.display = 'block';
                                        
                                        // Focus on first input
                                        const firstInput = contactForm.querySelector('input[type="text"]');
                                        if (firstInput) {
                                            firstInput.focus();
                                        }
                                    }
                                }
                            }

                            // Add event listener for "Send Another Message" button
                            document.addEventListener('click', function(e) {
                                if (e.target && e.target.classList.contains('send-another-btn') || 
                                    (e.target.parentElement && e.target.parentElement.classList.contains('send-another-btn'))) {
                                    // Hide success alert
                                    if (successAlert) {
                                        successAlert.style.display = 'none';
                                        successAlert.classList.remove('show');
                                    }
                                    
                                    // Show form again
                                    form.style.display = 'block';
                                    
                                    // Focus on first input
                                    const firstInput = form.querySelector('input[type="text"]');
                                    if (firstInput) {
                                        firstInput.focus();
                                    }
                                }
                            });

                            // Add direct click handler for all close buttons to ensure they work
                            document.querySelectorAll('.alert .btn-close').forEach(function(closeBtn) {
                                closeBtn.onclick = function() {
                                    console.log('Close button clicked directly');
                                    const alert = this.closest('.alert');
                                    alert.style.display = 'none';
                                    
                                    // If this was the success alert, show the form
                                    if (alert.id === 'formSuccess' && form) {
                                        form.style.display = 'block';
                                    }
                                    
                                    return false; // Prevent default and stop propagation
                                };
                            });

                            // Add loading overlay HTML after the form
                            if (!document.getElementById('loadingOverlay')) {
                                const loadingOverlay = document.createElement('div');
                                loadingOverlay.id = 'loadingOverlay';
                                loadingOverlay.className = 'loading-overlay';
                                loadingOverlay.style.display = 'none';
                                loadingOverlay.innerHTML = `
                                    <div class="loading-spinner">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <p class="mt-3">Sending your message...</p>
                                    </div>
                                `;
                                form.parentNode.insertBefore(loadingOverlay, form.nextSibling);
                            }

                            // Add CSS for the overlay
                            const style = document.createElement('style');
                            style.textContent = `
                                .loading-overlay {
                                    position: absolute;
                                    top: 0;
                                    left: 0;
                                    width: 100%;
                                    height: 100%;
                                    background-color: rgba(255, 255, 255, 0.9);
                                    display: flex;
                                    justify-content: center;
                                    align-items: center;
                                    z-index: 1000;
                                }
                                .loading-spinner {
                                    text-align: center;
                                }
                                /* Ensure contact form is visible by default */
                                #contactForm {
                                    display: block !important;
                                }
                            `;
                            document.head.appendChild(style);
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
