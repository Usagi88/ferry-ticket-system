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

    @section('js_route_create_alert')
        <script>
            @if(!empty(Session::get('success')))
                var popupId = "{{ uniqid() }}";
                if(!sessionStorage.getItem('shown-' + popupId)) {
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: 'Route has been created',
                        showConfirmButton: false,
                        timer: 1200
                    })
                }
                sessionStorage.setItem('shown-' + popupId, '1');
            @endif
        </script>
    @endsection

    @section('js_route_edit_alert')
        <script>
            @if(!empty(Session::get('Edit-success')))
                var popupId = "{{ uniqid() }}";
                if(!sessionStorage.getItem('shown-' + popupId)) {
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: 'Route has been edited',
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
                    <i class="i-Add-Route"></i>
                    <div class="content">
                        <p class="text-muted mt-2 mb-0">Routes</p>
                        <p class="lead text-primary-black text-24 mb-2">{{$routes->count()}}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="tableLabel">Routes</div>
        <div class="tableCreateBtn">
            <a href="/admin/route/create" class="btnTest btn-primaryTest rippleTest" role="button" aria-pressed="true">Create Route</a>
        </div>
        
        <div class="gridDataTable">
            <div class="table dataTable hover">
                <table class="myTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User ID</th>
                            <th>Route Name</th>
                            <th style="width: 100px">Schedule</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($routes as $route)
                            <tr>
                                <td>{{ $route['id'] }}</td>
                                <td>{{ $route['user_id'] }}</td>
                                <td>{{ $route['route_name'] }}</td>
                                <td> 
                                    <button id="routeBtn" data-userID="{{$route['user_id']}}" data-routeID="{{$route['id']}}" onclick="createSchedule(this)" class="rteSchedule btnTest btn-primaryTest rippleTest">Schedule</button>
                                </td>
                                <td>
                                    <div class="datatableActionButton">
                                        <a href="/admin/route/{{ $route['id'] }}" data-toggle="tooltip" data-placement="auto" title="Show"><i class="far fa-eye"></i></a>
                                        <a href="/admin/route/{{ $route['id'] }}/edit" data-toggle="tooltip" data-placement="auto" title="Edit"><i class="far fa-edit"></i></a>
                                        <a href="javascript:void(0)" data-routeID="{{$route['id']}}" onclick="deleteRoute(this)" data-toggle="tooltip" data-placement="auto" title="Delete"><i class="far fa-trash-alt"></i></a>
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

    <div id="scheduleModal" class="modalSchedule">

        <!-- Modal content -->
        <div class="modal-content-schedule" id="scheduleModalContent">
          <div class="modal-header-schedule">
            <span class="close-schedule">&times;</span>
            <h2>Create Schedule</h2>
          </div>
          <div class="modal-body-schedule">
            <form method="POST" action="/admin/schedule">
                {{ csrf_field() }}

                <div class="innerContainer">
                    <div class="iconBeside">
                        <i class="fas fa-route" data-toggle="tooltip" data-placement="auto" title="Route Code"></i>
                        <div id="routeCode" data-toggle="tooltip" data-placement="auto" title="Route Code"></div>
                        <div id="routeID" data-toggle="tooltip" data-placement="auto" title="Route ID"></div>
                    </div>
                    <div class="iconBeside" data-toggle="tooltip" data-placement="auto" title="Duration">
                        <i class="far fa-hourglass"></i>
                        <div id="duration"></div>
                    </div>
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
                <div class="userContainer">
                    <div class="iconBeside-user">
                        <i class="far fa-user" data-toggle="tooltip" data-placement="auto" title="User"></i>
                        <div class="verticalCol">
                            <div class="horizontalRow">
                                <div id="userFullname" data-toggle="tooltip" data-placement="auto" title="Name"></div>
                                <div id="userID" data-toggle="tooltip" data-placement="auto" title="User ID"></div>
                            </div>
                            <div id="username" data-toggle="tooltip" data-placement="auto" title="Username"></div>
                        </div>
                    </div>
                    
                </div>

                <div class="listContainer">
                    <div class="form-group">
                        <label for="vessel_id">Select Vessel</label>
                        <select class="search-single custom-select" onchange="vesselSelect();" name="vessel_id" id="vessel_id" required>
                            <optgroup label="Vessel ID">
                                @if (!$vessels->isEmpty() && $vessels != null )<!--If vessels are not empty and null -->
                                    @foreach ($vessels as $vessel)
                                        <option value="{{ $vessel['id'] }}">{{ $vessel['id'] }} | {{ $vessel['name'] }}</option>
                                    @endforeach
                                @endif
                            </optgroup>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="schedule_date">Scheduled Date</label>
                        <input type="text"  name="schedule_date" class="form-control" id="datetimepicker2" placeholder="Date" value="" required/>
                    </div>
                </div>
                
                <div class="modal-footer-schedule">
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

    @section('js_route_schedule_pop')
        <script defer>
            var users = {!! json_encode($users->toArray(), JSON_HEX_TAG) !!};
            var routes = {!! json_encode($routes->toArray(), JSON_HEX_TAG) !!};
            var vessels = {!! json_encode($vessels->toArray(), JSON_HEX_TAG) !!};
            function createSchedule(d){
                //get route id
                let id = d.getAttribute('data-routeID');
                //get user id
                let userID = d.getAttribute('data-userID');
                //insert it into field
                //document.getElementById('route_id').value = id;
                
                let giveUserID = users[userID - 1].id;
                let giveUserFullName = users[userID - 1].first_name +" "+ users[userID - 1].last_name;
                let giveUsername = users[userID - 1].username;
                document.getElementById('userID').innerHTML = "("+giveUserID+")";;
                document.getElementById('userFullname').innerHTML = giveUserFullName;
                document.getElementById('username').innerHTML = giveUsername;

                let giveRouteID = routes[id - 1].id;
                let giveRouteCode = routes[id - 1].route_code;
                let giveDuration = routes[id - 1].duration;
                document.getElementById('routeID').innerHTML = "("+giveRouteID+")";
                document.getElementById('routeCode').innerHTML = giveRouteCode;
                document.getElementById('duration').innerHTML = giveDuration;

                //datepicker
                $(function () {
                    $('#datetimepicker2').datetimepicker({
                        format: 'YYYY-MM-DD HH:mm:ss'
                    });
                });
                //search2
                $(document).ready(function() {
                    $('.search-single').select2({
                        dropdownCssClass: "increasedzindexclass",
                    });
                });
                //route btn
                let btn = document.getElementById("routeBtn");
                //modal content
                let scheduleModalContent = document.getElementById("scheduleModalContent");
                //modal
                let modal = document.getElementById("scheduleModal");

                //remove and add class
                $(modal).removeClass('swal2-backdrop-hide');
                $(scheduleModalContent).removeClass('swal2-hide');
                $(modal).addClass('swal2-backdrop-show');
                $(scheduleModalContent).addClass('swal2-show');
                
                // Get the <span> element that closes the modal
                let span = document.getElementsByClassName("close-schedule")[0];

                // When the user clicks the button, open the modal 
                 modal.style.display = "block";
                
                // When the user clicks on <span> (x), close the modal
                span.onclick = function() {
                    $(modal).removeClass('swal2-backdrop-show');
                    $(scheduleModalContent).removeClass('swal2-show');
                    $(modal).addClass('swal2-backdrop-hide');
                    $(scheduleModalContent).addClass('swal2-hide');
                    window.setTimeout(function(){
                        modal.style.display = 'none';
                    },700);
                }

                // When the user clicks anywhere outside of the modal, close it
                window.onclick = function(event) {
                    if (event.target == modal) {
                        $(modal).removeClass('swal2-backdrop-show');
                        $(scheduleModalContent).removeClass('swal2-show');
                        $(modal).addClass('swal2-backdrop-hide');
                        $(scheduleModalContent).addClass('swal2-hide');
                        window.setTimeout(function(){
                            modal.style.display = 'none';
                        },700);
                    }
                }
            }

            function vesselSelect(d){
                //get select
                let vesselSelectID = document.getElementById("vessel_id");
                //get select value
                let vessel_id = vesselSelectID[vesselSelectID.selectedIndex].value;
                //find id and return vessel
                let vessel = searchID(vessel_id);
                
                //insert into field
                let giveVesselType = vessel.vessel_type_id;
                if(giveVesselType == 1){
                    giveVesselType = "Speed Boat"
                }else if(giveVesselType == 2){
                    giveVesselType = "Ferry"
                }
                let giveSeat = vessel.seat_capacity;
                document.getElementById('vesselType').innerHTML = giveVesselType;
                document.getElementById('seat').innerHTML = giveSeat;


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
    @section('js_delete_route')
        <script defer>
                function deleteRoute(e){

                    let id = e.getAttribute('data-routeID');
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
                                url:'{{url("/admin/route")}}/' +id,
                                data:{
                                    "_token": "{{ csrf_token() }}",
                                },
                                success: function(response) {
                                    Swal.fire(
                                        'Deleted!',
                                        'Route has been deleted.',
                                        'success'
                                    )
                                    location.reload(true);
                                },
                                failure: function (response) {
                                    Swal.fire(
                                        'Internal Error',
                                        'Route was not deleted.',
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