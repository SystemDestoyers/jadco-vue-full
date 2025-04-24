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
                                <h4 class="location-title">{{ location.title }}</h4>
                                <p class="location-address" v-html="location.address"></p>
                                <div v-if="location.contacts">
                                    <div v-for="(contact, cIndex) in location.contacts" :key="`contact-${cIndex}`">
                                        <span class="contact-label">{{ contact.label }}:</span>
                                        <span class="contact-value">
                                            <a v-if="contact.type === 'whatsapp'" :href="`https://wa.me/${contact.value.replace(/\D/g, '')}`" class="whatsapp-link" target="_blank">{{ contact.value }}</a>
                                            <a v-else-if="contact.type === 'email'" :href="`mailto:${contact.value}`">{{ contact.value }}</a>
                                            <template v-else>{{ contact.value }}</template>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-9 col-sm-9">
                    <h3 class="contact-tagline">{{ content.tagline || 'We Listen, design your vision and bring it to life...' }}</h3>
                    <h2 class="let-talk">{{ content.heading || 'LET\'S TALK.' }}</h2>

                    <div class="contact-form">
                        <form @submit.prevent="submitForm">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="firstName" v-model="form.first_name"
                                            placeholder=" " required>
                                        <label for="firstName" class="form-label">{{ content.form?.labels?.firstName || 'First Name' }}</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="lastName" v-model="form.last_name"
                                            placeholder=" " required>
                                        <label for="lastName" class="form-label">{{ content.form?.labels?.lastName || 'Last Name' }}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input type="email" class="form-control" id="email" v-model="form.email"
                                            placeholder=" " required>
                                        <label for="email" class="form-label">{{ content.form?.labels?.email || 'Email' }}</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input type="tel" class="form-control" id="phone" v-model="form.phone"
                                            placeholder=" ">
                                        <label for="phone" class="form-label">{{ content.form?.labels?.phone || 'Phone Number' }}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <textarea class="form-control" id="message" v-model="form.message" rows="4" placeholder=" " required></textarea>
                                <label for="message" class="form-label">{{ content.form?.labels?.message || 'Message' }}</label>
                            </div>
                            <div class="text-start">
                                <button type="submit" class="btn btn-send">{{ content.form?.submitButton || 'SEND A MESSAGE' }} <i
                                        class="fas fa-arrow-right"></i></button>
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
                                <i :class="social.icon"></i> {{ social.title }}
                            </a>
                        </div>
                        <div>
                            <p class="copyright"> {{ currentYear }} <span class="jadco-shine">JADCO</span>. {{ content.copyright || 'All Rights Reserved.' }}</p>
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

export default {
    data() {
        return {
            loading: true,
            form: {
                first_name: '',
                last_name: '',
                email: '',
                phone: '',
                message: ''
            },
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
        submitForm() {
            // Add CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            
            axios.post('/api/contact/submit', this.form, {
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(response => {
                // Clear the form
                this.form = {
                    first_name: '',
                    last_name: '',
                    email: '',
                    phone: '',
                    message: ''
                };
                
                // Show success message
                alert(this.content.form?.successMessage || 'Thank you for your message. We will get back to you soon!');
            })
            .catch(error => {
                console.error('Error submitting the form:', error);
                alert('There was an error submitting your message. Please try again later.');
            });
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