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

    <div class="userCreateGridContainer">
        <div class="gridUserCreateFirstRow">
            <div class="element-wrapper">
                <div class="element-box">
                   <form method="POST" action="/admin/vessel/{{ $vessel->id }}" enctype="multipart/form-data">
                        @method('PATCH')
                        @csrf()
                        <h5 class="form-header">Edit Vessel</h5>
                        <div class="form-desc">Edit vessels with vessel type</div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-4" for="">Select Owner</label>
                            <div class="col-sm-8">
                                <select class="search-single custom-select" name="owner_id" id="owner_id" required>
                                    <optgroup label="User">
                                        @foreach ($users as $user)
                                            <option value="{{ $user['id'] }}" {{old('owner_id',$vessel->owner_id) == $user->id ? 'selected' : ''}}>{{ $user['id'] }} ● {{ $user['first_name'] }} {{ $user['last_name'] }}</option>
                                        @endforeach
                                    </optgroup>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-4" for=""> Vessel Name</label>
                            <div class="col-sm-8"><input class="form-control" name="name" type="text" placeholder="Name" value="{{ $vessel->name }}" required></div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-4" for="">Seat Capacity</label>
                            <div class="col-sm-8"><input type="number" name="seat_capacity" class="form-control" placeholder="Seat Capacity" value="{{ $vessel->seat_capacity }}" required></div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-4" for="">Max Accompanied Cargo</label>   
                            <div class="col-sm-8"><input type="number" name="max_accompanied_cargo" class="form-control" placeholder="Max Accompanied Cargo" value="{{ $vessel->max_accompanied_cargo }}" required></div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-4" for="">Max Unaccompanied Cargo</label>
                            <div class="col-sm-8"><input type="number" name="max_unaccompanied_cargo" class="form-control" placeholder="Max Unaccompanied Cargo" value="{{ $vessel->max_unaccompanied_cargo }}" required></div>
                        </div>
                        
                        <fieldset class="form-group">
                            <legend><span>Vessel Type</span></legend>
                            <div class="form-group row">
                                <label class="col-form-label col-sm-4" for=""> Select User</label>
                                <div class="col-sm-8">
                                    <select class="vessel_type custom-select" name="vessel_type_id" id="vessel_type_id" required>
                                        <option value="">Select Vessel Type</option>
                                        @if (!$vessel_types->isEmpty() && $vessel_types != null )
                                            @foreach ($vessel_types as $vessel_type)
                                                <option value="{{$vessel_type->id}}" {{old('vessel_type_id',$vessel->vessel_type_id) == $vessel_type->id ? 'selected' : ''}}>{{$vessel_type->name}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </fieldset>
                      <div class="form-buttons-w formBtnPosition">
                        <button class="btn btn-primary" type="submit">Submit</button>
                        <a class="btn btn-outline-primary" href="/admin/vessel">Back</a>
                      </div>
                   </form>
                </div>
             </div>
        </div>
    </div>

    @section('js_vessel_edit_select')
        <script>
            $(document).ready(function() {
                $('.search-single').select2();
            });
        </script>
    @endsection

@endsection