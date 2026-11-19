@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h4>Features</h4>
            <p class="text-muted">
                Manage homepage features
            </p>
        </div>

    </div>



    @if(session('success'))

    <div class="alert alert-success">

        {{session('success')}}

    </div>

    @endif



    <div class="card">


        <div class="card-body">


            <table class="table table-bordered table-striped">


                <thead>

                    <tr>

                        <th>#</th>
                        <th>Image</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Action</th>

                    </tr>

                </thead>



                <tbody>


                @foreach($features as $key=>$feature)

                    <tr>

                        <td>
                            {{$key+1}}
                        </td>


                        <td>

                            @if($feature->image)

                            <img src="{{asset('uploads/features/'.$feature->image)}}"
                                 width="70"
                                 height="70"
                                 class="rounded">

                            @else

                            No Image

                            @endif

                        </td>



                        <td>
                            {{$feature->title}}
                        </td>



                        <td>
                            {{$feature->description}}
                        </td>



                        <td>

                            @if($feature->status == 1)

                            <span class="badge bg-success">
                                Active
                            </span>

                            @else

                            <span class="badge bg-danger">
                                Inactive
                            </span>

                            @endif

                        </td>



                        <td>

                            <a href="{{route('feature.edit',$feature->id)}}"
                               class="btn btn-primary btn-sm">

                                Edit

                            </a>


                        </td>


                    </tr>


                @endforeach


                </tbody>


            </table>


        </div>


    </div>


</div>


@endsection