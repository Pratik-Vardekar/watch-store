<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HelpCategory;
use App\Models\HelpFaq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class HelpFaqController extends Controller
{
    public function index()
    {
        $categories = HelpCategory::with([
                'faqs' => fn ($q) => $q->orderBy('sort_order')->orderBy('id'),
            ])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return $this->helpView('help-faq-index', compact('categories'));
    }

    public function create()
    {
        $categories = HelpCategory::orderBy('sort_order')->orderBy('id')->get();

        return $this->helpView('help-faq-create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'help_category_id' => 'required|exists:help_categories,id',
            'question' => 'required|string|max:500',
            'answer' => 'required|string',
            'sort_order' => 'nullable|integer|min:0',
            'status' => 'required|in:0,1',
        ]);

        HelpFaq::create([
            'help_category_id' => $request->help_category_id,
            'question' => $request->question,
            'answer' => $request->answer,
            'sort_order' => $request->sort_order ?? 0,
            'status' => $request->status,
        ]);

        return redirect()
            ->route('admin.help-faqs.index')
            ->with('success', 'Help FAQ added successfully.');
    }

    public function edit(HelpFaq $helpFaq)
    {
        $categories = HelpCategory::orderBy('sort_order')->orderBy('id')->get();

        return $this->helpView('help-faq-edit', compact('helpFaq', 'categories'));
    }

    public function update(Request $request, HelpFaq $helpFaq)
    {
        $request->validate([
            'help_category_id' => 'required|exists:help_categories,id',
            'question' => 'required|string|max:500',
            'answer' => 'required|string',
            'sort_order' => 'nullable|integer|min:0',
            'status' => 'required|in:0,1',
        ]);

        $helpFaq->update([
            'help_category_id' => $request->help_category_id,
            'question' => $request->question,
            'answer' => $request->answer,
            'sort_order' => $request->sort_order ?? 0,
            'status' => $request->status,
        ]);

        return redirect()
            ->route('admin.help-faqs.index')
            ->with('success', 'Help FAQ updated successfully.');
    }

    public function destroy(HelpFaq $helpFaq)
    {
        $helpFaq->delete();

        return redirect()
            ->route('admin.help-faqs.index')
            ->with('success', 'Help FAQ deleted successfully.');
    }

    /**
     * Publish embedded Blade templates into storage (writable on shared hosting)
     * so the page works even when resources/views was never uploaded.
     */
    private function helpView(string $name, array $data = [])
    {
        $dir = storage_path('app/help-admin-views');
        File::ensureDirectoryExists($dir);

        $path = $dir.DIRECTORY_SEPARATOR.$name.'.blade.php';
        $template = $this->templates()[$name];

        if (! is_file($path) || md5_file($path) !== md5($template)) {
            File::put($path, $template);
        }

        view()->addLocation($dir);

        return view($name, $data);
    }

    private function templates(): array
    {
        return [
            'help-faq-index' => <<<'BLADE'
@extends('layouts.admin')

@section('content')
<div class="container-fluid my-2">

    <div class="mb-4">
        <h4 class="mb-1">Help FAQs</h4>
        <p class="text-muted mb-0">Manage questions and answers by Help Center category</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @forelse($categories as $category)
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">{{ $category->title }}</h5>
                    <small class="text-muted">
                        Color: {{ ucfirst($category->color) }}
                        · {{ $category->faqs->count() }} FAQ{{ $category->faqs->count() === 1 ? '' : 's' }}
                    </small>
                </div>
                <div>
                    @if($category->status)
                        <span class="badge bg-success">Active</span>
                    @else
                        <span class="badge bg-danger">Inactive</span>
                    @endif
                    <a href="{{ route('admin.help-faqs.create', ['help_category_id' => $category->id]) }}"
                        class="btn btn-sm btn-primary ms-2">
                        <i class="ti ti-plus"></i> Add FAQ
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle mb-0">
                        <thead>
                            <tr>
                                <th width="60">SL</th>
                                <th>Question</th>
                                <th>Answer</th>
                                <th width="90">Order</th>
                                <th width="100">Status</th>
                                <th width="160">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($category->faqs as $index => $faq)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $faq->question }}</td>
                                    <td>{{ \Illuminate\Support\Str::limit(strip_tags($faq->answer), 80) }}</td>
                                    <td>{{ $faq->sort_order }}</td>
                                    <td>
                                        @if($faq->status)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.help-faqs.edit', $faq) }}" class="btn btn-sm btn-primary">
                                            <i class="ti ti-edit"></i> Edit
                                        </a>
                                        <form action="{{ route('admin.help-faqs.destroy', $faq) }}" method="POST" class="d-inline"
                                            onsubmit="return confirm('Delete this FAQ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No FAQs in this category yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @empty
        <div class="card">
            <div class="card-body text-center text-muted">
                No categories found. Create a Help Category first, then add FAQs.
            </div>
        </div>
    @endforelse

</div>
@endsection
BLADE,
            'help-faq-create' => <<<'BLADE'
@extends('layouts.admin')

@section('content')
<div class="container-fluid my-2">

    <div class="mb-4">
        <h4 class="mb-1">Add Help FAQ</h4>
        <p class="text-muted mb-0">Create a new Help Center question and answer</p>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.help-faqs.store') }}" method="POST" class="row g-3">
                @csrf

                <div class="col-md-6">
                    <label class="form-label">Category</label>
                    <select name="help_category_id" class="form-select @error('help_category_id') is-invalid @enderror" required>
                        <option value="">Select category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ (string) old('help_category_id', request('help_category_id')) === (string) $category->id ? 'selected' : '' }}>
                                {{ $category->title }}
                            </option>
                        @endforeach
                    </select>
                    @error('help_category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Sort Order</label>
                    <input type="number" name="sort_order" min="0"
                        class="form-control @error('sort_order') is-invalid @enderror"
                        value="{{ old('sort_order', 0) }}">
                    @error('sort_order')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <label class="form-label">Question</label>
                    <input type="text" name="question"
                        class="form-control @error('question') is-invalid @enderror"
                        value="{{ old('question') }}" required>
                    @error('question')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <label class="form-label">Answer</label>
                    <textarea name="answer" rows="6"
                        class="form-control @error('answer') is-invalid @enderror"
                        required>{{ old('answer') }}</textarea>
                    @error('answer')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="1" {{ (string) old('status', '1') === '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ (string) old('status', '1') === '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Add FAQ</button>
                    <a href="{{ route('admin.help-faqs.index') }}" class="btn btn-secondary">Back</a>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
BLADE,
            'help-faq-edit' => <<<'BLADE'
@extends('layouts.admin')

@section('content')
<div class="container-fluid my-2">

    <div class="mb-4">
        <h4 class="mb-1">Edit Help FAQ</h4>
        <p class="text-muted mb-0">Update Help Center question and answer</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.help-faqs.update', $helpFaq) }}" method="POST" class="row g-3">
                @csrf
                @method('PUT')

                <div class="col-md-6">
                    <label class="form-label">Category</label>
                    <select name="help_category_id" class="form-select @error('help_category_id') is-invalid @enderror" required>
                        <option value="">Select category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ (string) old('help_category_id', $helpFaq->help_category_id) === (string) $category->id ? 'selected' : '' }}>
                                {{ $category->title }}
                            </option>
                        @endforeach
                    </select>
                    @error('help_category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Sort Order</label>
                    <input type="number" name="sort_order" min="0"
                        class="form-control @error('sort_order') is-invalid @enderror"
                        value="{{ old('sort_order', $helpFaq->sort_order) }}">
                    @error('sort_order')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <label class="form-label">Question</label>
                    <input type="text" name="question"
                        class="form-control @error('question') is-invalid @enderror"
                        value="{{ old('question', $helpFaq->question) }}" required>
                    @error('question')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <label class="form-label">Answer</label>
                    <textarea name="answer" rows="6"
                        class="form-control @error('answer') is-invalid @enderror"
                        required>{{ old('answer', $helpFaq->answer) }}</textarea>
                    @error('answer')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="1" {{ (string) old('status', (string) (int) $helpFaq->status) === '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ (string) old('status', (string) (int) $helpFaq->status) === '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Update FAQ</button>
                    <a href="{{ route('admin.help-faqs.index') }}" class="btn btn-secondary">Back</a>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
BLADE,
        ];
    }
}
