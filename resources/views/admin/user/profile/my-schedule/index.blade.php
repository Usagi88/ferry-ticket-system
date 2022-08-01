@extends('admin.layouts.dashboard')

@section('content')

    @if ($errors->any())
        <div class="alert alert-danger" route="alert">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li> 
                @endforeach
            </ul>
        </div>
    @endif

    @section('js_my_schedule_create_alert')
        <script>
            @if(!empty(Session::get('success')))
                var popupId = "{{ uniqid() }}";
                if(!sessionStorage.getItem('shown-' + popupId)) {
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: 'Your Schedule has been created',
                        showConfirmButton: false,
                        timer: 1200
                    })
                }
                sessionStorage.setItem('shown-' + popupId, '1');
            @endif
        </script>
    @endsection

    @section('js_my_schedule_edit_alert')
        <script>
            @if(!empty(Session::get('Edit-success')))
                var popupId = "{{ uniqid() }}";
                if(!sessionStorage.getItem('shown-' + popupId)) {
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: 'Your Schedule has been edited',
                        showConfirmButton: false,
                        timer: 1200
                    })
                }
                sessionStorage.setItem('shown-' + popupId, '1');
            @endif
            </script>
    @endsection

    <div class="islandIndexGridContainer">

        <div class="cardGridOne">
            <div class="cardNew card-icon-bg card-icon-bg-primary o-hidden mb-4">
                <div class="card-body text-center">
                    <i class="i-Add-Schedule"></i>
                    <div class="content">
                        <p class="text-muted mt-2 mb-0">Schedules</p>
                        <p class="lead text-primary-black text-24 mb-2">{{ count($user->schedules) }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="tableLabel">Schedules</div>
        <div class="tableCreateBtn">
            <a href="/admin/user/{{$user->id}}/profile/my-schedule/create" class="btnTest btn-primaryTest rippleTest" role="button" aria-pressed="true">Create Schedule</a>
        </div>
        
        <div class="gridDataTable">
            <div class="table dataTable hover">
                <table class="myTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User ID</th>
                            <th>Route Code</th>
                            <th>Vessel</th>
                            <th>Scheduled Date</th>
                            <th>Available Seats</th>
                            <th>Book</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($schedules as $schedule)
                            <tr>
                                <td>{{ $schedule['id'] }}</td>
                                <td>{{ $schedule['user_id'] }}</td>
                                <td>{{ $schedule->route->route_code }}</td>
                                <td>{{ $schedule->vessel->name }}</td>
                                <td>{{ $schedule['schedule_date'] }}</td>
                                <td>{{ $schedule['available_seats'] }}</td>
                                <td> 
                                    <button id="bookingBtn" data-routeCode="{{$schedule->route['route_code']}}"data-scheduleID="{{$schedule['id']}}" data-vesselID="{{$schedule->vessel['id']}}" data-userID="{{$schedule['user_id']}}" data-priceID="{{$schedule->route->price}}" onclick="createBooking(this)" class="btn btn-primary">Book</button>
                                </td>
                                <td>
                                    <div class="datatableActionButton">
                                        <a href="/admin/user/{{$user->id}}/profile/my-schedule/{{ $schedule['id'] }}" data-toggle="tooltip" data-placement="auto" title="Show"><i class="far fa-eye"></i></a>
                                        <a href="/admin/user/{{$user->id}}/profile/my-schedule/{{ $schedule['id'] }}/edit" data-toggle="tooltip" data-placement="auto" title="Edit"><i class="far fa-edit"></i></a>
                                        <a href="javascript:void(0)" data-scheduleID="{{$schedule['id']}}" onclick="deleteSchedule(this)" data-toggle="tooltip" data-placement="auto" title="Delete"><i class="far fa-trash-alt"></i></a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="card-footer small text-muted">Updated yesterday at 11:59 PM</div>
            </div>
        </div>
    </div>

    <div id="bookingModal" class="modalBooking">

        <!-- Modal content -->
        <div class="modal-content-booking">
          <div class="modal-header-booking">
            <span class="close-booking">&times;</span>
            <h2>Create Booking</h2>
          </div>
          <div class="modal-body-booking">
            <form method="POST" action="/admin/user/{{$user->id}}/profile/my-booking">
                {{ csrf_field() }}
                <div class="d-flex">
                    <h5 class="pr-2">Route Code:</h5>
                    <h5 id="routeCode"></h5>
                </div>
                
                <div class="form-group">
                    <div class="d-flex">
                        <h5 class="pr-2">Vessel ID:</h5>
                        <h5 id="vesselID"></h5>
                    </div>
                    <input type="hidden" id="vessel_id" name="vessel_id">
                </div>
                <div class="form-group" >
                    <input type="hidden" id="schedule_id" name="schedule_id">
                </div>   
                
                <div class="ticketType">
                    <label for="ticket_type_id">Select Ticket Type</label>
                    <select class="search-single custom-select" name="ticket_type_id" id="ticket_type_id" onchange="calcTotal()" required>
                        <optgroup label="Ticket Type ID">
                            @if (!$ticket_types->isEmpty() && $ticket_types != null )<!--If ticket_types are not empty and null -->
                                @foreach ($ticket_types as $ticket_type)
                                    <option value="{{ $ticket_type['id'] }}">{{ $ticket_type['id'] }} | {{ $ticket_type['name'] }}</option>
                                @endforeach
                            @endif
                        </optgroup>
                    </select>
                </div>
                    
                <div class="form-group">
                    <div class="ticketQuantity">
                        <label for="ticket_quantity">Quantity</label>
                        <input type="number" oninput="calcTotal()" min="1" name="ticket_quantity" id="ticketQty" data-field="qty" class="form-control" placeholder="Ticket Quantity" value="{{ old('ticket_quantity') }}" required>
                    </div>
                </div>
                <div class="form-group">
                    <div class="d-flex">
                        <h3 class="pr-2">Total:</h3>
                        <h3 id="totalAmount"></h3>
                    </div>
                </div>

                <div class="form-group pt-2">
                    <input class="btn btn-primary" type="submit" value="Submit">
                </div>
            </form>
          </div>
          <div class="modal-footer-schedule">
            <h3>Modal Footer</h3>
          </div>
        </div>
    </div>
    
    @section('js_tooltip')
        <script>
            //tooltip
            $(document).ready(function(){
                $('[data-toggle="tooltip"]').tooltip();
            });
        </script>
    @endsection
    
    @section('js_my_schedule_booking_pop')
        <script defer>
            var schedules = {!! json_encode($schedules->toArray(), JSON_HEX_TAG) !!};
            var scheduleId;

            function createBooking(d){
                //get id
                scheduleId = d.getAttribute('data-scheduleID');
                let vesselId = d.getAttribute('data-vesselID');
                let priceId = d.getAttribute('data-priceID');
                let routeCode = d.getAttribute('data-routeCode');
                //insert it into field
                document.getElementById('schedule_id').value = scheduleId;
                document.getElementById('vesselID').innerHTML = vesselId;
                document.getElementById('vessel_id').value = vesselId;
                //document.getElementById('totalAmount').innerHTML = priceId;
                document.getElementById('routeCode').innerHTML = routeCode;
                
                //search2
                $(document).ready(function() {
                    $('.search-single').select2();
                });
                
                // Get the modal
                let modal = document.getElementById("bookingModal");

                // Get the button that opens the modal
                let btn = document.getElementById("bookingBtn");

                // Get the <span> element that closes the modal
                let span = document.getElementsByClassName("close-booking")[0];

                // When the user clicks the button, open the modal 
                 modal.style.display = "block";
                
                // When the user clicks on <span> (x), close the modal
                span.onclick = function() {
                    modal.style.display = "none";
                    document.getElementById('ticketQty').value = "";
                    document.getElementById("totalAmount").innerHTML = "";
                }

                // When the user clicks anywhere outside of the modal, close it
                window.onclick = function(event) {
                    if (event.target == modal) {
                        modal.style.display = "none";
                        document.getElementById('ticketQty').value = "";
                        document.getElementById("totalAmount").innerHTML = "";
                    }
                }

            }

            function calcTotal(){
                var ticketSelectID = document.getElementById("ticket_type_id");
                var ticket_type_id = ticketSelectID[ticketSelectID.selectedIndex].value;
                //var schedule = searchID(scheduleId);
                //console.log(schedule);
                var price = schedules[scheduleId-1].route.all_ticket_type_of_route[ticket_type_id-1].pivot.price;
                var qty = document.getElementById("ticketQty").value;
                if(qty<0){
                    document.getElementById("totalAmount").innerHTML = "MVR " + 0;
                }else{
                    
                    if (!isNaN(qty) && !isNaN(price)){
                        var total = parseFloat(price) * qty
                    }
                    document.getElementById("totalAmount").innerHTML = "MVR " + total;
                }                
            }
            ///////////////NOT USING THIS FUNCTION NOW////////////////////
            function searchID(scheduleId){
                for (var i=0; i < schedules.length; i++) {
                    if (schedules[i].id == scheduleId) {
                        return schedules[i];
                    }
                }
                alert("Error. Could not find schedule ID");
            }
        </script>
    @endsection

    @section('js_delete_my_schedule')
        <script defer>
                function deleteMySchedule(e){

                    let id = e.getAttribute('data-scheduleID');
                    let userID = e.getAttribute('data-userID');
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed){
                            $.ajax({
                                type:'POST',
                                //url:'{{url("/admin/schedule")}}/' +id,
                                url:"/admin/user/" + userID +"/profile/my-schedule/"+id,
                                data:{
                                    "_token": "{{ csrf_token() }}",
                                },
                                success: function(response) {
                                    Swal.fire(
                                        'Deleted!',
                                        'Your Schedule has been deleted.',
                                        'success'
                                    )
                                    location.reload(true);
                                },
                                failure: function (response) {
                                    Swal.fire(
                                        'Internal Error',
                                        'Your Schedule was not deleted.',
                                        'error'
                                    )
                                }
                            });
                        }
                    })
                    

                }

            
        </script>
    @endsection

@endsection 