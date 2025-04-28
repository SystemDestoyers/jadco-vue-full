<template>
    <!-- Contact Section -->
    <section id="contact" class="contact-section section">
        <div class="container" v-if="!loading">
            <div class="row">
                <div class="contact-top">
                    <router-link to="/" class="footer-logo">
                        <img :src="content.logo || '/images/logo.png'" alt="JADCO Logo" class="logo">
                    </router-link>
                </div>
                <div class="col-lg-3 col-sm-3">
                    <div class="contact-info">
                        <div class="locations">
                            <div v-for="(location, index) in content.locations" :key="index" class="location-item" :class="{ 'mt-4': index > 0 }">
                                <h4 class="location-title" v-html="location.title"></h4>
                                <p class="location-address" v-html="location.address"></p>
                                <div v-if="location.contacts">
                                    <div v-for="(contact, cIndex) in location.contacts" :key="`contact-${cIndex}`">
                                        <span class="contact-label" v-html="contact.label + ':'"></span>
                                        <span class="contact-value">
                                            <a v-if="contact.type === 'whatsapp'" :href="`https://wa.me/${contact.value.replace(/\D/g, '')}`" class="whatsapp-link" target="_blank" v-html="contact.value"></a>
                                            <a v-else-if="contact.type === 'email'" :href="`mailto:${contact.value}`" v-html="contact.value"></a>
                                            <template v-else v-html="contact.value"></template>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-9 col-sm-9">
                    <h3 class="contact-tagline" v-if="content.tagline" v-html="content.tagline"></h3>
                    <h2 class="let-talk" v-if="content.heading" v-html="content.heading"></h2>

                    <div class="contact-form">
                        <form @submit.prevent="handleSubmit">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="firstName" v-model="firstName"
                                            placeholder=" " required>
                                        <label for="firstName" class="form-label" v-html="content.form?.labels?.firstName || 'First Name'"></label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="lastName" v-model="lastName"
                                            placeholder=" " required>
                                        <label for="lastName" class="form-label" v-html="content.form?.labels?.lastName || 'Last Name'"></label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input type="email" class="form-control" id="email" v-model="email"
                                            placeholder=" " required>
                                        <label for="email" class="form-label" v-html="content.form?.labels?.email || 'Email'"></label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input type="tel" class="form-control" id="phone" v-model="phone"
                                            placeholder=" ">
                                        <label for="phone" class="form-label" v-html="content.form?.labels?.phone || 'Phone Number'"></label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <textarea class="form-control" id="message" v-model="message" rows="4" placeholder=" " required></textarea>
                                <label for="message" class="form-label" v-html="content.form?.labels?.message || 'Message'"></label>
                            </div>
                            <div class="text-start">
                                <button type="submit" class="btn btn-send" v-html="(content.form?.submitButton || 'SEND A MESSAGE') + ' <i class=\'fas fa-arrow-right\'></i>'"></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="row mt-3 end-footer">
                <div class="col-lg-3 col-sm-3"></div>
                <div class="col-lg-9 col-sm-9">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="social-links">
                            <a v-for="(social, index) in content.socialLinks" :key="index" 
                               :href="social.url" class="social-link" target="_blank">
                                <i :class="social.icon"></i> <span v-html="social.title"></span>
                            </a>
                        </div>
                        <div>
                            <p class="copyright"> {{ currentYear }} <span class="jadco-shine">JADCO</span>. <span v-html="content.copyright || 'All Rights Reserved.'"></span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-else class="container loading-container">
            <div class="loading-spinner"></div>
            <p>Loading content...</p>
        </div>
    </section>
</template>

<script>
import axios from 'axios';
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';

export default {
    data() {
        return {
            loading: true,
            isSubmitting: false,
            errors: [],
            firstName: '',
            lastName: '',
            email: '',
            phone: '',
            message: '',
            currentYear: new Date().getFullYear(),
            content: {
                logo: '/images/logo.png',
                tagline: 'We Listen, design your vision and bring it to life...',
                heading: 'LET\'S TALK.',
                locations: [
                    {
                        title: 'Saudi Arabia',
                        address: 'Level 7, Building 4.07, Zone 4<br>King Abdullah Financial District<br>(KAFD)<br>Riyadh 13519, Saudi Arabia.',
                        contacts: [
                            { label: 'Tel', value: '(+966) 115256175', type: 'whatsapp' },
                            { label: 'Mobile', value: '(+966) 569292048', type: 'whatsapp' },
                            { label: 'Email', value: 'jad@jadco.co', type: 'email' }
                        ]
                    },
                    {
                        title: 'USA',
                        address: '3972 Barranca Parkway,<br>Ste J139, Irvine, CA 92606'
                    },
                    {
                        title: 'UAE',
                        address: 'A1, Dubai Digital Park, Dubai<br>Silicon Oasis, Dubai,<br>United Arab Emirates.'
                    }
                ],
                form: {
                    labels: {
                        firstName: 'First Name',
                        lastName: 'Last Name',
                        email: 'Email',
                        phone: 'Phone Number',
                        message: 'Message'
                    },
                    submitButton: 'SEND A MESSAGE',
                    successMessage: 'Thank you for your message. We will get back to you soon!'
                },
                socialLinks: [
                    { icon: 'fab fa-youtube', title: 'YouTube', url: '#' },
                    { icon: 'fab fa-linkedin', title: 'LinkedIn', url: '#' }
                ],
                copyright: 'All Rights Reserved.'
            }
        };
    },
    methods: {
        async fetchContactData() {
            try {
                // Get contact content from API
                const response = await axios.get('/api/contact/sections');
                
                if (response.data && response.data.success) {
                    const contactSection = response.data.data;
                    
                    if (contactSection && contactSection.content) {
                        // Parse content if needed
                        let content = contactSection.content;
                        if (typeof content === 'string') {
                            content = JSON.parse(content);
                        }
                        
                        // Only update the content properties, preserving component structure
                        if (content.logo) this.content.logo = content.logo;
                        if (content.tagline) this.content.tagline = content.tagline;
                        if (content.heading) this.content.heading = content.heading;
                        if (content.locations) this.content.locations = content.locations;
                        if (content.form) this.content.form = content.form;
                        if (content.socialLinks) this.content.socialLinks = content.socialLinks;
                        if (content.copyright) this.content.copyright = content.copyright;
                    }
                }
            } catch (error) {
                console.error('Error fetching contact data:', error);
                // Use default content on error
            } finally {
                this.loading = false;
            }
        },
        async handleSubmit() {
            this.isSubmitting = true;
            this.errors = [];
            
            try {
                // Format data for the new email endpoint
                const formData = {
                    name: `${this.firstName} ${this.lastName}`,
                    email: this.email,
                    phone: this.phone || 'Not provided',
                    message: this.message,
                    saveToDatabase: true // Explicitly tell controller to save to database
                };
                
                // Use our new email notification endpoint
                const response = await axios.post('/api/contact/email', formData, {
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                if (response.data.success) {
                    // Show success message
                    toast.success('Thank you for your message! We will get back to you soon.');
                    
                    // Reset form
                    this.resetForm();
                } else {
                    // Handle error
                    this.errors = response.data.errors || ['Failed to submit form. Please try again.'];
                    toast.error('Failed to submit your message. Please try again.');
                }
            } catch (error) {
                console.error('Error submitting contact form:', error);
                
                if (error.response && error.response.data && error.response.data.errors) {
                    // Laravel validation errors
                    const errorMessages = Object.values(error.response.data.errors).flat();
                    this.errors = errorMessages;
                    toast.error(errorMessages[0] || 'Form validation failed');
                } else {
                    this.errors = ['An error occurred. Please try again later.'];
                    toast.error('Failed to submit your message. Please try again later.');
                }
            } finally {
                this.isSubmitting = false;
            }
        },
        
        resetForm() {
            this.firstName = '';
            this.lastName = '';
            this.email = '';
            this.phone = '';
            this.message = '';
            this.errors = [];
        }
    },
    async created() {
        await this.fetchContactData();
    }
};
</script>

<style scoped>
.loading-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 300px;
    padding: 50px 0;
}

.loading-spinner {
    width: 50px;
    height: 50px;
    border: 5px solid #f3f3f3;
    border-top: 5px solid #e0285a;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin-bottom: 20px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style> 