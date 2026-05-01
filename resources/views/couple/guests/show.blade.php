@extends('couple.layout.layout-couple')

@section('title', 'Guest Detail - WebPlan')
@section('page-title', 'Guest Detail')
@section('page-subtitle', 'Update RSVP information, contact details, and remove guests when needed.')

@push('page-styles')
    @vite(['resources/css/couple/guests.css'])
@endpush

@section('content')
    @php
        $status = strtolower($guest->rsvp_status ?? \App\Models\Guest::RSVP_PENDING);
        $statusClass = $status === \App\Models\Guest::RSVP_CONFIRMED ? 'is-confirmed' : ($status === \App\Models\Guest::RSVP_DECLINED ? 'is-declined' : 'is-pending');
        $rawPhone = preg_replace('/\D+/', '', (string) ($guest->phone ?? ''));
        if (str_starts_with($rawPhone, '0')) {
            $phoneForWhatsapp = '60' . substr($rawPhone, 1);
        } else {
            $phoneForWhatsapp = $rawPhone;
        }

        $coupleNames = ($couple->partner_1_name ?? 'Partner 1') . ' & ' . ($couple->partner_2_name ?? 'Partner 2');
        $whatsAppMessage = rawurlencode("Hi {$guest->name},\n\nYou are invited to {$coupleNames}'s wedding.\nInvite code: " . ($guest->invite_code ?? 'N/A') . "\n\nPlease reply with your RSVP. Thank you.");
        $whatsAppUrl = $phoneForWhatsapp !== '' ? "https://wa.me/{$phoneForWhatsapp}?text={$whatsAppMessage}" : null;
    @endphp

    <div class="guests-page guests-page-form">
        @if(session('success'))
            <section class="guests-flash guests-flash-success" role="status">
                <strong>Success</strong>
                <span>{{ session('success') }}</span>
            </section>
        @endif

        @if($errors->any())
            <section class="guests-flash guests-flash-error" role="alert">
                <strong>Please review the form</strong>
                <span>{{ $errors->first() }}</span>
            </section>
        @endif

        <section class="guests-hero">
            <div>
                <span class="guests-kicker">Guest Management</span>
                <h1 class="guests-title">{{ $guest->name }}</h1>
                <p class="guests-subtitle">Edit this guest record, manage RSVP state, and keep your list accurate for wedding day planning.</p>
            </div>

            <div class="guests-hero-stats">
                <article>
                    <span>Status</span>
                    <strong>{{ ucfirst($status) }}</strong>
                </article>
                <article>
                    <span>Pax</span>
                    <strong>{{ (int) ($guest->pax_count ?? 1) }}</strong>
                </article>
                <article>
                    <span>Invite Code</span>
                    <strong>{{ $guest->invite_code ?? 'N/A' }}</strong>
                </article>
                <article>
                    <span>Phone</span>
                    <strong>{{ $guest->phone ?? 'N/A' }}</strong>
                </article>
            </div>
        </section>

        <section class="guests-layout-split">
            <article class="guests-form-card">
                <h2>Update Guest</h2>

                <form method="POST" action="{{ route('couple.guests.update', $guest) }}" class="guests-form" novalidate>
                    @csrf
                    @method('PUT')

                    <div class="guests-form-group">
                        <label for="name" class="guests-form-label">Guest Name *</label>
                        <input id="name" type="text" name="name" class="guests-form-input @error('name') is-invalid @enderror" value="{{ old('name', $guest->name) }}" required>
                        @error('name')
                            <small class="field-error">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="guests-form-group">
                        <label for="phone" class="guests-form-label">Malaysia Mobile Number</label>
                        <input id="phone" type="tel" name="phone" class="guests-form-input @error('phone') is-invalid @enderror" value="{{ old('phone', $guest->phone) }}" inputmode="tel" placeholder="e.g., +60123456789">
                        @error('phone')
                            <small class="field-error">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="guests-form-group">
                        <label for="pax_count" class="guests-form-label">Pax Count</label>
                        <input id="pax_count" type="number" name="pax_count" class="guests-form-input @error('pax_count') is-invalid @enderror" value="{{ old('pax_count', $guest->pax_count ?? 1) }}" min="1">
                        @error('pax_count')
                            <small class="field-error">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="guests-form-group">
                        <label for="rsvp_status" class="guests-form-label">RSVP Status</label>
                        <select id="rsvp_status" name="rsvp_status" class="guests-form-select @error('rsvp_status') is-invalid @enderror">
                            @foreach(\App\Models\Guest::RSVP_STATUS as $rsvp)
                                <option value="{{ $rsvp }}" @selected(old('rsvp_status', $guest->rsvp_status) === $rsvp)>{{ ucfirst($rsvp) }}</option>
                            @endforeach
                        </select>
                        @error('rsvp_status')
                            <small class="field-error">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="guests-form-actions">
                        <button type="submit" class="guests-form-submit">Update Guest</button>

                        @if(Route::has('couple.guests.index'))
                            <a href="{{ route('couple.guests.index') }}" class="guests-secondary-btn">Back to List</a>
                        @endif
                    </div>
                </form>
            </article>

            <aside class="guests-detail-card">
                <h3>Guest Actions</h3>
                <div class="guests-meta-list">
                    <div class="guests-meta-item">
                        <span>Current Status</span>
                        <strong><span class="guests-status-pill {{ $statusClass }}">{{ ucfirst($status) }}</span></strong>
                    </div>
                    <div class="guests-meta-item">
                        <span>Invite Code</span>
                        <strong>{{ $guest->invite_code ?? 'Not generated' }}</strong>
                    </div>
                    <div class="guests-meta-item">
                        <span>QR String</span>
                        <strong>{{ $guest->qr_code_string ?? 'Not available' }}</strong>
                    </div>
                </div>

                <div class="guests-side-actions">
                    @if($whatsAppUrl)
                        <a href="{{ $whatsAppUrl }}" target="_blank" rel="noopener" class="guests-whatsapp-btn guests-full-btn">Send via WhatsApp</a>
                    @endif

                    @if($status !== \App\Models\Guest::RSVP_CONFIRMED && Route::has('couple.guests.checkin'))
                        <form method="POST" action="{{ route('couple.guests.checkin', $guest) }}">
                            @csrf
                            <button type="submit" class="guests-secondary-btn guests-full-btn">Mark Confirmed</button>
                        </form>
                    @endif

                    @if(Route::has('couple.guests.destroy'))
                        <form method="POST" action="{{ route('couple.guests.destroy', $guest) }}" onsubmit="return confirm('Delete this guest?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="guests-danger-btn guests-full-btn">Delete Guest</button>
                        </form>
                    @endif
                </div>
            </aside>
        </section>
    </div>
@endsection

