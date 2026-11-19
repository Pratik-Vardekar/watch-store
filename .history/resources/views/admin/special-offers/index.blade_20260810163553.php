```blade
{{-- resources/views/admin/special-offers/index.blade.php --}}

@extends('layouts.admin')

@section('content')

<style>
    .special-offers-page {
        padding: 10px 0 30px;
    }

    /* Page Header */
    .page-header {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fc 100%);
        border: 1px solid #e9ecef;
        border-radius: 16px;
        padding: 24px 26px;
        margin-bottom: 24px;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.04);
    }

    .page-header-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
    }

    .page-title-wrapper {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .page-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #6f42c1, #8e5de7);
        color: #fff;
        font-size: 22px;
        box-shadow: 0 8px 18px rgba(111, 66, 193, 0.22);
    }

    .page-title {
        margin: 0;
        font-size: 24px;
        font-weight: 700;
        color: #212529;
    }

    .page-subtitle {
        margin: 4px 0 0;
        color: #6c757d;
        font-size: 14px;
    }

    .add-offer-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 11px 18px;
        border-radius: 10px;
        font-weight: 600;
        text-decoration: none;
        color: #fff;
        background: linear-gradient(135deg, #6f42c1, #8e5de7);
        border: none;
        transition: all 0.25s ease;
        box-shadow: 0 6px 15px rgba(111, 66, 193, 0.20);
    }

    .add-offer-btn:hover {
        color: #fff;
        transform: translateY(-2px);
        box-shadow: 0 10px 22px rgba(111, 66, 193, 0.28);
    }

    /* Alert */
    .custom-alert {
        border: 0;
        border-radius: 12px;
        padding: 14px 18px;
        box-shadow: 0 4px 14px rgba(25, 135, 84, 0.08);
        margin-bottom: 22px;
    }

    /* Stats */
    .stats-row {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 18px;
        margin-bottom: 24px;
    }

    .stat-card {
        background: #fff;
        border: 1px solid #e9ecef;
        border-radius: 15px;
        padding: 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.035);
        transition: all 0.25s ease;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 22px rgba(0, 0, 0, 0.07);
    }

    .stat-label {
        color: #6c757d;
        font-size: 13px;
        font-weight: 500;
        margin-bottom: 5px;
    }

    .stat-value {
        font-size: 25px;
        font-weight: 700;
        color: #212529;
        line-height: 1.2;
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 19px;
    }

    .stat-icon.total {
        background: #eee8ff;
        color: #6f42c1;
    }

    .stat-icon.active {
        background: #e5f8ed;
        color: #198754;
    }

    .stat-icon.inactive {
        background: #f1f3f5;
        color: #6c757d;
    }

    /* Main Card */
    .offers-card {
        background: #fff;
        border: 1px solid #e9ecef;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.045);
    }

    .offers-card-header {
        padding: 20px 22px;
        border-bottom: 1px solid #edf0f2;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
    }

    .offers-card-title {
        margin: 0;
        font-size: 17px;
        font-weight: 700;
        color: #212529;
    }

    .offers-count {
        font-size: 12px;
        font-weight: 600;
        padding: 5px 10px;
        border-radius: 20px;
        background: #f3efff;
        color: #6f42c1;
    }

    .table-wrapper {
        overflow-x: auto;
    }

    .offers-table {
        margin: 0;
        min-width: 900px;
    }

    .offers-table thead th {
        background: #f8f9fb;
        color: #6c757d;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        padding: 15px 16px;
        border-bottom: 1px solid #e9ecef;
        white-space: nowrap;
    }

    .offers-table tbody td {
        padding: 16px;
        vertical-align: middle;
        border-bottom: 1px solid #f0f1f3;
        color: #343a40;
        font-size: 14px;
    }

    .offers-table tbody tr {
        transition: background 0.2s ease;
    }

    .offers-table tbody tr:hover {
        background: #fbfaff;
    }

    .offers-table tbody tr:last-child td {
        border-bottom: 0;
    }

    /* Number */
    .row-number {
        width: 32px;
        height: 32px;
        border-radius: 9px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #f3f4f6;
        color: #6c757d;
        font-size: 12px;
        font-weight: 700;
    }

    /* Title */
    .offer-title {
        font-weight: 650;
        color: #212529;
        max-width: 220px;
    }

    .offer-id {
        font-size: 11px;
        color: #adb5bd;
        margin-top: 3px;
    }

    /* Badge */
    .offer-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 10px;
        border-radius: 20px;
        background: #fff0f0;
        color: #dc3545;
        font-size: 11px;
        font-weight: 700;
    }

    /* Button Text */
    .button-preview {
        display: inline-flex;
        align-items: center;
        padding: 6px 11px;
        border-radius: 7px;
        background: #f5f6f8;
        color: #495057;
        font-size: 12px;
        font-weight: 600;
    }

    /* Image */
    .offer-image-wrapper {
        position: relative;
        width: 90px;
        height: 56px;
        border-radius: 9px;
        overflow: hidden;
        background: #f1f3f5;
        border: 1px solid #e9ecef;
    }

    .offer-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        transition: transform 0.3s ease;
    }

    .offer-image-wrapper:hover .offer-image {
        transform: scale(1.08);
    }

    .no-image {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #adb5bd;
        font-size: 12px;
    }

    /* Status */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 11px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
    }

    .status-badge::before {
        content: '';
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: currentColor;
    }

    .status-active {
        background: #e8f8ef;
        color: #198754;
    }

    .status-inactive {
        background: #f1f3f5;
        color: #6c757d;
    }

    /* Actions */
    .action-buttons {
        display: flex;
        align-items: center;
        gap: 7px;
    }

    .action-btn {
        width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        border: 1px solid transparent;
        transition: all 0.2s ease;
    }

    .edit-btn {
        color: #0d6efd;
        background: #edf5ff;
        border-color: #dcecff;
    }

    .edit-btn:hover {
        color: #fff;
        background: #0d6efd;
        border-color: #0d6efd;
        transform: translateY(-1px);
    }

    .activate-btn {
        color: #198754;
        background: #eaf8f0;
        border-color: #d7f1e2;
    }

    .activate-btn:hover {
        color: #fff;
        background: #198754;
        border-color: #198754;
        transform: translateY(-1px);
    }

    .deactivate-btn {
        color: #fd7e14;
        background: #fff4e9;
        border-color: #ffe5cc;
    }

    .deactivate-btn:hover {
        color: #fff;
        background: #fd7e14;
        border-color: #fd7e14;
        transform: translateY(-1px);
    }

    /* Empty State */
    .empty-state {
        padding: 65px 20px !important;
    }

    .empty-icon {
        width: 75px;
        height: 75px;
        margin: 0 auto 18px;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f5f2ff;
        color: #6f42c1;
        font-size: 28px;
    }

    .empty-title {
        font-size: 17px;
        font-weight: 700;
        color: #343a40;
        margin-bottom: 7px;
    }

    .empty-text {
        color: #8a9299;
        font-size: 13px;
        margin-bottom: 20px;
    }

    .empty-add-btn {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 9px 15px;
        border-radius: 8px;
        background: #6f42c1;
        color: #fff;
        text-decoration: none;
        font-size: 13px;
        font-weight: 600;
        transition: 0.2s ease;
    }

    .empty-add-btn:hover {
        background: #59339d;
        color: #fff;
    }

    /* Responsive */
    @media (max-width: 991px) {
        .stats-row {
            grid-template-columns: repeat(3, 1fr);
        }

        .page-header-content {
            align-items: flex-start;
        }
    }

    @media (max-width: 767px) {
        .special-offers-page {
            padding: 5px 0 20px;
        }

        .page-header {
            padding: 18px;
        }

        .page-header-content {
            flex-direction: column;
        }

        .add-offer-btn {
            width: 100%;
            justify-content: center;
        }

        .stats-row {
            grid-template-columns: 1fr;
            gap: 12px;
        }

        .stat-card {
            padding: 16px;
        }

        .offers-card-header {
            padding: 16px;
        }

        .page-title {
            font-size: 20px;
        }
    }
</style>

<div class="special-offers-page">

    {{-- Success Message --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show custom-alert" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-check-circle"></i>
                <span>{{ session('success') }}</span>
            </div>

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Close">
            </button>
        </div>
    @endif


    {{-- Page Header --}}
    <div class="page-header">
        <div class="page-header-content">

            <div class="page-title-wrapper">
                <div class="page-icon">
                    <i class="fas fa-tags"></i>
                </div>

                <div>
                    <h1 class="page-title">Special Offers</h1>
                    <p class="page-subtitle">
                        Manage promotional banners, offers and campaign content.
                    </p>
                </div>
            </div>

            {{-- Add Offer Button --}}
            <a href="{{ route('admin.special-offers.create') }}"
               class="add-offer-btn">
                <i class="fas fa-plus"></i>
                <span>Add Offer</span>
            </a>

        </div>
    </div>


    {{-- Statistics --}}
    @php
        $totalOffers = $offers->count();
        $activeOffers = $offers->where('is_active', 1)->count();
        $inactiveOffers = $offers->where('is_active', 0)->count();
    @endphp

    <div class="stats-row">

        {{-- Total --}}
        <div class="stat-card">
            <div>
                <div class="stat-label">Total Offers</div>
                <div class="stat-value">{{ $totalOffers }}</div>
            </div>

            <div class="stat-icon total">
                <i class="fas fa-tags"></i>
            </div>
        </div>

        {{-- Active --}}
        <div class="stat-card">
            <div>
                <div class="stat-label">Active Offers</div>
                <div class="stat-value">{{ $activeOffers }}</div>
            </div>

            <div class="stat-icon active">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>

        {{-- Inactive --}}
        <div class="stat-card">
            <div>
                <div class="stat-label">Inactive Offers</div>
                <div class="stat-value">{{ $inactiveOffers }}</div>
            </div>

            <div class="stat-icon inactive">
                <i class="fas fa-pause-circle"></i>
            </div>
        </div>

    </div>


    {{-- Offers Table Card --}}
    <div class="offers-card">

        {{-- Card Header --}}
        <div class="offers-card-header">

            <div>
                <h5 class="offers-card-title">
                    <i class="fas fa-list me-2 text-muted"></i>
                    All Special Offers
                </h5>
            </div>

            <span class="offers-count">
                {{ $totalOffers }} {{ $totalOffers == 1 ? 'Offer' : 'Offers' }}
            </span>

        </div>


        {{-- Table --}}
        <div class="table-wrapper">

            <table class="table offers-table align-middle">

                <thead>
                    <tr>
                        <th style="width: 70px;">#</th>
                        <th>Offer</th>
                        <th>Badge</th>
                        <th>Button</th>
                        <th>Image</th>
                        <th>Status</th>
                        <th style="width: 110px;">Actions</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($offers as $offer)

                        <tr>

                            {{-- Number --}}
                            <td>
                                <span class="row-number">
                                    {{ $loop->iteration }}
                                </span>
                            </td>


                            {{-- Title --}}
                            <td>
                                <div class="offer-title">
                                    {{ $offer->title }}
                                </div>

                                <div class="offer-id">
                                    Offer #{{ $offer->id }}
                                </div>
                            </td>


                            {{-- Badge --}}
                            <td>
                                @if($offer->badge_text)

                                    <span class="offer-badge">
                                        <i class="fas fa-star"></i>
                                        {{ $offer->badge_text }}
                                    </span>

                                @else

                                    <span class="text-muted">—</span>

                                @endif
                            </td>


                            {{-- Button Text --}}
                            <td>
                                @if($offer->button_text)

                                    <span class="button-preview">
                                        <i class="fas fa-mouse-pointer me-1"></i>
                                        {{ $offer->button_text }}
                                    </span>

                                @else

                                    <span class="text-muted">—</span>

                                @endif
                            </td>


                            {{-- Image --}}
                            <td>

                                @if($offer->background_image)

                                    <div class="offer-image-wrapper">

                                        <img src="{{ $offer->image_url }}"
                                             alt="{{ $offer->title }}"
                                             class="offer-image"
                                             loading="lazy">

                                    </div>

                                @else

                                    <span class="no-image">
                                        <i class="fas fa-image"></i>
                                        No image
                                    </span>

                                @endif

                            </td>


                            {{-- Status --}}
                            <td>

                                @if($offer->is_active)

                                    <span class="status-badge status-active">
                                        Active
                                    </span>

                                @else

                                    <span class="status-badge status-inactive">
                                        Inactive
                                    </span>

                                @endif

                            </td>


                            {{-- Actions --}}
                            <td>

                                <div class="action-buttons">

                                    {{-- Edit --}}
                                    <a href="{{ route('admin.special-offers.edit', $offer->id) }}"
                                       class="action-btn edit-btn"
                                       title="Edit Offer"
                                       data-bs-toggle="tooltip">

                                        <i class="fas fa-edit"></i>

                                    </a>


                                    {{-- Toggle Status --}}
                                    <form action="{{ route('admin.special-offers.toggle-status', $offer->id) }}"
                                          method="POST"
                                          class="d-inline">

                                        @csrf
                                        @method('PUT')

                                        <button type="submit"
                                                class="action-btn {{ $offer->is_active ? 'deactivate-btn' : 'activate-btn' }}"
                                                title="{{ $offer->is_active ? 'Deactivate Offer' : 'Activate Offer' }}"
                                                data-bs-toggle="tooltip">

                                            <i class="fas fa-{{ $offer->is_active ? 'pause' : 'play' }}"></i>

                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>

                    @empty

                        {{-- Empty State --}}
                        <tr>

                            <td colspan="7" class="text-center empty-state">

                                <div class="empty-icon">
                                    <i class="fas fa-tags"></i>
                                </div>

                                <div class="empty-title">
                                    No Special Offers Found
                                </div>

                                <p class="empty-text">
                                    You haven't created any special offers yet.
                                    Create your first offer to get started.
                                </p>

                                <a href="{{ route('admin.special-offers.create') }}"
                                   class="empty-add-btn">

                                    <i class="fas fa-plus"></i>
                                    Create First Offer

                                </a>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>


{{-- Bootstrap Tooltip --}}
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {

        const tooltipTriggerList = document.querySelectorAll(
            '[data-bs-toggle="tooltip"]'
        );

        tooltipTriggerList.forEach(function (tooltipTriggerEl) {
            new bootstrap.Tooltip(tooltipTriggerEl);
        });

    });
</script>
@endpush

@endsection
```
