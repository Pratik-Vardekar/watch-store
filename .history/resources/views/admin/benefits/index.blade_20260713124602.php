@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Benefits</h4>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Title</th>
                            </tr>
                            <tr>
                                <th>Description</th>
                            </tr>
                            <tr>
                                <th>SVG Logo</th>
                            </tr>
                            <tr>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($benefits as $benefit)
                                <tr>
                                    <td>{{ $benefit->title }}</td>
                                    <td>{{ $benefit->description }}</td>
                                    <td>{{ $benefit->svg_logo }}</td>
                                    <td>{{ $benefit->status }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection