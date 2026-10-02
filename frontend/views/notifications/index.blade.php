@extends(auth()->user()->isAdmin() ? 'layouts.admin' : 'layouts.citizen', ['pageTitle' => 'सूचना केन्द्र'])

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">
            <i data-lucide="bell" class="me-2 text-primary"></i>सूचना केन्द्र (Notifications)
        </h4>
        <span class="text-muted small">तपाईंका निवेदन, भुक्तानी, कागजात र टिप्पणी सम्बन्धी आधिकारिक अलर्टहरू</span>
    </div>
    @if($unreadCount > 0)
        <div>
            <form action="{{ route('notifications.mark-all-read') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-primary fw-semibold">
                    <i data-lucide="check-check" class="me-1"></i> सबै पढेको चिन्ह लगाउनुहोस्
                </button>
            </form>
        </div>
    @endif
</div>

<!-- Filter Tabs -->
<div class="card border-0 shadow-sm mb-4 bg-white rounded-3">
    <div class="card-body p-2 d-flex gap-2">
        <a href="{{ route('notifications.index', ['filter' => 'all']) }}" 
           class="btn btn-sm {{ $filter === 'all' ? 'btn-primary' : 'btn-light text-secondary' }} fw-semibold rounded-pill px-3">
            सबै सूचनाहरू
        </a>
        <a href="{{ route('notifications.index', ['filter' => 'unread']) }}" 
           class="btn btn-sm {{ $filter === 'unread' ? 'btn-primary' : 'btn-light text-secondary' }} fw-semibold rounded-pill px-3 position-relative">
            नपढेका 
            @if($unreadCount > 0)
                <span class="badge bg-danger text-white rounded-pill ms-1">{{ $unreadCount }}</span>
            @endif
        </a>
        <a href="{{ route('notifications.index', ['filter' => 'read']) }}" 
           class="btn btn-sm {{ $filter === 'read' ? 'btn-primary' : 'btn-light text-secondary' }} fw-semibold rounded-pill px-3">
            पढिएका
        </a>
    </div>
</div>

<!-- Notification Items List -->
<div class="row g-3 mb-4">
    @forelse($notifications as $notification)
        @php
            $data = $notification->data;
            $isUnread = is_null($notification->read_at);
            $icon = $data['icon'] ?? 'bell';
            $color = $data['color'] ?? 'primary';
            $targetUrl = route('notifications.read', $notification->id);
        @endphp
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-3 overflow-hidden {{ $isUnread ? 'border-start border-4 border-primary' : '' }}"
                 style="background: {{ $isUnread ? '#f8faff' : '#ffffff' }}; transition: transform 0.15s ease;">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex align-items-start gap-3">
                        
                        <!-- Icon Circle -->
                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width: 44px; height: 44px; background: rgba(5, 55, 117, 0.08);">
                            <i data-lucide="{{ $icon }}" style="width: 22px; height: 22px;" class="text-{{ $color }}"></i>
                        </div>

                        <!-- Content Block -->
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-1">
                                <div class="d-flex align-items-center gap-2">
                                    <h6 class="fw-bold mb-0 text-dark">{{ $data['title'] ?? 'सूचना' }}</h6>
                                    @if($isUnread)
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle extra-small fw-bold">नयाँ</span>
                                    @endif
                                </div>
                                <span class="text-muted extra-small">
                                    <i data-lucide="clock" style="width: 12px; height: 12px;" class="me-1"></i>
                                    {{ $notification->created_at->format('M d, Y h:i A') }} ({{ $notification->created_at->diffForHumans() }})
                                </span>
                            </div>

                            <p class="text-secondary small mb-3 mb-md-2" style="line-height: 1.5;">
                                {{ $data['message'] ?? '' }}
                            </p>

                            <!-- Actions -->
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 pt-2 border-top">
                                <div class="d-flex gap-2">
                                    @if(!empty($data['link']) && $data['link'] !== '#')
                                        <a href="{{ $targetUrl }}" class="btn btn-sm btn-primary fw-semibold">
                                            <i data-lucide="external-link" class="me-1" style="width: 13px; height: 13px;"></i> विवरण हेर्नुहोस्
                                        </a>
                                    @endif
                                    @if($isUnread)
                                        <form action="{{ route('notifications.read', $notification->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-secondary">
                                                <i data-lucide="check" class="me-1" style="width: 13px; height: 13px;"></i> पढेको चिन्ह लगाउनुहोस्
                                            </button>
                                        </form>
                                    @endif
                                </div>

                                <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST" 
                                      onsubmit="return confirm('के तपाईं यो सूचना हटाउन चाहनुहुन्छ?');" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-link text-danger text-decoration-none p-0 extra-small">
                                        <i data-lucide="trash-2" style="width: 13px; height: 13px;" class="me-0.5"></i> सूचना हटाउनुहोस्
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 text-center py-5 bg-white">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-light mb-3 mx-auto" style="width: 64px; height: 64px;">
                    <i data-lucide="bell-off" style="width: 32px; height: 32px; color: #94a3b8;"></i>
                </div>
                <h6 class="fw-bold text-dark mb-1">कुनै पनि सूचना फेला परेन</h6>
                <p class="text-muted small mb-0">यस फिल्टर अन्तर्गत हाल कुनै पनि सूचना उपलब्ध छैन।</p>
            </div>
        </div>
    @endforelse
</div>

<!-- Pagination -->
@if($notifications->hasPages())
    <div class="d-flex justify-content-center">
        {{ $notifications->links() }}
    </div>
@endif
@endsection
