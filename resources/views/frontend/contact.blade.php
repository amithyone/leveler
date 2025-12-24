@extends('layouts.frontend')

@section('title', 'Contact Us - Leveler')

@section('content')
<section class="page-header">
    <div class="container">
        <h1>Contact Us</h1>
    </div>
</section>

<section class="page-content">
    <div class="container">
        <div class="contact-grid">
            <div class="contact-info">
                <h2>Get in Touch</h2>
                @php
                    $contactDetails = $page->contact_details ?? [];
                    $address = $contactDetails['address'] ?? 'Nigeria';
                    $addressLine2 = $contactDetails['address_line2'] ?? 'Plot 559c, Capital Str., A11, Garki, Abuja';
                    $phone = $contactDetails['phone'] ?? '(+234) 806-141-3675';
                    $email = $contactDetails['email'] ?? '';
                    $workingHours = $contactDetails['working_hours'] ?? 'Mon - Fri: 9.00 to 17.00';
                @endphp
                
                <div class="contact-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <div>
                        <h3>Location</h3>
                        @if($address)
                            <p>{{ $address }}</p>
                        @endif
                        @if($addressLine2)
                            <p>{{ $addressLine2 }}</p>
                        @endif
                    </div>
                </div>
                @if($phone)
                <div class="contact-item">
                    <i class="fas fa-phone"></i>
                    <div>
                        <h3>Phone</h3>
                        <p>{{ $phone }}</p>
                    </div>
                </div>
                @endif
                @if($email)
                <div class="contact-item">
                    <i class="fas fa-envelope"></i>
                    <div>
                        <h3>Email</h3>
                        <p><a href="mailto:{{ $email }}">{{ $email }}</a></p>
                    </div>
                </div>
                @endif
                @if($workingHours)
                <div class="contact-item">
                    <i class="fas fa-clock"></i>
                    <div>
                        <h3>Working Hours</h3>
                        <p>{{ $workingHours }}</p>
                    </div>
                </div>
                @endif
            </div>
            <div class="contact-form">
                <h2>Send us a Message</h2>
                <form>
                    <div class="form-group">
                        <input type="text" placeholder="Your Name" required>
                    </div>
                    <div class="form-group">
                        <input type="email" placeholder="Your Email" required>
                    </div>
                    <div class="form-group">
                        <input type="text" placeholder="Subject" required>
                    </div>
                    <div class="form-group">
                        <textarea rows="5" placeholder="Your Message" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Send Message</button>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection

