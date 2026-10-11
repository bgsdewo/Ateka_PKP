@extends('layouts.app')

@section('title', 'Pusat Notifikasi - Inventori ATK')

@section('page-header')
    <div class="iq-navbar-header" style="height: 160px;">
        <div class="container-fluid iq-container">
            <div class="row">
                <div class="col-md-12">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <h2 class="text-white fw-bold mb-1">Pusat Notifikasi</h2>
                            <p class="text-white-50 mb-0 small">Riwayat seluruh pemberitahuan sistem, ketersediaan pengadaan, dan status permohonan ATK.</p>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <form action="{{ route('notifications.markAllAsRead') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-light fw-bold rounded-pill shadow-sm px-3">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                    Tandai Semua Dibaca
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="container-fluid content-inner mt-n4 py-0 pb-4">
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <h5 class="card-title mb-0 fw-bold">Riwayat Pemberitahuan</h5>
                            @if(($unreadCount ?? 0) > 0)
                                <span class="badge bg-danger rounded-pill px-2.5 py-1">{{ $unreadCount }} Belum Dibaca</span>
                            @else
                                <span class="badge bg-success-subtle text-success border rounded-pill px-2.5 py-1">Semua Terbaca</span>
                            @endif
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @if($notifications->isEmpty())
                            <div class="text-center py-5 px-3">
                                <div class="bg-light rounded-circle d-inline-flex p-3 mb-3 text-muted">
                                    <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                                        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                                    </svg>
                                </div>
                                <h5 class="fw-bold text-dark mb-1">Belum Ada Notifikasi</h5>
                                <p class="text-muted small mb-0">Pemberitahuan terkait usulan pengadaan, barang persediaan, atau distribusi akan muncul di sini.</p>
                            </div>
                        @else
                            <div class="list-group list-group-flush">
                                @foreach($notifications as $notif)
                                    <div class="list-group-item p-3.5 {{ !$notif['is_read'] ? 'bg-light border-start border-primary border-4' : '' }} transition-all">
                                        <div class="d-flex align-items-start justify-content-between gap-3">
                                            <div class="d-flex align-items-start gap-3 flex-grow-1">
                                                <div class="rounded-circle p-2.5 d-flex align-items-center justify-content-center flex-shrink-0 {{ $notif['type'] === 'procurement_available' ? 'bg-success text-white' : 'bg-primary-subtle text-primary' }}" style="width: 42px; height: 42px;">
                                                    @if($notif['type'] === 'procurement_available')
                                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                            <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                                                            <line x1="3" y1="6" x2="21" y2="6"></line>
                                                            <path d="M16 10a4 4 0 0 1-8 0"></path>
                                                        </svg>
                                                    @else
                                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                            <circle cx="12" cy="12" r="10"></circle>
                                                            <line x1="12" y1="16" x2="12" y2="12"></line>
                                                            <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                                        </svg>
                                                    @endif
                                                </div>
                                                <div>
                                                    <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                                        <h6 class="mb-0 fw-bold text-dark">{{ $notif['title'] }}</h6>
                                                        @if(!$notif['is_read'])
                                                            <span class="badge bg-danger rounded-pill" style="font-size: 0.65rem;">Baru</span>
                                                        @else
                                                            <span class="badge bg-light text-secondary border rounded-pill" style="font-size: 0.65rem;">Dibaca</span>
                                                        @endif
                                                    </div>
                                                    <p class="mb-1 text-secondary small" style="font-size: 0.88rem; line-height: 1.45;">{{ $notif['message'] }}</p>
                                                    <small class="text-muted" style="font-size: 0.75rem;">
                                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1">
                                                            <circle cx="12" cy="12" r="10"></circle>
                                                            <polyline points="12 6 12 12 16 14"></polyline>
                                                        </svg>
                                                        {{ $notif['created_at'] }} &bull; {{ $notif['time_ago'] }}
                                                    </small>
                                                </div>
                                            </div>

                                            <div class="d-flex align-items-center gap-2 flex-shrink-0">
                                                @if(!empty($notif['link']) && $notif['link'] !== '#')
                                                    <a href="{{ $notif['link'] }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1">
                                                        Buka &rarr;
                                                    </a>
                                                @endif
                                                @if(!$notif['is_read'])
                                                    <form action="{{ route('notifications.markAsRead', $notif['id']) }}" method="POST" class="mb-0">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 py-1" title="Tandai Sudah Dibaca">
                                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                <polyline points="20 6 9 17 4 12"></polyline>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
