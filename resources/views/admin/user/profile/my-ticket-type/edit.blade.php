@extends('admin.layouts.dashboard')

@section('content')

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="assignVesselCreateGridContainer">
        <div class="gridAssignVesselCreateFirstRow">
            <div class="element-wrapper">
                <div class="element-box">
                   <form method="POST" action="/admin/user/{{ $user->id }}/profile/my-ticket-type/{{$ticketType->id}}">
                        @method('PATCH')
                        @csrf()
                        <h5 class="form-header">Edit Ticket Type</h5>
                        <div class="form-desc">Edit ticket name or description</div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-4" for="">Name</label>
                            <div class="col-sm-8"><input class="form-control" name="name" type="text" placeholder="Name" value="{{ $ticketType->name }}" required></div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-4" for="">Description</label>
                            <div class="col-sm-8"><input class="form-control" name="description" type="text" placeholder="Description" value="{{ $ticketType->description }}" required></div>
                        </div>
                        
                      <div class="form-buttons-w formBtnPosition">
                        <button class="btn btn-primary" type="submit">Submit</button>
                        <a class="btn btn-outline-primary" href="/admin/user/{{$user->id}}/profile/my-ticket-type">Back</a>
                      </div>
                   </form>
                </div>
             </div>
        </div>
    </div>

@endsection