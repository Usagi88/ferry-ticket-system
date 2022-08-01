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

    @section('js_my_booking_create_alert')
        <script>
            @if(!empty(Session::get('success')))
                var popupId = "{{ uniqid() }}";
                if(!sessionStorage.getItem('shown-' + popupId)) {
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: 'Your Booking has been created',
                        showConfirmButton: false,
                        timer: 1200
                    })
                }
                sessionStorage.setItem('shown-' + popupId, '1');
            @endif
        </script>
    @endsection
    @section('js_my_booking_edit_alert')
        <script>
            @if(!empty(Session::get('Edit-success')))
                var popupId = "{{ uniqid() }}";
                if(!sessionStorage.getItem('shown-' + popupId)) {
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: 'Your Booking has been edited',
                        showConfirmButton: false,
                        timer: 1200
                    })
                }
                sessionStorage.setItem('shown-' + popupId, '1');
            @endif
        </script>
    @endsection

    <div class="bookingIndexGridContainer">
    
        <div class="cardGridOne">
            <div class="cardNew card-icon-bg card-icon-bg-primary o-hidden mb-4">
                <div class="card-body text-center">
                    <i class="i-Add-Booking"></i>
                    <div class="content">
                        <p class="text-muted mt-2 mb-0">Booking</p>
                        <p class="lead text-primary-black text-24 mb-2">{{ count($user->bookings) }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="cardGridTwo">
            <div class="ant-card ant-card-bordered">
                <div class="ant-card-body">
                    <h4 class="mb-0">Sales</h4>
                    <div class=" mt-3">
                        <div>
                            <div class="d-flex align-items-center">
                            <h1 class="totalColor mb-0 font-weight-bold">MVR 6,982</h1>
                            
                            </div>
                            <div class="text-gray-light mt-1">Total amount</div>
                        </div>
                    </div>
                </div>
                </div>
        </div>
            
        <div class="tableLabel">Bookings</div>
        <div class="tableCreateBtn">
            <a href="/admin/user/{{$user->id}}/profile/my-booking/create" class="btnTest btn-primaryTest rippleTest" role="button" aria-pressed="true">Create Booking</a>
        </div>
        <div class="gridDataTable">
            <div class="table dataTable hover">
                <table class="myTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User ID</th>
                            <th>Vessel</th>
                            <th>Schedule ID</th>
                            <th>Ticket Type</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                            <th style="width: 120px">Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($bookings as $booking)
                            @if (!$booking->schedule->route->allTicketTypeOfRoute->isEmpty() && $booking->schedule->route->allTicketTypeOfRoute != null )<!--If allTicketTypeOfRoute are not empty and null -->
                                @foreach($booking->schedule->route->allTicketTypeOfRoute->where('id',$booking->ticket_type_id) as $route)
                                    <tr>
                                        <td>{{ $booking['id'] }}</td>
                                        <td style="text-align: right">{{ $booking['user_id'] }}</td>
                                        <td>{{ $booking->vessel->name }}</td>
                                        <td style="text-align: right">{{ $booking->schedule['id'] }}</td>
                                        <td>{{ $booking->ticket_type->name }}</td>
                                        <td style="text-align: right">{{ $route->pivot->price }}</td>
                                        <td style="text-align: right">{{ $booking['ticket_quantity'] }}</td>
                                        <td class="totalColor font-weight-bold" style="text-align: right">{{ $booking['total'] }}</td>
                                        <td style="text-align: center">       
                                            @if($booking->booking_status->name == "Pending")
                                                <div class="roleBadge roleBadge-pill roleBadge-outline-pending p-2 m-1">
                                                    {{ $booking->booking_status->name }} 
                                                </div>
                                            @elseif($booking->booking_status->name == "Paid")
                                                <div class="roleBadge roleBadge-pill roleBadge-outline-paid p-2 m-1">
                                                    {{ $booking->booking_status->name }} 
                                                </div>
                                            @elseif($booking->booking_status->name == "Cancelled")
                                                <div class="roleBadge roleBadge-pill roleBadge-outline-cancelled p-2 m-1">
                                                    {{ $booking->booking_status->name }} 
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="datatableActionButton">
                                                <a href="/admin/user/{{$user->id}}/profile/my-booking/{{ $booking['id'] }}" data-toggle="tooltip" data-placement="auto" title="Show"><i class="far fa-eye"></i></a>
                                                <a href="/admin/user/{{$user->id}}/profile/my-booking/{{ $booking['id'] }}/edit" data-toggle="tooltip" data-placement="auto" title="Edit"><i class="far fa-edit"></i></a>
                                                <a href="javascript:void(0)" data-userID="{{$user['id']}}" data-bookingID="{{$booking['id']}}" onclick="deleteMyBooking(this)" data-toggle="tooltip" data-placement="auto" title="Delete"><i class="far fa-trash-alt"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        @endforeach  
                    </tbody>
                </table>
                <div class="card-footer small text-muted">Updated yesterday at 11:59 PM</div>
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
    
    @section('js_delete_my_booking')
        <script defer>
                function deleteMyBooking(e){

                    let id = e.getAttribute('data-bookingID');
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
                                //{{url("/admin/user/'+userID+'/profile/my-bookings")}}/' +id,
                                url:"/admin/user/" + userID +"/profile/my-booking/"+id,
                                data:{
                                    "_token": "{{ csrf_token() }}",
                                },
                                success: function(response) {
                                    Swal.fire(
                                        'Deleted!',
                                        'Your Booking has been deleted.',
                                        'success'
                                    )
                                    location.reload(true);
                                },
                                failure: function (response) {
                                    Swal.fire(
                                        'Internal Error',
                                        'Your Booking was not deleted.',
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