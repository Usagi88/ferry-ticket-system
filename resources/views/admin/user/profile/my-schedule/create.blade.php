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

    <div class="bookingCreateGridContainer">
        <div class="gridUserCreateFirstRow">
            <div class="element-wrapper">
                <div class="element-box">
                   <form method="POST" action="/admin/user/{{$user->id}}/profile/my-schedule">
                        {{ csrf_field() }}
                        <h5 class="form-header">Create Schedule</h5>
                        <div class="form-desc">Create schedule a schedule with route & date</div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-4" for="">Select Route</label>
                            <div class="col-sm-8">
                                <select class="search-single custom-select" name="route_id" required>
                                    <optgroup label="Route">
                                        @if (!$routes->isEmpty() && $routes != null )
                                            @foreach ($routes as $route)
                                                <option value="{{ $route['id'] }}">{{ $route['id'] }} ● {{ $route['route_code'] }}</option>
                                            @endforeach
                                        @endif
                                    </optgroup>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-4" for="">Date</label>
                            <div class="col-sm-8"><input type="text"  name="schedule_date" class="form-control" id="datetimepicker2" placeholder="Date" value="{{ old('schedule_date') }}" required/></div>
                        </div>
                        <fieldset class="form-group">
                            <legend><span>Vessel</span></legend>
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
                        </fieldset>
                      <div class="form-buttons-w formBtnPosition">
                        <button class="btn btn-primary" type="submit">Submit</button>
                        <a class="btn btn-outline-primary" href="/admin/user/{{$user->id}}/profile/my-schedule">Back</a>
                      </div>
                   </form>
                </div>
             </div>
        </div>
    </div>

    @section('js_my_schedule_create_datetimepicker')
        <script type="text/javascript">
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
