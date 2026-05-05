@switch($icon)
    @case('home')
        <svg class="admin-nav-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 11.5L12 4l8 7.5V20a1 1 0 0 1-1 1h-4.5a.5.5 0 0 1-.5-.5V15a2 2 0 0 0-2-2h-1a2 2 0 0 0-2 2v5.5a.5.5 0 0 1-.5.5H5a1 1 0 0 1-1-1v-8.5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>
        @break
    @case('shield')
        <svg class="admin-nav-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 3l7 3v5c0 4.5-3 8.6-7 10-4-1.4-7-5.5-7-10V6l7-3Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M9.5 12.5l1.9 1.9 3.8-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
        @break
    @case('users')
        <svg class="admin-nav-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M17 20v-1.3A3.7 3.7 0 0 0 13.3 15H10.7A3.7 3.7 0 0 0 7 18.7V20" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M12 12.5a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" stroke="currentColor" stroke-width="1.8"/></svg>
        @break
    @case('settings')
        <svg class="admin-nav-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 15.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Z" stroke="currentColor" stroke-width="1.8"/><path d="M19 12a7.3 7.3 0 0 0-.08-.99l2.01-1.57-1.9-3.29-2.45.77a7.7 7.7 0 0 0-1.71-1l-.38-2.55H9.51l-.38 2.55a7.7 7.7 0 0 0-1.71 1l-2.45-.77-1.9 3.29 2.01 1.57A7.3 7.3 0 0 0 5 12c0 .33.03.66.08.99l-2.01 1.57 1.9 3.29 2.45-.77c.53.4 1.1.74 1.71 1l.38 2.55h4.98l.38-2.55c.61-.26 1.18-.6 1.71-1l2.45.77 1.9-3.29-2.01-1.57c.05-.33.08-.66.08-.99Z" stroke="currentColor" stroke-width="1.2" stroke-linejoin="round"/></svg>
        @break
    @default
        <svg class="admin-nav-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 4l8 6v10H4V10l8-6Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>
@endswitch