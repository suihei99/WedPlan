@if($icon === 'home')
    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true" class="sidebar-nav-icon">
        <path d="M4 11L12 4L20 11" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M6.5 9.5V20H17.5V9.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
@elseif($icon === 'wallet')
    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true" class="sidebar-nav-icon">
        <rect x="3" y="6" width="18" height="13" rx="2.5" stroke="currentColor" stroke-width="1.8"/>
        <path d="M15 12H21" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        <circle cx="16.5" cy="12" r="1" fill="currentColor"/>
    </svg>
@elseif($icon === 'store')
    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true" class="sidebar-nav-icon">
        <path d="M4 10H20L18.5 6H5.5L4 10Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
        <path d="M5 10V19H19V10" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
        <path d="M10 19V14H14V19" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
    </svg>
@elseif($icon === 'users')
    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true" class="sidebar-nav-icon">
        <circle cx="9" cy="9" r="3" stroke="currentColor" stroke-width="1.8"/>
        <path d="M3.5 19C4.3 15.8 6.2 14 9 14C11.8 14 13.7 15.8 14.5 19" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        <circle cx="16.5" cy="8.5" r="2.5" stroke="currentColor" stroke-width="1.8"/>
        <path d="M14.5 13.8C16.9 14.3 18.5 16 19.2 18.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
    </svg>
@elseif($icon === 'checklist')
    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true" class="sidebar-nav-icon">
        <path d="M10 6H20" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        <path d="M10 12H20" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        <path d="M10 18H20" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        <path d="M4 6.2L5.2 7.4L7.4 5.2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M4 12.2L5.2 13.4L7.4 11.2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M4 18.2L5.2 19.4L7.4 17.2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
@elseif($icon === 'sparkles')
    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true" class="sidebar-nav-icon">
        <path d="M12 3L13.8 8.2L19 10L13.8 11.8L12 17L10.2 11.8L5 10L10.2 8.2L12 3Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
        <path d="M19.5 3.5L20.1 5.1L21.7 5.7L20.1 6.3L19.5 7.9L18.9 6.3L17.3 5.7L18.9 5.1L19.5 3.5Z" fill="currentColor"/>
    </svg>
@else
    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true" class="sidebar-nav-icon">
        <circle cx="12" cy="12" r="3.2" stroke="currentColor" stroke-width="1.8"/>
        <path d="M12 3V5.2M12 18.8V21M3 12H5.2M18.8 12H21M5.6 5.6L7.1 7.1M16.9 16.9L18.4 18.4M18.4 5.6L16.9 7.1M7.1 16.9L5.6 18.4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
    </svg>
@endif
