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

    <div class="bookingCreateGridContainer">
        <div class="gridUserCreateFirstRow">
            <div class="element-wrapper">
                <div class="element-box">
                   <form method="POST" action="/admin/schedule/{{ $schedule->id }}">
                        @method('PATCH')
                        @csrf()
                        <h5 class="form-header">Edit Schedule</h5>
                        <div class="form-desc">Edit schedule or user & vessel</div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-4" for="">Select Route</label>
                            <div class="col-sm-8">
                                <select class="search-single custom-select" name="route_id" required>
                                    <optgroup label="Route">
                                        @if (!$routes->isEmpty() && $routes != null )
                                            @foreach ($routes as $route)
                                                <option value="{{ $route['id'] }}" {{old('route_id',$schedule->route_id) == $route->id ? 'selected' : ''}}>{{ $route['id'] }} ● {{ $route['route_code'] }}</option>
                                            @endforeach
                                        @endif
                                    </optgroup>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-4" for="">Date</label>
                            <div class="col-sm-8"><input type="text"  name="schedule_date" class="form-control" id="datetimepicker2" placeholder="Date" value="{{ $schedule->schedule_date }}" required/></div>
                        </div>
                        <fieldset class="form-group">
                            <legend><span>User & Vessel</span></legend>
                            <div class="form-group row">
                                <label class="col-form-label col-sm-4" for="">Select User</label>
                                <div class="col-sm-8">
                                    <select class="search-single custom-select" name="user_id" required>
                                        <optgroup label="User">
                                            @foreach ($users as $user)
                                                <option value="{{ $user['id'] }}" {{old('user_id',$schedule->user_id) == $user->id ? 'selected' : ''}}>{{ $user['id'] }} ● {{ $user['first_name'] }} {{ $user['last_name'] }}</option>
                                            @endforeach
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
                                                    <option value="{{ $vessel['id'] }}" {{old('vessel_id',$schedule->vessel_id) == $vessel->id ? 'selected' : ''}}>{{ $vessel['id'] }} ● {{ $vessel['name'] }}</option>
                                                @endforeach
                                            @endif
                                        </optgroup>
                                    </select>
                                </div>
                            </div>
                        </fieldset>
                      <div class="form-buttons-w formBtnPosition">
                        <button class="btn btn-primary" type="submit">Submit</button>
                        <a class="btn btn-outline-primary" href="/admin/schedule">Back</a>
                      </div>
                   </form>
                </div>
             </div>
        </div>
    </div>

    @section('js_schedule_edit_datetimepicker')
        <script defer>
            $(document).ready(function() {
                $('.search-single').select2();

                $(function () {
                    $('#datetimepicker2').datetimepicker({
                        format: 'YYYY-MM-DD HH:mm:ss'
                    });
                });
            });
        </script>
    @endsection

@endsection