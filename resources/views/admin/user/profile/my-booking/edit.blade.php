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
                   <form method="POST" action="/admin/user/{{$user->id}}/profile/my-booking/{{ $booking->id }}">
                        @method('PATCH')
                        @csrf()
                        <h5 class="form-header">Edit Booking</h5>
                        <div class="form-desc">Edit booking or ticket type</div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-4" for="">Select Schedule</label>
                            <div class="col-sm-8">
                                <select class="search-single custom-select" name="schedule_id" required>
                                    <optgroup label="Schedule">
                                        @if (!$schedules->isEmpty() && $schedules != null )
                                            @foreach ($schedules as $schedule)   
                                                <option value="{{ $schedule['id'] }}" {{old('schedule_id',$booking->schedule_id) == $schedule->id ? 'selected' : ''}}>{{ $schedule['id'] }} ● {{ $schedule->route->route_code }}</option>
                                            @endforeach
                                        @endif
                                    </optgroup>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-4" for="">Select Booking Status</label>
                            <div class="col-sm-8">
                                <select class="search-single custom-select" name="booking_status" required>
                                    <optgroup label="Booking Status">
                                        @if (!$booking_statuses->isEmpty() && $booking_statuses != null )
                                            @foreach ($booking_statuses as $booking_status)
                                                <option value="{{$booking_status->id}}" {{old('booking_status',$booking->booking_status_id) == $booking_status->id ? 'selected' : ''}}>{{$booking_status->name}}</option>
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
                                                <option value="{{$ticket_type->id}}" {{old('ticket_type_id',$booking->ticket_type_id) == $ticket_type->id ? 'selected' : ''}}>{{$ticket_type->name}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-sm-4" for="">Quantity</label>
                                <div class="col-sm-8"><input type="number" name="ticket_quantity" class="form-control" placeholder="Quantity" value="{{ $booking->ticket_quantity }}" required></div>
                            </div>
                        </fieldset>
                      <div class="form-buttons-w formBtnPosition">
                        <button class="btn btn-primary" type="submit">Submit</button>
                        <a class="btn btn-outline-primary" href="/admin/user/{{$user->id}}/profile/my-booking/">Back</a>
                      </div>
                   </form>
                </div>
             </div>
        </div>
    </div>

    @section('js_my_booking_edit_select')
        <script>
            $(document).ready(function() {
                $('.search-single').select2();
            });
        </script>
    @endsection

@endsection