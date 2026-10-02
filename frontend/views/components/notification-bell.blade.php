@php
    $user = auth()->user();
    $unreadCount = $user ? $user->unreadNotifications()->count() : 0;
    $recentNotifications = $user ? $user->notifications()->latest()->take(6)->get() : collect();
@endphp

<div class="dropdown notification-dropdown" id="portalNotificationDropdown">
    <button class="btn btn-sm btn-light border shadow-sm rounded-circle position-relative d-flex align-items-center justify-content-center"
            type="button"
            data-bs-toggle="dropdown"
            aria-expanded="false"
            id="notificationBellBtn"
            title="सूचनाहरू (Notifications)"
            style="width: 38px; height: 38px; background: #ffffff;">
        <i data-lucide="bell" style="width: 18px; height: 18px; color: #1e293b;"></i>
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-white notification-badge {{ $unreadCount > 0 ? '' : 'd-none' }}"
              id="notificationCountBadge"
              style="font-size: 0.68rem; padding: 0.28em 0.55em;">
            {{ $unreadCount > 9 ? '9+' : $unreadCount }}
        </span>
    </button>

    <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3 p-0 overflow-hidden" 
         style="width: 340px; max-width: 90vw; z-index: 1060;" 
         aria-labelledby="notificationBellBtn">
        
        <!-- Header -->
        <div class="p-3 bg-light border-bottom d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <i data-lucide="bell" style="width: 16px; height: 16px;" class="text-primary"></i>
                <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.95rem;">सूचनाहरू</h6>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill extra-small" id="notificationHeaderCount">
                    {{ $unreadCount }} नयाँ
                </span>
            </div>
            @if($unreadCount > 0)
                <button type="button" 
                        class="btn btn-link text-decoration-none p-0 extra-small fw-semibold text-primary" 
                        id="markAllReadBtn"
                        onclick="markAllNotificationsAsRead(event)">
                    सबै पढ्नुहोस्
                </button>
            @endif
        </div>

        <!-- Notification List -->
        <div class="notification-items-wrap" id="notificationItemsList" style="max-height: 360px; overflow-y: auto;">
            @forelse($recentNotifications as $notification)
                @php
                    $nData = $notification->data;
                    $isUnread = is_null($notification->read_at);
                    $iconName = $nData['icon'] ?? 'bell';
                    $colorName = $nData['color'] ?? 'primary';
                    $readUrl = route('notifications.read', $notification->id);
                @endphp
                <a href="{{ $readUrl }}" 
                   class="d-flex align-items-start gap-2.5 p-3 border-bottom text-decoration-none text-dark notification-item {{ $isUnread ? 'bg-primary-subtle-light' : 'bg-white' }}"
                   style="transition: background-color 0.15s ease;"
                   onmouseover="this.style.backgroundColor='#f1f5f9'"
                   onmouseout="this.style.backgroundColor='{{ $isUnread ? '#f0f7ff' : '#ffffff' }}'">
                    
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 mt-0.5" 
                         style="width: 34px; height: 34px; background: rgba(5, 55, 117, 0.08);">
                        <i data-lucide="{{ $iconName }}" style="width: 16px; height: 16px;" class="text-{{ $colorName }}"></i>
                    </div>

                    <div class="flex-grow-1 overflow-hidden">
                        <div class="d-flex align-items-center justify-content-between mb-0.5">
                            <span class="fw-bold text-dark extra-small text-truncate" style="max-width: 200px;">
                                {{ $nData['title'] ?? 'सूचना' }}
                            </span>
                            @if($isUnread)
                                <span class="badge bg-primary rounded-circle p-1" style="width: 7px; height: 7px;"></span>
                            @endif
                        </div>
                        <p class="mb-1 text-secondary extra-small leading-tight text-truncate-2" style="font-size: 0.78rem;">
                            {{ $nData['message'] ?? '' }}
                        </p>
                        <span class="text-muted" style="font-size: 0.7rem;">
                            <i data-lucide="clock" style="width: 10px; height: 10px;" class="me-0.5"></i>
                            {{ $notification->created_at->diffForHumans() }}
                        </span>
                    </div>
                </a>
            @empty
                <div class="text-center py-4 px-3 text-muted" id="notificationEmptyState">
                    <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center mb-2" style="width: 44px; height: 44px;">
                        <i data-lucide="bell-off" style="width: 20px; height: 20px; color: #94a3b8;"></i>
                    </div>
                    <div class="small fw-semibold text-secondary">हाल कुनै पनि नयाँ सूचना छैन।</div>
                    <span class="extra-small text-muted">तपाईंका सबै कामहरूको प्रगति यहाँ देखिनेछ।</span>
                </div>
            @endforelse
        </div>

        <!-- Footer -->
        <div class="p-2 bg-light border-top text-center">
            <a href="{{ route('notifications.index') }}" class="btn btn-sm btn-link text-decoration-none fw-semibold text-primary extra-small">
                सबै सूचनाहरू हेर्नुहोस् <i data-lucide="arrow-right" style="width: 12px; height: 12px;" class="ms-0.5"></i>
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function markAllNotificationsAsRead(e) {
        if (e) e.preventDefault();
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        fetch("{{ route('notifications.mark-all-read') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const badge = document.getElementById('notificationCountBadge');
                if (badge) badge.classList.add('d-none');
                const headerCount = document.getElementById('notificationHeaderCount');
                if (headerCount) headerCount.textContent = '० नयाँ';
                const markBtn = document.getElementById('markAllReadBtn');
                if (markBtn) markBtn.classList.add('d-none');

                document.querySelectorAll('.notification-item').forEach(item => {
                    item.style.backgroundColor = '#ffffff';
                    item.classList.remove('bg-primary-subtle-light');
                    const dot = item.querySelector('.bg-primary.rounded-circle');
                    if (dot) dot.remove();
                });
            }
        })
        .catch(err => console.error('Error marking all as read:', err));
    }

    // Auto-refresh unread count periodically
    setInterval(function() {
        fetch("{{ route('notifications.unread-count') }}", {
            headers: { 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(d => {
            const badge = document.getElementById('notificationCountBadge');
            const headerCount = document.getElementById('notificationHeaderCount');
            if (badge) {
                if (d.unread_count > 0) {
                    badge.textContent = d.unread_count > 9 ? '9+' : d.unread_count;
                    badge.classList.remove('d-none');
                } else {
                    badge.classList.add('d-none');
                }
            }
            if (headerCount && d.unread_count !== undefined) {
                headerCount.textContent = d.unread_count + ' नयाँ';
            }
        })
        .catch(() => {});
    }, 25000);
</script>
@endpush
