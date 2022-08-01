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

    @section('js_vessel_type_create_alert')
        <script>
            @if(!empty(Session::get('success')))
                var popupId = "{{ uniqid() }}";
                if(!sessionStorage.getItem('shown-' + popupId)) {
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: 'Vessel Type has been created',
                        showConfirmButton: false,
                        timer: 1200
                    })
                }
                sessionStorage.setItem('shown-' + popupId, '1');
            @endif
        </script>
    @endsection

    @section('js_vessel_type_edit_alert')
        <script>
            @if(!empty(Session::get('Edit-success')))
                var popupId = "{{ uniqid() }}";
                if(!sessionStorage.getItem('shown-' + popupId)) {
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: 'Vessel Type has been edited',
                        showConfirmButton: false,
                        timer: 1200
                    })
                }
                sessionStorage.setItem('shown-' + popupId, '1');
            @endif
        </script>
    @endsection

    <div class="row py-lg-2">
        <div class="col-md-6">
            <h2>This is Vessel Type List</h2>
        </div>
        <div class="col-md-6">
            <a href="/admin/vessel-type/create" class="btn btn-primary btn-lg float-md-right" role="button" aria-pressed="true">Create New Vessel Type</a>
        </div>
    </div>

    <!-- DataTables Example -->
    <div class="table table-striped table-bordered nowrap">
        <table class="myTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Tools</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($vesselTypes as $vesselType)
                    <tr>
                        <td>{{ $vesselType['id'] }}</td>
                        <td>{{ $vesselType['name'] }}</td>
                        <td>{{ $vesselType['description'] }}</td>
                        <td>
                            <a href="/admin/vessel-type/{{ $vesselType['id'] }}" data-toggle="tooltip" data-placement="auto" title="Show"><i class="fa fa-eye"></i></a>
                            <a href="/admin/vessel-type/{{ $vesselType['id'] }}/edit" data-toggle="tooltip" data-placement="auto" title="Edit"><i class="fa fa-edit"></i></a>
                            <a href="javascript:void(0)" data-vessel_typeID="{{$vesselType['id']}}" onclick="deleteVesselType(this)" data-toggle="tooltip" data-placement="auto" title="Delete"><i class="fas fa-trash-alt"></i></a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="card-footer small text-muted">Updated yesterday at 11:59 PM</div>
    </div>

    @section('js_tooltip')
        <script>
            //tooltip
            $(document).ready(function(){
                $('[data-toggle="tooltip"]').tooltip();
            });
        </script>
    @endsection
    
    @section('js_delete_vessel_type')
        <script defer>
                function deleteVesselType(e){

                    let id = e.getAttribute('data-vessel_typeID');
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
                                url:'{{url("/admin/vessel-type")}}/' +id,
                                data:{
                                    "_token": "{{ csrf_token() }}",
                                },
                                success: function(response) {
                                    Swal.fire(
                                        'Deleted!',
                                        'Vessel Type has been deleted.',
                                        'success'
                                    )
                                    location.reload(true);
                                },
                                failure: function (response) {
                                    Swal.fire(
                                        'Internal Error',
                                        'Vessel Type was not deleted.',
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