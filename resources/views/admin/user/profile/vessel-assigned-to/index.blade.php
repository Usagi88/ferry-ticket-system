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
    
    @section('js_my_assign_vessel_create_alert')
        <script>
            @if(!empty(Session::get('success')))
                var popupId = "{{ uniqid() }}";
                if(!sessionStorage.getItem('shown-' + popupId)) {
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: 'Your Vessel Assign has been created',
                        showConfirmButton: false,
                        timer: 1200
                    })
                }
                sessionStorage.setItem('shown-' + popupId, '1');
            @endif
        </script>
    @endsection

    @section('js_my_assign_vessel_edit_alert')
        <script>
            @if(!empty(Session::get('Edit-success')))
                var popupId = "{{ uniqid() }}";
                if(!sessionStorage.getItem('shown-' + popupId)) {
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: 'Your Vessel Asign has been edited',
                        showConfirmButton: false,
                        timer: 1200
                    })
                }
                sessionStorage.setItem('shown-' + popupId, '1');
            @endif
        </script>
    @endsection
    
    <div class="assignVesselIndexGridContainer">

        <div class="cardGridOne">
            <div class="cardNew card-icon-bg card-icon-bg-primary o-hidden mb-4">
                <div class="card-body text-center">
                    <i class="i-Add-Vessel"></i>
                    <div class="content">
                        <p class="text-muted mt-2 mb-0">Assigns</p>
                        <p class="lead text-primary-black text-24 mb-2">{{ $user->vesselsAssignedToUser->count() }}</p>
                    </div>
                </div>
            </div>
        </div>
        

        <div class="tableLabel">Vessels I'm assigned to</div>
        @can('create', App\Models\Vessel::class)
            <div class="tableCreateBtn">
                <a href="/admin/user/{{$user->id}}/profile/vessel-assigned-to/create" class="btnTest btn-primaryTest rippleTest" role="button" aria-pressed="true">New Assign</a>
            </div>
        @endcan
        <div class="gridDataTable">
            <div class="table dataTable hover">
                <table class="myTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Agent ID</th>
                            <th>Name</th>
                            <th>Vessel ID</th>
                            <th>Vessel Name</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($myAssigns as $myAssign)
                            @if (!$myAssign->vesselsAssignedToUser->isEmpty() && $myAssign->vesselsAssignedToUser != null )
                                @foreach ($myAssign->vesselsAssignedToUser as $vessel)
                                    <tr>
                                        <td>{{ $vessel->pivot->id }}</td>
                                        <td>{{ $myAssign['id']}}</td>
                                        <td>{{ $myAssign['first_name']}} {{ $myAssign['last_name'] }}</td>
                                        <td>{{ $vessel['id'] }}</td>
                                        <td>{{ $vessel['name'] }}</td>
                                        <td>
                                            <div class="datatableActionButton">
                                                <a href="/admin/user/{{$user->id}}/profile/vessel-assigned-to/{{ $vessel->pivot['id'] }}" data-toggle="tooltip" data-placement="auto" title="Show"><i class="far fa-eye"></i></a>
                                                @can('create', App\Models\Vessel::class)
                                                    <a href="/admin/user/{{$user->id}}/profile/vessel-assigned-to/{{ $vessel->pivot['id'] }}/edit" data-toggle="tooltip" data-placement="auto" title="Edit"><i class="far fa-edit"></i></a>
                                                @endcan
                                                <a href="javascript:void(0)" data-userID="{{$user->id}}" data-myAssignID="{{$vessel->pivot['id']}}" onclick="deleteMyAssign(this)" data-toggle="tooltip" data-placement="auto" title="Delete"><i class="far fa-trash-alt"></i></a>
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
    
    @section('js_my_delete_my_assign_vessel')
        <script defer>
                function deleteMyAssign(e){

                    let id = e.getAttribute('data-myAssignID');
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
                                //url:'{{url("/admin/vessel")}}/' +id,
                                url:"/admin/user/" + userID +"/profile/vessel-assigned-to/"+id,
                                data:{
                                    "_token": "{{ csrf_token() }}",
                                },
                                success: function(response) {
                                    Swal.fire(
                                        'Deleted!',
                                        'The Vessel Assigned to you has been deleted.',
                                        'success'
                                    )
                                    location.reload(true);
                                },
                                failure: function (response) {
                                    Swal.fire(
                                        'Internal Error',
                                        'The Vessel Assigned to you was not deleted.',
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