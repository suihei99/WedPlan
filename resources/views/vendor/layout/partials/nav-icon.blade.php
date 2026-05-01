@if($icon === 'home')
    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true" class="vendor-nav-icon">
        <path d="M4 11L12 4L20 11" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M6.5 9.5V20H17.5V9.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
@elseif($icon === 'store')
    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true" class="vendor-nav-icon">
        <path d="M4 10H20L18.5 6H5.5L4 10Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
        <path d="M5 10V19H19V10" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
        <path d="M10 19V14H14V19" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
    </svg>
@elseif($icon === 'checklist')
    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true" class="vendor-nav-icon">
        <path d="M10 6H20" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        <path d="M10 12H20" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        <path d="M10 18H20" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        <path d="M4 6.2L5.2 7.4L7.4 5.2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M4 12.2L5.2 13.4L7.4 11.2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M4 18.2L5.2 19.4L7.4 17.2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
@elseif($icon === 'bell')
    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true" class="vendor-nav-icon">
        <path d="M15 17H9C7.343 17 6 15.657 6 14V10C6 6.686 8.686 4 12 4C15.314 4 18 6.686 18 10V14C18 15.657 16.657 17 15 17Z" stroke="currentColor" stroke-width="1.8"/>
        <path d="M4 17H20" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        <path d="M10 20C10.355 20.622 11.078 21 12 21C12.922 21 13.645 20.622 14 20" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
    </svg>
@else
    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true" class="vendor-nav-icon">
        <circle cx="12" cy="12" r="3.2" stroke="currentColor" stroke-width="1.8"/>
        <path d="M12 3V5.2M12 18.8V21M3 12H5.2M18.8 12H21M5.6 5.6L7.1 7.1M16.9 16.9L18.4 18.4M18.4 5.6L16.9 7.1M7.1 16.9L5.6 18.4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
    </svg>
@endif
