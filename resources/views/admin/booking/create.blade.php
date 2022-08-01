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
                   <form method="POST" action="/admin/booking">
                        {{ csrf_field() }}
                        <h5 class="form-header">Create Booking</h5>
                        <div class="form-desc">Create booking with ticket type</div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-4" for="">Select User</label>
                            <div class="col-sm-8">
                                <select class="search-single custom-select" name="user_id" required>
                                    <optgroup label="User">
                                        @foreach ($users as $user)
                                            <option value="{{$user->id}}">{{$user->id}} ● {{$user->first_name}} {{$user->last_name}}</option>
                                        @endforeach
                                    </optgroup>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-4" for="">Select Schedule</label>
                            <div class="col-sm-8">
                                <select class="search-single custom-select" name="schedule_id" required>
                                    <optgroup label="Schedule">
                                        @if (!$schedules->isEmpty() && $schedules != null )
                                            @foreach ($schedules as $schedule)   
                                                <option value="{{ $schedule['id'] }}">{{ $schedule['id'] }} ● {{ $schedule->route->route_code }}</option>
                                            @endforeach
                                        @endif
                                    </optgroup>
                                </select>
                            </div>
                        </div>
                        
                        <fieldset class="form-group">
                            <legend><span>Ticket</span></legend>
                            <div class="form-group row">
                                <label class="col-form-label col-sm-4" for="">Select Ticket Type</label>
                                <div class="col-sm-8">
                                    <select class="ticket_type_id custom-select" name="ticket_type_id">
                                        <option value="">Select Ticket Type</option>
                                        @if (!$ticket_types->isEmpty() && $ticket_types != null )
                                            @foreach ($ticket_types as $ticket_type)
                                                <option value="{{$ticket_type->id}}">{{$ticket_type->name}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-sm-4" for="">Quantity</label>
                                <div class="col-sm-8"><input type="number" name="ticket_quantity" class="form-control" placeholder="Quantity" value="{{ old('ticket_quantity') }}" required></div>
                            </div>
                        </fieldset>
                      <div class="form-buttons-w formBtnPosition">
                        <button class="btn btn-primary" type="submit">Submit</button>
                        <a class="btn btn-outline-primary" href="/admin/booking">Back</a>
                      </div>
                   </form>
                </div>
             </div>
        </div>
    </div>

    @section('js_booking_create_select')
        <script>
            $(document).ready(function() {
                $('.search-single').select2();
            });
        </script>
    @endsection

@endsection
