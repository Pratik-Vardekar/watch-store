@extends('layouts.admin')

@section('content')

<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h4>GST Slabs</h4>

        <a href="{{ route('gst.settings.create') }}"
           class="btn btn-primary">
            Add GST
        </a>
    </div>

    <div class="card-body">

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>GST %</th>
                  
                    <th width="180">Action</th>
                </tr>
            </thead>

            <tbody>
                @foreach($gstSettings as $gst)
                <tr>
                    <td>{{ $loop->iteration }}</td>               
                         <td>{{ $gst->gst_percentage }}%</td>
                         <td class="align-middle">
                            <div class="d-flex justify-content-center gap-1">
                        
                                <!-- STATUS TOGGLE -->
                                <form action="{{ route('gst.settings.status', $gst->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                        
                                    <button type="submit"
                                        class="btn btn-light btn-icon btn-sm rounded-circle"
                                        title="{{ $gst->status ? 'Deactivate' : 'Activate' }}">
                                        @if($gst->status)
                                            <i class="ti ti-toggle-right fs-5 text-success"></i>
                                        @else
                                            <i class="ti ti-toggle-left fs-5 text-danger"></i>
                                        @endif
                                    </button>
                                </form>
                        
                                <!-- EDIT -->
                                <a href="{{ route('gst.settings.edit', $gst->id) }}"
                                   class="btn btn-light btn-icon btn-sm rounded-circle"
                                   title="Edit GST">
                                    <i class="ti ti-edit fs-5"></i>
                                </a>
                        
                                <!-- DELETE -->
                                <form action="{{ route('gst.settings.destroy', $gst->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('Delete GST slab?')">
                                    @csrf
                                    @method('DELETE')
                        
                                    <button type="submit"
                                        class="btn btn-light btn-icon btn-sm rounded-circle"
                                        title="Delete GST">
                                        <i class="ti ti-trash fs-5 text-danger"></i>
                                    </button>
                                </form>
                        
                            </div>
                        </td>
                </tr>
                @endforeach
            </tbody>

        </table>

        {{ $gstSettings->links() }}

    </div>
</div>

@endsection