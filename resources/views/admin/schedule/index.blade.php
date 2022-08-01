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

    @section('js_schedule_create_alert')
        <script>
            @if(!empty(Session::get('success')))
                var popupId = "{{ uniqid() }}";
                if(!sessionStorage.getItem('shown-' + popupId)) {
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: 'Schedule has been created',
                        showConfirmButton: false,
                        timer: 1200
                    })
                }
                sessionStorage.setItem('shown-' + popupId, '1');
            @endif
        </script>
    @endsection

    @section('js_schedule_edit_alert')
        <script>
            @if(!empty(Session::get('Edit-success')))
                var popupId = "{{ uniqid() }}";
                if(!sessionStorage.getItem('shown-' + popupId)) {
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: 'Schedule has been edited',
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
                        <p class="lead text-primary-black text-24 mb-2">{{$schedules->count()}}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="tableLabel">Schedules</div>
        <div class="tableCreateBtn">
            <a href="/admin/schedule/create" class="btnTest btn-primaryTest rippleTest" role="button" aria-pressed="true">Create Schedule</a>
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
                            <th>Start</th>
                            <th>End</th>
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
                                <td>{{ $schedule->route->route_name }}</td>
                                @if(isset($schedule->vessel))
                                    <td>{{ $schedule->vessel->name }}</td>
                                @else
                                    <td><strong>none</strong></td>
                                @endif
                                
                                <td>{{ $schedule['start'] }}</td>
                                <td>{{ $schedule['end'] }}</td>
                                <td>{{ $schedule['available_seats'] }}</td>
                                <td>
                                    @if(isset($schedule->vessel))
                                        <button id="bookingBtn" data-routeID="{{$schedule->route->id}}" data-scheduleID="{{$schedule['id']}}" data-vesselID="{{$schedule->vessel['id']}}" data-userID="{{$schedule['user_id']}}" data-priceID="{{$schedule->route->price}}" onclick="createBooking(this)" class="schBook btnTest btn-primaryTest rippleTest">Book</button>
                                    @else
                                        <button id="bookingBtn" data-routeID="{{$schedule->route->id}}" data-scheduleID="{{$schedule['id']}}" data-userID="{{$schedule['user_id']}}" data-priceID="{{$schedule->route->price}}" onclick="createBooking(this)" class="schBook btnTest btn-primaryTest rippleTest">Book</button>
                                    @endif 
                                </td>
                                <td>
                                    <div class="datatableActionButton">
                                        <a href="/admin/schedule/{{ $schedule['id'] }}" data-toggle="tooltip" data-placement="auto" title="Show"><i class="far fa-eye"></i></a>
                                        <a href="/admin/schedule/{{ $schedule['id'] }}/edit" data-toggle="tooltip" data-placement="auto" title="Edit"><i class="far fa-edit"></i></a>
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
        <div class="modal-content-booking" id="bookingModalContent">
          <div class="modal-header-booking">
            <span class="close-booking">&times;</span>
            <h2>Create Booking</h2>
          </div>
          <div class="modal-body-booking">
            <form method="POST" action="/admin/booking">
                {{ csrf_field() }}
                <div class="innerContainer">
                    <div class="iconBeside">
                        <i class="fas fa-route" data-toggle="tooltip" data-placement="auto" title="Route name"></i>
                        <div id="routeCode" data-toggle="tooltip" data-placement="auto" title="Route name"></div>
                        <div id="routeID" data-toggle="tooltip" data-placement="auto" title="Route ID"></div>
                    </div>
                    <div class="iconBeside" data-toggle="tooltip" data-placement="auto" title="Duration">
                        <i class="far fa-hourglass"></i>
                        <div id="duration"></div>
                    </div>
                </div>
                
                <!-- hidden -->
                <div class="form-group">
                    <input type="hidden" id="vessel_id" name="vessel_id">
                </div>
                <div class="form-group">
                    <input type="hidden" id="schedule_id" name="schedule_id">
                </div>   
                
                <div class="iconBeside-vessel" data-toggle="tooltip" data-placement="auto" title="Vessel Name">
                    <i class="fas fa-ship"></i>
                    <div id="vesselName">N/A</div>
                </div>
                <div class="innerContainer">
                    <div class="innerContainerRow">
                        <div class="iconBeside-vessel" data-toggle="tooltip" data-placement="auto" title="Vessel Type">
                            <i class="fas fa-ship"></i>
                            <div id="vesselType">N/A</div>
                        </div>
                        <div class="iconBeside-vessel" data-toggle="tooltip" data-placement="auto" title="Seat Capacity">
                            <i class="fas fa-chair"></i>
                            <div id="seat">N/A</div>
                        </div>
                    </div>
                </div>

                <div class="innerContainer-textInitial">
                    <label for="user_id">Select User</label>
                    <select class="search-single custom-select" name="user_id" id="user_id" required>
                        <optgroup label="User ID">
                            @if (!$users->isEmpty() && $users != null )<!--If users are not empty and null -->
                                @foreach ($users as $user)
                                    <option value="{{ $user['id'] }}">{{ $user['id'] }} | {{ $user['first_name'] }} {{ $user['last_name'] }}</option>
                                @endforeach
                            @endif
                        </optgroup>
                    </select>
                </div>
                <div class="listContainer">
                    <div class="totalAtSide">
                        <div class="verticalDiv">
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
                            <div class="ticketQuantity">
                                <label for="ticket_quantity">Ticket Quantity</label>
                                <input type="number" oninput="calcTotal()" min="1" max="100" name="ticket_quantity" id="ticketQty" data-field="qty" class="form-control" placeholder="Quantity" value="{{ old('ticket_quantity') }}" required>
                            </div>
                        </div>
                        <div class="totalAmt">
                            <div class="totalTxt">Total:</div>
                            <div id="totalAmount"></div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer-booking">
                    <input class="btn btn-primary" type="submit" value="Submit">
                </div>
            </form>
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
    
    @section('js_schedule_booking_pop')
        <script defer>
            var schedules = {!! json_encode($schedules->toArray(), JSON_HEX_TAG) !!};
            var users = {!! json_encode($users->toArray(), JSON_HEX_TAG) !!};
            var vessels = {!! json_encode($vessels->toArray(), JSON_HEX_TAG) !!};
            var scheduleId;

            function createBooking(d){
                //get id
                scheduleId = d.getAttribute('data-scheduleID');
                let vesselId = d.getAttribute('data-vesselID');
                let userId = d.getAttribute('data-userID');
                let priceId = d.getAttribute('data-priceID');
                //let routeCode = d.getAttribute('data-routeCode');
                let routeID = d.getAttribute('data-routeID');
                //insert it into field
                document.getElementById('schedule_id').value = scheduleId;
                //document.getElementById('vesselID').innerHTML = vesselId;
                document.getElementById('vessel_id').value = vesselId;
                document.getElementById('user_id').value = userId;
                //document.getElementById('totalAmount').innerHTML = priceId;
                //document.getElementById('routeCode').innerHTML = routeCode;
                
                routeInfo(scheduleId);
                vesselInfo(vesselId);

                //search2
                $(document).ready(function() {
                    $('.search-single').select2({
                        dropdownCssClass: "increasedzindexclass",
                    });
                });
                // Get the button that opens the modal
                let btn = document.getElementById("bookingBtn");
                // Get the modal
                let modal = document.getElementById("bookingModal");
                //modal content
                let bookingModalContent = document.getElementById("bookingModalContent");

                $(modal).removeClass('swal2-backdrop-hide');
                $(bookingModalContent).removeClass('swal2-hide');
                $(modal).addClass('swal2-backdrop-show');
                $(bookingModalContent).addClass('swal2-show');

                // Get the <span> element that closes the modal
                let span = document.getElementsByClassName("close-booking")[0];

                // When the user clicks the button, open the modal 
                 modal.style.display = "block";
                
                // When the user clicks on <span> (x), close the modal
                span.onclick = function() {
                    document.getElementById('ticketQty').value = "";
                    document.getElementById("totalAmount").innerHTML = "";

                    $(modal).removeClass('swal2-backdrop-show');
                    $(bookingModalContent).removeClass('swal2-show');
                    $(modal).addClass('swal2-backdrop-hide');
                    $(bookingModalContent).addClass('swal2-hide');
                    window.setTimeout(function(){
                        modal.style.display = 'none';
                    },700);
                }

                // When the user clicks anywhere outside of the modal, close it
                window.onclick = function(event) {
                    if (event.target == modal) {
                        document.getElementById('ticketQty').value = "";
                        document.getElementById("totalAmount").innerHTML = "";

                        $(modal).removeClass('swal2-backdrop-show');
                        $(bookingModalContent).removeClass('swal2-show');
                        $(modal).addClass('swal2-backdrop-hide');
                        $(bookingModalContent).addClass('swal2-hide');
                        window.setTimeout(function(){
                            modal.style.display = 'none';
                        },700);
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
            function routeInfo(id){
                
                let scheduleId = id;
                //find id and return route
                let schedule = searchID(scheduleId);
                
                //insert into field
                let giveRouteCode = schedule.route.route_code;
                let giveRouteID = schedule.route.id;
                let giveDuration = schedule.route.duration;

                document.getElementById('routeID').innerHTML = "("+giveRouteID+")";
                document.getElementById('routeCode').innerHTML = giveRouteCode;
                document.getElementById('duration').innerHTML = giveDuration;

                //search function
                function searchID(scheduleId){
                    for (let i=0; i < schedules.length; i++) {
                        if (schedules[i].id == scheduleId) {
                            return schedules[i];
                        }
                    }
                    alert("Error. Could not find schedule ID");
                }
            }

            function vesselInfo(id){
                let vesselID = id;
                let vessel = searchID(vesselID);
                
                //insert into field
                let giveVesselType = vessel.vessel_type_id;
                if(giveVesselType == 1){
                    giveVesselType = "Speed Boat"
                }else if(giveVesselType == 2){
                    giveVesselType = "Ferry"
                }
                let giveSeat = vessel.seat_capacity;
                let giveVesselName = vessel.name;
                document.getElementById('vesselType').innerHTML = giveVesselType;
                document.getElementById('seat').innerHTML = giveSeat;
                document.getElementById('vesselName').innerHTML = giveVesselName;


                //search function
                function searchID(vesselID){
                    for (let i=0; i < vessels.length; i++) {
                        if (vessels[i].id == vesselID) {
                            return vessels[i];
                        }
                    }
                    alert("Error. Could not find vessel ID");
                }
            }
        </script>
    @endsection
    @section('js_ajax')
        <script>
            ///////////////NOT USING AJAX FUNCTION NOW///////////////////////
            function findTotal(){
                var ticketTypeBox = $('.ticketType');//initializing
                var ticketTypeSelectList = $('.ticketTypeSelectList');
                ticketTypeBox.hide();
                ticketTypeSelectList.empty();

                var routeCodeID = document.getElementById('routeCode').innerHTML;//getting routeCode from id
                $.ajax({
                    type:'get',
                    url:"/admin/schedule/create",//create method in schedule controller
                    data:{//data we are sending
                        routeCodeID:routeCodeID
                    },
                    success: function(data) {
                        ticketTypeBox.show();//show ticket type box
                        $.each(data, function(index, element){//getting each data. index and element are the parameters
                            $(ticketTypeSelectList).append(//appending it to ticket type select list
                                '<option value="'+ element.ticket_type_id +'"data-ticket_type_price="'+ element.price +'">'+ element.ticket_type_id +' | '+ element.ticket_type.name +'</option>'
                            );
                        });
                    },
                    error: function (err) {
                        alert("error"); 
                    }
                });
            }
            
        </script>
    @endsection
    @section('js_delete_schedule')
        <script defer>
                function deleteSchedule(e){

                    let id = e.getAttribute('data-scheduleID');
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
                                url:'{{url("/admin/schedule")}}/' +id,
                                data:{
                                    "_token": "{{ csrf_token() }}",
                                },
                                success: function(response) {
                                    Swal.fire(
                                        'Deleted!',
                                        'Schedule has been deleted.',
                                        'success'
                                    )
                                    location.reload(true);
                                },
                                failure: function (response) {
                                    Swal.fire(
                                        'Internal Error',
                                        'Schedule was not deleted.',
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