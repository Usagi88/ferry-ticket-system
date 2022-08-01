@extends('admin.layouts.dashboard')

@section('content')

    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
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
                   <form method="POST" action="/admin/user/{{$user->id}}/profile/my-assigned-vessel">
                        {{ csrf_field() }}
                        <h5 class="form-header">Assign Vessel</h5>
                        <div class="form-desc">Assign an agent to a vessel</div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-4" for="">Select Agent</label>
                            <div class="col-sm-8">
                                <select class="search-single custom-select" name="user_id"required>
                                    <optgroup label="User">
                                        @if (!$users->isEmpty() && $users != null )
                                            @foreach ($users as $user)
                                                <option value="{{$user->id}}">{{$user->id}} ● {{$user->first_name}} {{$user->last_name}}</option>
                                            @endforeach
                                        @endif
                                    </optgroup>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-4" for="">Select Vessel</label>
                            <div class="col-sm-8">
                                <select class="search-single custom-select" name="vessel_id" required>
                                    <optgroup label="Vessel">
                                        @if (!$vessels->isEmpty() && $vessels != null )
                                            @foreach ($vessels as $vessel)
                                                <option value="{{ $vessel['id'] }}">{{ $vessel['id'] }} ● {{ $vessel['name'] }}</option>
                                            @endforeach
                                        @endif
                                    </optgroup>
                                </select>
                            </div>
                        </div>
                        
                      <div class="form-buttons-w formBtnPosition">
                        <button class="btn btn-primary" type="submit">Submit</button>
                        <a class="btn btn-outline-primary" href="/admin/user/{{$user->id}}/profile/my-assigned-vessel">Back</a>
                      </div>
                   </form>
                </div>
             </div>
        </div>
    </div>

    @section('js_my_assign_vessel_create_select')
        <script>
            $(document).ready(function() {
                $('.search-single').select2();
            });
        </script>
    @endsection

@endsection