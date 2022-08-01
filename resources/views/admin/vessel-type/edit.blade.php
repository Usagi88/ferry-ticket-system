@extends('admin.layouts.dashboard')

@section('content')

    <h2>Edit Vessel Type</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="/admin/vessel-type/{{ $vesselType->id }}" enctype="multipart/form-data">
        @method('PATCH')
        @csrf()
        
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" name="name" class="form-control" id="name" placeholder="Name" value="{{ $vesselType->name }}">
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <input type="text" name="description" class="form-control" id="description" placeholder="Description" value="{{ $vesselType->description }}">
        </div>

        <div class="form-group pt-2">
            <input class="btn btn-primary" type="submit" value="Submit">
        </div>
    </form>


@endsection