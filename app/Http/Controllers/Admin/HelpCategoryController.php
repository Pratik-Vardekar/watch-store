<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HelpCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;

class HelpCategoryController extends Controller
{
    public function index()
    {
        $categories = HelpCategory::withCount('faqs')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return $this->helpView('help-category-index', compact('categories'));
    }

    public function create()
    {
        return $this->helpView('help-category-create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'color' => ['required', Rule::in(array_keys(HelpCategory::COLORS))],
            'sort_order' => 'nullable|integer|min:0',
            'status' => 'required|in:0,1',
        ]);

        HelpCategory::create([
            'title' => $request->title,
            'color' => $request->color,
            'sort_order' => $request->sort_order ?? 0,
            'status' => $request->status,
        ]);

        return redirect()
            ->route('admin.help-categories.index')
            ->with('success', 'Help category added successfully.');
    }

    public function edit(HelpCategory $helpCategory)
    {
        return $this->helpView('help-category-edit', compact('helpCategory'));
    }

    public function update(Request $request, HelpCategory $helpCategory)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'color' => ['required', Rule::in(array_keys(HelpCategory::COLORS))],
            'sort_order' => 'nullable|integer|min:0',
            'status' => 'required|in:0,1',
        ]);

        $helpCategory->update([
            'title' => $request->title,
            'color' => $request->color,
            'sort_order' => $request->sort_order ?? 0,
            'status' => $request->status,
        ]);

        return redirect()
            ->route('admin.help-categories.index')
            ->with('success', 'Help category updated successfully.');
    }

    public function destroy(HelpCategory $helpCategory)
    {
        $helpCategory->delete();

        return redirect()
            ->route('admin.help-categories.index')
            ->with('success', 'Help category deleted successfully.');
    }

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
            'help-category-index' => <<<'BLADE'
@extends('layouts.admin')

@section('content')
<div class="container-fluid my-2">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Help Categories</h4>
            <p class="text-muted mb-0">Manage Help Center accordion sections (Orders, Shipping, etc.)</p>
        </div>
        <a href="{{ route('admin.help-categories.create') }}" class="btn btn-primary">
            <i class="ti ti-plus me-1"></i> Add Category
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead>
                        <tr>
                            <th width="60">SL</th>
                            <th>Title</th>
                            <th width="120">Color</th>
                            <th width="90">FAQs</th>
                            <th width="90">Order</th>
                            <th width="100">Status</th>
                            <th width="160">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $index => $category)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $category->title }}</td>
                                <td>
                                    <span class="badge bg-secondary">{{ ucfirst($category->color) }}</span>
                                </td>
                                <td>{{ $category->faqs_count }}</td>
                                <td>{{ $category->sort_order }}</td>
                                <td>
                                    @if($category->status)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.help-categories.edit', $category) }}" class="btn btn-sm btn-primary">
                                        <i class="ti ti-edit"></i> Edit
                                    </a>
                                    <form action="{{ route('admin.help-categories.destroy', $category) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('Delete this category and its FAQs?');">
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
                                <td colspan="7" class="text-center">No categories found. Click Add Category to create one.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
BLADE,
            'help-category-create' => <<<'BLADE'
@extends('layouts.admin')

@section('content')
<div class="container-fluid my-2">

    <div class="mb-4">
        <h4 class="mb-1">Add Help Category</h4>
        <p class="text-muted mb-0">Create a new Help Center section</p>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.help-categories.store') }}" method="POST" class="row g-3">
                @csrf

                <div class="col-md-6">
                    <label class="form-label">Title</label>
                    <input type="text" name="title"
                        class="form-control @error('title') is-invalid @enderror"
                        value="{{ old('title') }}" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Color</label>
                    <select name="color" class="form-select @error('color') is-invalid @enderror" required>
                        @foreach(\App\Models\HelpCategory::COLORS as $value => $label)
                            <option value="{{ $value }}" {{ old('color', 'yellow') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('color')
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

                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="1" {{ (string) old('status', '1') === '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ (string) old('status', '1') === '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Add Category</button>
                    <a href="{{ route('admin.help-categories.index') }}" class="btn btn-secondary">Back</a>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
BLADE,
            'help-category-edit' => <<<'BLADE'
@extends('layouts.admin')

@section('content')
<div class="container-fluid my-2">

    <div class="mb-4">
        <h4 class="mb-1">Edit Help Category</h4>
        <p class="text-muted mb-0">Update Help Center section</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.help-categories.update', $helpCategory) }}" method="POST" class="row g-3">
                @csrf
                @method('PUT')

                <div class="col-md-6">
                    <label class="form-label">Title</label>
                    <input type="text" name="title"
                        class="form-control @error('title') is-invalid @enderror"
                        value="{{ old('title', $helpCategory->title) }}" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Color</label>
                    <select name="color" class="form-select @error('color') is-invalid @enderror" required>
                        @foreach(\App\Models\HelpCategory::COLORS as $value => $label)
                            <option value="{{ $value }}" {{ old('color', $helpCategory->color) === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('color')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Sort Order</label>
                    <input type="number" name="sort_order" min="0"
                        class="form-control @error('sort_order') is-invalid @enderror"
                        value="{{ old('sort_order', $helpCategory->sort_order) }}">
                    @error('sort_order')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="1" {{ (string) old('status', (string) (int) $helpCategory->status) === '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ (string) old('status', (string) (int) $helpCategory->status) === '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Update Category</button>
                    <a href="{{ route('admin.help-categories.index') }}" class="btn btn-secondary">Back</a>
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
