@extends('layouts.admin')
@section('content')

<div class="container-fluid my-2">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1">Edit Contact Enquiry</h4>
            <p class="text-muted mb-0">Update enquiry details and status</p>
        </div>
        <a href="{{ route('admin.contact-enquiries.index') }}" class="btn btn-secondary">Back</a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.contact-enquiries.update', $contactEnquiry->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="full_name" class="form-control"
                        value="{{ old('full_name', $contactEnquiry->full_name) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control"
                        value="{{ old('email', $contactEnquiry->email) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Contact No.</label>
                    <input type="text" name="contact_no" class="form-control"
                        value="{{ old('contact_no', $contactEnquiry->contact_no) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Comment</label>
                    <textarea name="comment" class="form-control" rows="4">{{ old('comment', $contactEnquiry->comment) }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select" required>
                        <option value="0" {{ (string) old('status', $contactEnquiry->status ? '1' : '0') === '0' ? 'selected' : '' }}>Unread</option>
                        <option value="1" {{ (string) old('status', $contactEnquiry->status ? '1' : '0') === '1' ? 'selected' : '' }}>Read</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-success">Update Enquiry</button>
            </form>
        </div>
    </div>
</div>
@endsection
