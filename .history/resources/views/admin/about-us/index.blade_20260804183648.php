@extends('layouts.admin')

@section('content')
@php
    $stats = $about->stats ?: [['value' => '', 'label' => ''], ['value' => '', 'label' => ''], ['value' => '', 'label' => ''], ['value' => '', 'label' => '']];
    $stats = array_pad($stats, 4, ['value' => '', 'label' => '']);

    $features = $about->features ?: [];
    $features = array_pad($features, 4, '');

    // Remove description and image for team
    $team = $about->team ?: [];
    $team = array_pad($team, 3, ['name' => '', 'role' => '']);

    // Remove description (assuming certificates should still have label, image, pdf, but if image removed, leave only label and pdf)
    $certs = $about->certificates ?: [];
    $certs = array_pad($certs, 2, ['label' => '', 'pdf' => '']);
@endphp

<div class="container-fluid my-2">

    <div class="mb-4">
        <h4 class="mb-1">About Page Content</h4>
        <p class="text-muted mb-0">Manage every section shown on the front-end About page</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('about-us.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- HERO SECTION --}}
        <div class="card mb-4">
            <div class="card-header"><h5 class="mb-0">Hero Section</h5></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Title Word 1 (yellow)</label>
                        <input type="text" name="tag_1" class="form-control" value="{{ old('tag_1', $about->tag_1) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Title Word 2 (green)</label>
                        <input type="text" name="tag_2" class="form-control" value="{{ old('tag_2', $about->tag_2) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Title Word 3 (red)</label>
                        <input type="text" name="tag_3" class="form-control" value="{{ old('tag_3', $about->tag_3) }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Intro Paragraphs <small class="text-muted">(leave a blank line between paragraphs)</small></label>
                        <textarea name="content" rows="8" class="form-control">{{ old('content', $about->content) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- STATS SECTION --}}
        <div class="card mb-4">
            <div class="card-header"><h5 class="mb-0">Stats Section</h5></div>
            <div class="card-body">
                <div class="row">
                    @foreach($stats as $i => $stat)
                        <div class="col-md-6 mb-3">
                            <div class="row g-2">
                                <div class="col-4">
                                    <label class="form-label small">Value {{ $i + 1 }}</label>
                                    <input type="text" name="stats[{{ $i }}][value]" class="form-control" value="{{ $stat['value'] ?? '' }}" placeholder="e.g. 5000">
                                </div>
                                <div class="col-8">
                                    <label class="form-label small">Label {{ $i + 1 }}</label>
                                    <input type="text" name="stats[{{ $i }}][label]" class="form-control" value="{{ $stat['label'] ?? '' }}" placeholder="e.g. Happy Little Customers">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- WHO WE ARE SECTION --}}
        <div class="card mb-4">
            <div class="card-header"><h5 class="mb-0">Who We Are Section</h5></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Subtitle</label>
                        <input type="text" name="whoweare_subtitle" class="form-control" value="{{ old('whoweare_subtitle', $about->whoweare_subtitle) }}" placeholder="Who We Are">
                    </div>
                    <div class="col-md-8 mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="whoweare_title" class="form-control" value="{{ old('whoweare_title', $about->whoweare_title) }}">
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Paragraph</label>
                        <textarea name="whoweare_content" rows="3" class="form-control">{{ old('whoweare_content', $about->whoweare_content) }}</textarea>
                    </div>
                    @foreach($features as $i => $feature)
                        <div class="col-md-6 mb-3">
                            <label class="form-label small">Feature {{ $i + 1 }}</label>
                            <input type="text" name="features[{{ $i }}]" class="form-control" value="{{ $feature }}">
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- MISSION / VISION / VALUES --}}
        <div class="card mb-4">
            <div class="card-header"><h5 class="mb-0">Mission / Vision / Values</h5></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Mission</label>
                        <textarea name="mission" rows="4" class="form-control">{{ old('mission', $about->mission) }}</textarea>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Vision</label>
                        <textarea name="vision" rows="4" class="form-control">{{ old('vision', $about->vision) }}</textarea>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Values</label>
                        <textarea name="core_values" rows="4" class="form-control">{{ old('core_values', $about->core_values) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- TEAM SECTION --}}
        <div class="card mb-4">
            <div class="card-header"><h5 class="mb-0">Team Members</h5></div>
            <div class="card-body">
                <div class="row">
                    @foreach($team as $i => $member)
                        <div class="col-md-4 mb-3">
                            <div class="border rounded p-3 h-100">
                                <h6 class="mb-3">Member {{ $i + 1 }}</h6>
                                <div class="mb-2">
                                    <label class="form-label small">Name</label>
                                    <input type="text" name="team[{{ $i }}][name]" class="form-control" value="{{ $member['name'] ?? '' }}">
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small">Designation</label>
                                    <input type="text" name="team[{{ $i }}][role]" class="form-control" value="{{ $member['role'] ?? '' }}">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- CERTIFICATES SECTION --}}
        <div class="card mb-4">
            <div class="card-header"><h5 class="mb-0">Certificates</h5></div>
            <div class="card-body">
                <div class="row">
                    @foreach($certs as $i => $cert)
                        <div class="col-md-6 mb-3">
                            <div class="border rounded p-3 h-100">
                                <h6 class="mb-3">Certificate {{ $i + 1 }}</h6>
                                <div class="mb-2">
                                    <label class="form-label small">Label</label>
                                    <input type="text" name="certificates[{{ $i }}][label]" class="form-control" value="{{ $cert['label'] ?? '' }}">
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small">PDF File</label>
                                    <input type="file" name="certificates[{{ $i }}][pdf]" class="form-control" accept="application/pdf">
                                    @if(!empty($cert['pdf']))
                                        <a href="{{ asset($cert['pdf']) }}" target="_blank" class="d-inline-block mt-2 small">View current PDF</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="mb-5">
            <button type="submit" class="btn btn-primary">Save About Page</button>
        </div>

    </form>

</div>

@endsection
