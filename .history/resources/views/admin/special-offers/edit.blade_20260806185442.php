{{-- resources/views/admin/special-offers/edit.blade.php --}}

@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Edit Special Offer</h4>
        <a href="{{ route('admin.special-offers.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.special-offers.update', $specialOffer->id) }}" 
                          method="POST" 
                          enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="badge_text">Badge Text <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           name="badge_text" 
                                           id="badge_text" 
                                           class="form-control @error('badge_text') is-invalid @enderror" 
                                           value="{{ old('badge_text', $specialOffer->badge_text) }}" 
                                           required>
                                    @error('badge_text')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="title">Title <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           name="title" 
                                           id="title" 
                                           class="form-control @error('title') is-invalid @enderror" 
                                           value="{{ old('title', $specialOffer->title) }}" 
                                           required>
                                    @error('title')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="description">Description</label>
                                    <textarea name="description" 
                                              id="description" 
                                              class="form-control @error('description') is-invalid @enderror" 
                                              rows="4">{{ old('description', $specialOffer->description) }}</textarea>
                                    @error('description')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="button_text">Button Text <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           name="button_text" 
                                           id="button_text" 
                                           class="form-control @error('button_text') is-invalid @enderror" 
                                           value="{{ old('button_text', $specialOffer->button_text) }}" 
                                           required>
                                    @error('button_text')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="button_link">Button Link</label>
                                    <input type="text" 
                                           name="button_link" 
                                           id="button_link" 
                                           class="form-control @error('button_link') is-invalid @enderror" 
                                           value="{{ old('button_link', $specialOffer->button_link) }}" 
                                           placeholder="e.g., /shop">
                                    @error('button_link')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr>

                        <h5 class="mb-3">USP Items (Bottom Features)</h5>
                        <p class="text-muted small mb-3">These appear as the 4 features at the bottom of the section.</p>

                        <div id="usp-container">
                            @php
                                $uspItems = $specialOffer->usp_items ?? [];
                                if (empty($uspItems)) {
                                    $uspItems = [
                                        ['icon' => 'shipping', 'title' => 'Free Shipping', 'description' => 'for all orders above ₹ 2000'],
                                        ['icon' => 'returns', 'title' => 'Free Returns', 'description' => 'within 10 days'],
                                        ['icon' => 'secure', 'title' => 'Secure shopping', 'description' => "you're in safe hands"],
                                        ['icon' => 'styles', 'title' => 'Over 10,000 Styles', 'description' => 'to match your unique style']
                                    ];
                                }
                            @endphp

                            @foreach($uspItems as $index => $item)
                                <div class="usp-item card mb-2 p-3">
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="small">Icon <span class="text-muted">(shipping, returns, secure, styles, default)</span></label>
                                                <input type="text" 
                                                       name="usp_items[{{ $index }}][icon]" 
                                                       class="form-control" 
                                                       value="{{ $item['icon'] ?? 'default' }}"
                                                       placeholder="Icon name">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="small">Title</label>
                                                <input type="text" 
                                                       name="usp_items[{{ $index }}][title]" 
                                                       class="form-control" 
                                                       value="{{ $item['title'] ?? '' }}"
                                                       placeholder="e.g., Free Shipping">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="small">Description</label>
                                                <input type="text" 
                                                       name="usp_items[{{ $index }}][description]" 
                                                       class="form-control" 
                                                       value="{{ $item['description'] ?? '' }}"
                                                       placeholder="e.g., for all orders above ₹ 2000">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Special Offer
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection