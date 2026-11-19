@extends('layouts.admin')

@section('content')

<div class="container">

<h4>Add Brand</h4>

<form action="{{ route('brands.store') }}"
      method="POST"
      enctype="multipart/form-data">

@csrf

<div class="mb-3">
    <label>Name</label>
    <input type="text"
           name="name"
           class="form-control"
           required>
</div>

<div class="mb-3">
    <label>Logo</label>
    <input type="file"
           name="logo"
           class="form-control"
           required>
</div>

<div class="mb-3">
    <label>Link</label>
    <input type="text"
           name="link"
           class="form-control">
</div>

<div class="mb-3">
    <label>Status</label>

    <select name="status" class="form-control">

        <option value="1">Active</option>

        <option value="0">Inactive</option>

    </select>

</div>

<button class="btn btn-primary">
    Save Brand
</button>

</form>

</div>

@endsection
