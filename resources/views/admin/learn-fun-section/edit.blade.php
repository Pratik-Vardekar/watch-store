@extends('layouts.admin')

@section('content')
@php
    $defaultFeatures = [
        ['highlight' => '', 'text' => '', 'bg_class' => 'bgPurpleSub', 'text_class' => 'txtpurple', 'icon' => ''],
        ['highlight' => '', 'text' => '', 'bg_class' => 'bgYellowSub', 'text_class' => 'txtyellow', 'icon' => ''],
        ['highlight' => '', 'text' => '', 'bg_class' => 'bgGreenSub', 'text_class' => 'txtgreen', 'icon' => ''],
        ['highlight' => '', 'text' => '', 'bg_class' => 'bgRedSub', 'text_class' => 'txtred', 'icon' => ''],
    ];
    $features = $section->features ?: $defaultFeatures;
    $features = array_pad($features, 4, ['highlight' => '', 'text' => '', 'bg_class' => '', 'text_class' => '', 'icon' => '']);
    $bgOptions = ['bgPurpleSub', 'bgYellowSub', 'bgGreenSub', 'bgRedSub'];
    $textOptions = ['txtpurple', 'txtyellow', 'txtgreen', 'txtred'];
@endphp

<div class="container-fluid">

    <div class="mb-4">
        <h4 class="mb-1">Learn Fun Section</h4>
        <p class="text-muted mb-0">Edit the homepage Word Games section and feature cards.</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('admin.learn-fun-section.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="card mb-4">
            <div class="card-header"><h5 class="mb-0">Main Content</h5></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Subtitle</label>
                        <input type="text" name="subtitle" class="form-control"
                            value="{{ old('subtitle', $section->subtitle) }}"
                            placeholder="e.g. Bringing Words to Life">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Button Text</label>
                        <input type="text" name="button_text" class="form-control"
                            value="{{ old('button_text', $section->button_text) }}"
                            placeholder="e.g. Read More">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Title <small class="text-muted">(black part)</small></label>
                        <input type="text" name="title" class="form-control"
                            value="{{ old('title', $section->title) }}"
                            placeholder="e.g. Word Games That Makes">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Title Highlight <small class="text-muted">(orange part)</small></label>
                        <input type="text" name="title_highlight" class="form-control"
                            value="{{ old('title_highlight', $section->title_highlight) }}"
                            placeholder="e.g. Learning Fun!">
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Paragraph</label>
                        <textarea name="content" rows="5" class="form-control">{{ old('content', $section->content) }}</textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Button URL</label>
                        <input type="text" name="button_url" class="form-control"
                            value="{{ old('button_url', $section->button_url) }}"
                            placeholder="/about or #">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Section Image</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                        @if($section->image)
                            <div class="mt-2">
                                <img src="{{ asset($section->image) }}" alt="Learn Fun" style="max-height:120px;" class="rounded border">
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header"><h5 class="mb-0">Feature Cards</h5></div>
            <div class="card-body">
                @foreach($features as $i => $feature)
                    <div class="border rounded p-3 mb-3">
                        <h6 class="mb-3">Card {{ $i + 1 }}</h6>
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label small">Highlight Word</label>
                                <input type="text" name="features[{{ $i }}][highlight]" class="form-control"
                                    value="{{ old("features.$i.highlight", $feature['highlight'] ?? '') }}"
                                    placeholder="e.g. Enhances">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label small">Title Text</label>
                                <input type="text" name="features[{{ $i }}][text]" class="form-control"
                                    value="{{ old("features.$i.text", $feature['text'] ?? '') }}"
                                    placeholder="e.g. Cognitive Skills">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label small">Background Class</label>
                                <select name="features[{{ $i }}][bg_class]" class="form-select">
                                    @foreach($bgOptions as $opt)
                                        <option value="{{ $opt }}" @selected(old("features.$i.bg_class", $feature['bg_class'] ?? '') === $opt)>{{ $opt }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label small">Highlight Color Class</label>
                                <select name="features[{{ $i }}][text_class]" class="form-select">
                                    @foreach($textOptions as $opt)
                                        <option value="{{ $opt }}" @selected(old("features.$i.text_class", $feature['text_class'] ?? '') === $opt)>{{ $opt }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small">Icon Image</label>
                                <input type="file" name="features[{{ $i }}][icon]" class="form-control" accept="image/*">
                                @if(!empty($feature['icon']))
                                    <div class="mt-2">
                                        <img src="{{ asset($feature['icon']) }}" alt="Icon" style="max-height:48px;" class="rounded border bg-white p-1">
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <button type="submit" class="btn btn-success">Update Learn Fun Section</button>
    </form>

</div>
@endsection
