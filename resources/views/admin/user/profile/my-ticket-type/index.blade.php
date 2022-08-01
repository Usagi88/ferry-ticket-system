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

    @section('js_ticket_type_create_alert')
        <script>
            @if(!empty(Session::get('success')))
                var popupId = "{{ uniqid() }}";
                if(!sessionStorage.getItem('shown-' + popupId)) {
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: 'Ticket Type has been created',
                        showConfirmButton: false,
                        timer: 1200
                    })
                }
                sessionStorage.setItem('shown-' + popupId, '1');
            @endif
        </script>
    @endsection

    @section('js_ticket_type_edit_alert')
        <script>
            @if(!empty(Session::get('Edit-success')))
                var popupId = "{{ uniqid() }}";
                if(!sessionStorage.getItem('shown-' + popupId)) {
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: 'Ticket Type has been edited',
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
                    <i class="i-Add-TicketType"></i>
                    <div class="content">
                        <p class="text-muted mt-2 mb-0">Ticket Types</p>
                        <p class="lead text-primary-black text-24 mb-2">{{$user->ticket_types->count()}}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="tableLabel">My Ticket Types</div>
        <div class="tableCreateBtn">
            <div class="createBtnWidth">
                <a href="/admin/user/{{$user->id}}/profile/my-ticket-type/create" class="btnTest btn-primaryTest rippleTest" role="button" aria-pressed="true">Create Ticket Type</a>
            </div>
        </div>
        
        <div class="gridDataTable">
            <div class="table dataTable hover">
                <table class="myTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (!$user->ticket_types->isEmpty() && $user->ticket_types != null )
                            @foreach ($userT->ticket_types as $ticket_type)
                                <tr>
                                    <td>{{ $ticket_type['id'] }}</td>
                                    <td>{{ $ticket_type['name'] }}</td>
                                    <td>{{ $ticket_type['description'] }}</td>
                                    <td>
                                        <div class="datatableActionButton">
                                            <a href="/admin/user/{{$user->id}}/profile/my-ticket-type/{{ $ticket_type['id'] }}" data-toggle="tooltip" data-placement="auto" title="Show"><i class="far fa-eye"></i></a>
                                            <a href="/admin/user/{{$user->id}}/profile/my-ticket-type/{{ $ticket_type['id'] }}/edit" data-toggle="tooltip" data-placement="auto" title="Edit"><i class="far fa-edit"></i></a>
                                            <a href="javascript:void(0)" data-userID="{{$user['id']}}" data-ticket_typeID="{{$ticket_type['id']}}" onclick="deleteTicketType(this)" data-toggle="tooltip" data-placement="auto" title="Delete"><i class="far fa-trash-alt"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
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

    @section('js_delete_ticket_type')
        <script defer>
                function deleteTicketType(e){

                    let id = e.getAttribute('data-ticket_typeID');
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
                                url:"/admin/user/" + userID +"/profile/my-ticket-type/"+id,
                                data:{
                                    "_token": "{{ csrf_token() }}",
                                },
                                success: function(response) {
                                    Swal.fire(
                                        'Deleted!',
                                        'Ticket type has been deleted.',
                                        'success'
                                    )
                                    location.reload(true);
                                },
                                failure: function (response) {
                                    Swal.fire(
                                        'Internal Error',
                                        'Ticket type was not deleted.',
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