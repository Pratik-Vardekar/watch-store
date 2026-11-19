@extends('layouts.admin')
@section('content')

<div class="container-fluid my-2">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1">Contact Enquiries</h4>
            <p class="text-muted mb-0">Manage all contact enquiries</p>
        </div>
        <div class="d-flex gap-2 align-self-start align-self-sm-center">
            <a href="{{ route('admin.contact-enquiries.export', request()->all()) }}" class="btn btn-success">
                <i class="ti ti-download me-1"></i>
                Export
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered w-100">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Contact No.</th>
                            <th>Comment</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($contactEnquiries as $contactEnquiry)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $contactEnquiry->full_name }}</td>
                            <td>{{ $contactEnquiry->email }}</td>
                            <td>{{ $contactEnquiry->contact_no }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($contactEnquiry->comment, 60) }}</td>
                            <td>
                                @if($contactEnquiry->status)
                                    <span class="badge bg-success">Read</span>
                                @else
                                    <span class="badge bg-warning text-dark">Unread</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.contact-enquiries.edit', $contactEnquiry->id) }}" class="btn btn-sm btn-primary">Edit</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">No contact enquiries found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
