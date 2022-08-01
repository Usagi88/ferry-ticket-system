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
    
    @section('js_my_route_create_alert')
        <script>
            @if(!empty(Session::get('success')))
                var popupId = "{{ uniqid() }}";
                if(!sessionStorage.getItem('shown-' + popupId)) {
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: 'Your Route has been created',
                        showConfirmButton: false,
                        timer: 1200
                    })
                }
                sessionStorage.setItem('shown-' + popupId, '1');
            @endif
        </script>
    @endsection

    @section('js_my_route_edit_alert')
        <script>
            @if(!empty(Session::get('Edit-success')))
                var popupId = "{{ uniqid() }}";
                if(!sessionStorage.getItem('shown-' + popupId)) {
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: 'Your Route has been edited',
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
                        <p class="lead text-primary-black text-24 mb-2">{{ count($user->routes) }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="tableLabel">Routes</div>
        <div class="tableCreateBtn">
            <a href="/admin/user/{{$user->id}}/profile/my-route/create" class="btnTest btn-primaryTest rippleTest" role="button" aria-pressed="true">Create Route</a>
        </div>
        
        <div class="gridDataTable">
            <div class="table dataTable hover">
                <table class="myTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User ID</th>
                            <th>Origin</th>
                            <th>Destination</th>
                            <th>Route Code</th>
                            <th>Duration</th>
                            <th>Schedule</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($routes as $route)
                            <tr>
                                <td>{{ $route['id'] }}</td>
                                <td>{{ $route['user_id'] }}</td>
                                <td>{{ $route['origin'] }}</td>
                                <td>{{ $route['destination'] }}</td>
                                <td>{{ $route['route_code'] }}</td>
                                <td>{{ $route['duration'] }}</td>
                                <td> 
                                    <button id="routeBtn" data-routeID="{{$route['id']}}" onclick="createSchedule(this)" class="rteSchedule btnTest btn-primaryTest rippleTest">Schedule</button>
                                </td>
                                <td>
                                    <div class="datatableActionButton">
                                        <a href="/admin/user/{{$user->id}}/profile/my-route/{{ $route['id'] }}" data-toggle="tooltip" data-placement="auto" title="Show"><i class="fa fa-eye"></i></a>
                                        <a href="/admin/user/{{$user->id}}/profile/my-route/{{ $route['id'] }}/edit" data-toggle="tooltip" data-placement="auto" title="Edit"><i class="fa fa-edit"></i></a>
                                        <a href="javascript:void(0)" data-userID="{{$user['id']}}" data-routeID="{{$route['id']}}" onclick="deleteMyRoute(this)" data-toggle="tooltip" data-placement="auto" title="Delete"><i class="fas fa-trash-alt"></i></a>
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

    <div id="myModal" class="modalSchedule">

        <!-- Modal content -->
        <div class="modal-content-schedule">
          <div class="modal-header-schedule">
            <span class="close-schedule">&times;</span>
            <h2>Create Schedule</h2>
          </div>
          <div class="modal-body-schedule">
            <form method="POST" action="/admin/user/{{$user->id}}/profile/my-schedule">
                {{ csrf_field() }}
                <div class="form-group"><!-- In here we later put an if condition to show merchant's vessels if user role is merchant-->
                    <label for="vessel_id">Select Vessel</label>
                    <select class="search-single custom-select" name="vessel_id" id="vessel_id" required>
                        <optgroup label="Vessel ID">
                            @foreach ($users as $user)
                                @if (!$user->vesselsAssignedToUser->isEmpty() && $user->vesselsAssignedToUser != null )<!--If assigned vessels are not empty and null -->
                                    @foreach($user->vesselsAssignedToUser as $vessel)
                                        <option value="{{ $vessel['id'] }}">{{ $vessel['id'] }} | {{ $vessel['name'] }}</option>
                                    @endforeach
                                @endif    
                            @endforeach
                        </optgroup>
                    </select>
                </div>
                <div class="form-group">
                    <label for="route_id">Select Route</label>
                    <select class="search-single custom-select" name="route_id" id="route_id" required>
                        <optgroup label="Route ID">
                            @if (!$routes->isEmpty() && $routes != null )<!--If routes are not empty and null -->
                                @foreach ($routes as $route)
                                    <option value="{{ $route['id'] }}">{{ $route['id'] }} | {{ $route['route_code'] }}</option>
                                @endforeach
                            @endif
                        </optgroup>
                    </select>
                </div>    
                <div class="form-group">
                    <label for="schedule_date">Scheduled Date</label>
                    <input type="text"  name="schedule_date" class="form-control" id="datetimepicker2" placeholder="Date" value="" required/>
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
    
    @section('js_my_route_schedule_pop')
        <script defer>
            function createSchedule(d){
                //get route id
                let id = d.getAttribute('data-routeID');

                //insert it into field
                document.getElementById('route_id').value = id;
                
                //datepicker
                $(function () {
                    $('#datetimepicker2').datetimepicker({
                        format: 'YYYY-MM-DD HH:mm:ss'
                    });
                });
                //search2
                $(document).ready(function() {
                    $('.search-single').select2();
                });

                // Get the modal
                let modal = document.getElementById("myModal");

                // Get the button that opens the modal
                let btn = document.getElementById("myBtn");

                // Get the <span> element that closes the modal
                let span = document.getElementsByClassName("close-schedule")[0];

                // When the user clicks the button, open the modal 
                 modal.style.display = "block";
                

                // When the user clicks on <span> (x), close the modal
                span.onclick = function() {
                    modal.style.display = "none";
                }

                // When the user clicks anywhere outside of the modal, close it
                window.onclick = function(event) {
                    if (event.target == modal) {
                        modal.style.display = "none";
                    }
                }
            }
        </script>
    @endsection
    @section('js_delete_my_route')
        <script defer>
                function deleteMyRoute(e){

                    let id = e.getAttribute('data-routeID');
                    let userID = e.getAttribute('data-userID');
                    console.log(userID);
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
                                //url:'{{url("/admin/route")}}/' +id,
                                url:"/admin/user/" + userID +"/profile/my-route/"+id,
                                data:{
                                    "_token": "{{ csrf_token() }}",
                                },
                                success: function(response) {
                                    Swal.fire(
                                        'Deleted!',
                                        'Your Route has been deleted.',
                                        'success'
                                    )
                                    location.reload(true);
                                },
                                failure: function (response) {
                                    Swal.fire(
                                        'Internal Error',
                                        'Your Route was not deleted.',
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