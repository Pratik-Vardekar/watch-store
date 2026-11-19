@extends('layouts.admin')

@section('content')
<div class="container-fluid my-2">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Help FAQs</h4>
            <p class="text-muted mb-0">Manage questions and answers by Help Center category</p>
        </div>
    </div>

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
