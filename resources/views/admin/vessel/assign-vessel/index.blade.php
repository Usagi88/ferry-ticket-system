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
    
    @section('js_assign_vessel_create_alert')
        <script>
            @if(!empty(Session::get('success')))
                var popupId = "{{ uniqid() }}";
                if(!sessionStorage.getItem('shown-' + popupId)) {
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: 'Vessel Assign has been created',
                        showConfirmButton: false,
                        timer: 1200
                    })
                }
                sessionStorage.setItem('shown-' + popupId, '1');
            @endif
        </script>
    @endsection

    @section('js_assign_vessel_edit_alert')
        <script>
            @if(!empty(Session::get('Edit-success')))
                var popupId = "{{ uniqid() }}";
                if(!sessionStorage.getItem('shown-' + popupId)) {
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: 'Vessel Assign has been edited',
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
                        <p class="text-muted mt-2 mb-0">Assigned Vessels</p>
                        <p class="lead text-primary-black text-24 mb-2">{{$assignCount}}</p>
                    </div>
                </div>
            </div>
        </div>
        

        <div class="tableLabel">Assigned Vessels</div>
        @can('viewAny', App\Models\Vessel::class)
            <div class="tableCreateBtn">
                <a href="/admin/vessel/assign-vessel/create" class="btnTest btn-primaryTest rippleTest" role="button" aria-pressed="true">Assign Vessel</a>
            </div>
        @endcan
        <div class="gridDataTable">
            <div class="table dataTable hover">
                <table class="myTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Owner ID</th>
                            <th>Agent ID</th>
                            <th>Name</th>
                            <th>Vessel ID</th>
                            <th>Vessel Name</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($assigns as $assign)
                            @if (!$assign->vesselsAssignedToUser->isEmpty() && $assign->vesselsAssignedToUser != null )<!--If vesselsAssignedToUser are not empty and null -->
                                @foreach ($assign->vesselsAssignedToUser as $vessel)
                                    <tr>
                                        <td>{{ $vessel->pivot['id'] }}</td>
                                        <td>{{ $vessel['owner_id'] }}</td>
                                        <td>{{ $assign['id'] }}</td>
                                        <td>{{ $assign['first_name'] }} {{ $assign['last_name'] }}</td>
                                        <td>{{ $vessel['id'] }}</td>
                                        <td>{{ $vessel['name'] }}</td>
                                        <td>
                                            <div class="datatableActionButton">
                                                <a href="/admin/vessel/assign-vessel/{{ $vessel->pivot['id'] }}" data-toggle="tooltip" data-placement="auto" title="Show"><i class="far fa-eye"></i></a>
                                                @can('assignEdit', $vessel)
                                                    <a href="/admin/vessel/assign-vessel/{{ $vessel->pivot['id'] }}/edit" data-toggle="tooltip" data-placement="auto" title="Edit"><i class="far fa-edit"></i></a>
                                                @endcan
                                                <a href="javascript:void(0)" data-assignID="{{$vessel->pivot['id']}}" data-toggle="tooltip" data-placement="auto" title="Delete" onclick="deleteAssign(this)"><i class="far fa-trash-alt"></i></a>
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
    
    @section('js_delete_assign_vessel')
        <script defer>
                function deleteAssign(e){

                    let id = e.getAttribute('data-assignID');
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
                                url:'{{url("/admin/vessel/assign-vessel")}}/' +id,
                                data:{
                                    "_token": "{{ csrf_token() }}",
                                },
                                success: function(response) {
                                    Swal.fire(
                                        'Deleted!',
                                        'Assigned Vessel has been deleted.',
                                        'success'
                                    )
                                    location.reload(true);
                                },
                                failure: function (response) {
                                    Swal.fire(
                                        'Internal Error',
                                        'Assigned Vessel was not deleted.',
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