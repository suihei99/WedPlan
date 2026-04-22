@extends('couple.layout.layout-couple')

@section('title', $guest->name . ' - Guest Details - WebPlan')
@section('page-title', $guest->name)
@section('page-subtitle', 'View guest details and send personalized invitation.')

@push('page-styles')
    @vite(['resources/css/couple/guests.css'])
@endpush

@section('content')
    <div class="guests-page">
        @if(session('success'))
            <section class="guests-flash guests-flash-success" role="status">
                <strong>Success</strong>
                <span>{{ session('success') }}</span>
            </section>
        @endif

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
            <!-- Guest Information -->
            <section style="background: #fff; border: 1px solid #efd7df; border-radius: 0.85rem; padding: 1.5rem;">
                <h2 style="margin: 0 0 1rem; font-size: 1.1rem; color: #201419;">Guest Information</h2>

                <div style="display: grid; gap: 1rem;">
                    <!-- Name -->
                    <div>
                        <label style="display: block; font-size: 0.8rem; color: #876f79; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 0.3rem;">Full Name</label>
                        <p style="margin: 0; font-size: 1rem; font-weight: 600; color: #201419;">{{ $guest->name }}</p>
                    </div>

                    <!-- Contact -->
                    <div>
                        <label style="display: block; font-size: 0.8rem; color: #876f79; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 0.3rem;">WhatsApp Contact</label>
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $guest->contact_number) }}" target="_blank" style="font-size: 1rem; color: #d54c6d; text-decoration: none; font-weight: 600;">
                            📱 {{ $guest->contact_number }}
                        </a>
                    </div>

                    <!-- Email -->
                    @if($guest->email)
                    <div>
                        <label style="display: block; font-size: 0.8rem; color: #876f79; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 0.3rem;">Email</label>
                        <a href="mailto:{{ $guest->email }}" style="font-size: 1rem; color: #d54c6d; text-decoration: none; font-weight: 600;">
                            {{ $guest->email }}
                        </a>
                    </div>
                    @endif

                    <!-- Plus Ones -->
                    <div>
                        <label style="display: block; font-size: 0.8rem; color: #876f79; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 0.3rem;">Plus Ones</label>
                        <p style="margin: 0; font-size: 1rem; font-weight: 600; color: #d54c6d;">{{ $guest->plus_one_count ?? 0 }}</p>
                    </div>

                    <!-- Dietary Preference -->
                    <div>
                        <label style="display: block; font-size: 0.8rem; color: #876f79; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 0.3rem;">Dietary Preference</label>
                        <p style="margin: 0; font-size: 1rem; font-weight: 600; color: #201419;">{{ ucfirst($guest->dietary_preference ?? 'Not specified') }}</p>
                    </div>

                    <!-- RSVP Status -->
                    <div>
                        <label style="display: block; font-size: 0.8rem; color: #876f79; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 0.3rem;">RSVP Status</label>
                        <div style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.35rem 0.65rem; background: #fff; border: 1px solid #efd7df; border-radius: 999px; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; color: #876f79;">
                            <span>●</span>
                            @php
                                $rsvpStatus = strtolower($guest->rsvp_status ?? 'pending');
                                $statusLabel = match($rsvpStatus) {
                                    'confirmed' => 'Confirmed',
                                    'declined' => 'Declined',
                                    'pending' => 'Pending Response',
                                    default => 'Not Invited',
                                };
                            @endphp
                            {{ $statusLabel }}
                        </div>
                    </div>

                    <!-- Invitation Sent -->
                    <div>
                        <label style="display: block; font-size: 0.8rem; color: #876f79; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 0.3rem;">Invitation Sent</label>
                        <p style="margin: 0; font-size: 1rem; font-weight: 600; color: {{ $guest->invitation_sent_at ? '#28603a' : '#8b5f1c' }};">
                            {{ $guest->invitation_sent_at ? '✓ ' . $guest->invitation_sent_at->format('M j, Y H:i') : '— Not sent yet' }}
                        </p>
                    </div>

                    <!-- Notes -->
                    @if($guest->notes)
                    <div style="padding-top: 1rem; border-top: 1px solid #efd7df;">
                        <label style="display: block; font-size: 0.8rem; color: #876f79; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 0.3rem;">Notes</label>
                        <p style="margin: 0; font-size: 0.9rem; color: #715b64; line-height: 1.4;">{{ $guest->notes }}</p>
                    </div>
                    @endif
                </div>

                <!-- Action Buttons -->
                <div style="display: flex; flex-direction: column; gap: 0.8rem; margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid #efd7df;">
                    @if(Route::has('couple.guests.update'))
                        <a href="{{ route('couple.guests.update', $guest) }}" style="display: inline-flex; align-items: center; justify-content: center; gap: 0.4rem; padding: 0.55rem 0.95rem; background: linear-gradient(135deg, #d54c6d 0%, #c23f5d 100%); color: #fff; border: none; border-radius: 0.7rem; font-size: 0.9rem; font-weight: 600; cursor: pointer; text-decoration: none; transition: all 0.2s ease;">
                            ✏️ Edit Guest
                        </a>
                    @endif

                    @if(!$guest->invitation_sent_at && Route::has('couple.guests.checkin'))
                        <button type="button" id="sendInviteBtn" style="display: inline-flex; align-items: center; justify-content: center; gap: 0.4rem; padding: 0.55rem 0.95rem; background: linear-gradient(135deg, #25a329 0%, #1f8621 100%); color: #fff; border: none; border-radius: 0.7rem; font-size: 0.9rem; font-weight: 600; cursor: pointer; transition: all 0.2s ease;">
                            📲 Send via WhatsApp
                        </button>
                    @elseif($guest->invitation_sent_at)
                        <button type="button" id="sendInviteBtn" style="display: inline-flex; align-items: center; justify-content: center; gap: 0.4rem; padding: 0.55rem 0.95rem; background: linear-gradient(135deg, #25a329 0%, #1f8621 100%); color: #fff; border: none; border-radius: 0.7rem; font-size: 0.9rem; font-weight: 600; cursor: pointer; transition: all 0.2s ease;">
                            📲 Send Again
                        </button>
                    @endif

                    @if(Route::has('couple.guests.index'))
                        <a href="{{ route('couple.guests.index') }}" style="display: inline-flex; align-items: center; justify-content: center; padding: 0.55rem 0.95rem; background: #fff; border: 1px solid #efd7df; border-radius: 0.7rem; color: #d54c6d; text-decoration: none; font-weight: 600; transition: all 0.2s ease;">
                            ← Back to List
                        </a>
                    @endif
                </div>
            </section>

            <!-- Invite Card Preview -->
            <section>
                <div class="invite-card">
                    <div class="invite-card-top">
                        <div class="invite-card-icon">💒</div>
                        <h2 class="invite-card-title">You're Invited!</h2>
                        <p class="invite-card-subtitle">to the wedding of</p>
                    </div>

                    <div class="invite-card-qr" id="qrCodeContainer">
                        <canvas id="qrCode"></canvas>
                    </div>

                    <div class="invite-card-details">
                        <div class="invite-card-detail-row">
                            <span class="invite-card-detail-label">Guest:</span>
                            <span class="invite-card-detail-value">{{ $guest->name }}</span>
                        </div>
                        @php
                            $couple = auth()->user()?->couple;
                        @endphp
                        @if($couple)
                        <div class="invite-card-detail-row">
                            <span class="invite-card-detail-label">Event:</span>
                            <span class="invite-card-detail-value">
                                {{ $couple->partner_1_name ?? 'Partner 1' }} & {{ $couple->partner_2_name ?? 'Partner 2' }}'s Wedding
                            </span>
                        </div>
                        @if($couple->wedding_date)
                        <div class="invite-card-detail-row">
                            <span class="invite-card-detail-label">Date:</span>
                            <span class="invite-card-detail-value">{{ \Carbon\Carbon::parse($couple->wedding_date)->format('F j, Y') }}</span>
                        </div>
                        @endif
                        @if($couple->wedding_venue)
                        <div class="invite-card-detail-row">
                            <span class="invite-card-detail-label">Venue:</span>
                            <span class="invite-card-detail-value">{{ $couple->wedding_venue }}</span>
                        </div>
                        @endif
                        @endif
                        <div class="invite-card-detail-row">
                            <span class="invite-card-detail-label">Plus Ones:</span>
                            <span class="invite-card-detail-value">{{ $guest->plus_one_count ?? 0 }}</span>
                        </div>
                        @if($guest->dietary_preference)
                        <div class="invite-card-detail-row">
                            <span class="invite-card-detail-label">Diet:</span>
                            <span class="invite-card-detail-value">{{ ucfirst($guest->dietary_preference) }}</span>
                        </div>
                        @endif
                    </div>

                    <div class="invite-card-actions">
                        <button type="button" id="printCardBtn" class="invite-btn invite-btn-primary">
                            🖨️ Print Card
                        </button>
                        <button type="button" id="downloadQrBtn" class="invite-btn invite-btn-secondary">
                            ⬇️ Download QR
                        </button>
                    </div>
                </div>
            </section>
        </div>

        <!-- Notes -->
        <section style="background: #fef7fa; border: 1px solid #efd7df; border-radius: 0.85rem; padding: 1.2rem;">
            <h3 style="margin: 0 0 0.6rem; font-size: 0.95rem; color: #201419;">📋 How It Works</h3>
            <ul style="margin: 0; padding: 0 0 0 1.2rem; color: #715b64; font-size: 0.9rem; line-height: 1.6;">
                <li>The QR code contains the invitation link unique to this guest</li>
                <li>When scanned, guests can directly confirm their RSVP</li>
                <li>Share the card via WhatsApp by clicking the button above</li>
                <li>You can print the card for hand delivery or digital sharing</li>
            </ul>
        </section>
    </div>
@endsection

@push('page-scripts')
<script>
    // Include qrcode.js library
    const script = document.createElement('script');
    script.src = 'https://cdnjs.cloudflare.com/ajax/libs/qrcode.js/1.5.3/qrcode.min.js';
    document.head.appendChild(script);

    script.onload = function() {
        generateQRCode();
    };

    function generateQRCode() {
        // Generate invitation link - you may need to adjust this route
        const guestId = {{ $guest->id }};
        const inviteLink = `{{ url('/invite') }}/${guestId}`;

        const canvas = document.getElementById('qrCode');
        if (canvas) {
            new QRCode(canvas, {
                text: inviteLink,
                width: 240,
                height: 240,
                colorDark: '#d54c6d',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.H
            });
        }
    }

    // Send via WhatsApp
    document.getElementById('sendInviteBtn')?.addEventListener('click', function() {
        const guestName = "{{ $guest->name }}";
        const contactNumber = "{{ preg_replace('/[^0-9]/', '', $guest->contact_number) }}";
        const inviteLink = `{{ url('/invite') }}/{{ $guest->id }}`;
        const couple = "{{ auth()->user()?->couple }}";

        @php
            $coupleName = auth()->user()?->couple?->partner_1_name . ' & ' . auth()->user()?->couple?->partner_2_name;
        @endphp

        const message = `Hi {{ $guest->name }},

You're invited to {{ $coupleName }}'s wedding! 💒

Please scan the QR code or click the link below to confirm your RSVP:
${inviteLink}

Can't wait to celebrate with you!`;

        const encodedMessage = encodeURIComponent(message);
        const whatsappUrl = `https://wa.me/${contactNumber}?text=${encodedMessage}`;

        window.open(whatsappUrl, '_blank');
    });

    // Print Card
    document.getElementById('printCardBtn')?.addEventListener('click', function() {
        const printWindow = window.open('', '', 'width=800,height=600');
        const inviteCard = document.querySelector('.invite-card');
        printWindow.document.write(inviteCard.outerHTML);
        printWindow.document.close();
        setTimeout(() => printWindow.print(), 250);
    });

    // Download QR Code
    document.getElementById('downloadQrBtn')?.addEventListener('click', function() {
        const canvas = document.getElementById('qrCode');
        const link = document.createElement('a');
        link.href = canvas.toDataURL();
        link.download = 'invite-{{ $guest->id }}-qr-code.png';
        link.click();
    });
</script>
@endpush

