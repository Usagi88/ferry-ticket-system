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

    @section('js_agent_island_create_alert')
        <script>
            @if(!empty(Session::get('success')))
                var popupId = "{{ uniqid() }}";
                if(!sessionStorage.getItem('shown-' + popupId)) {
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: 'Agent Island Assign has been created',
                        showConfirmButton: false,
                        timer: 1200
                    })
                }
                sessionStorage.setItem('shown-' + popupId, '1');
            @endif
        </script>
    @endsection

    @section('js_agent_island_edit_alert')
        <script>
            @if(!empty(Session::get('Edit-success')))
                var popupId = "{{ uniqid() }}";
                if(!sessionStorage.getItem('shown-' + popupId)) {
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: 'Agent Island Assign has been edited',
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
                    <i class="i-Add-User"></i>
                    <div class="content">
                        <p class="text-muted mt-2 mb-0">Assigned Agents</p>
                        <p class="lead text-primary-black text-24 mb-2">{{$assignCount}}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="tableLabel">Assigned Agents to Island</div>
        <div class="tableCreateBtn">
            <a href="/admin/island/agent-island/create" class="btnTest btn-primaryTest rippleTest" role="button" aria-pressed="true">Assign Agent</a>
        </div>
        
        <div class="gridDataTable">
            <div class="table dataTable hover">
                <table class="myTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Agent ID</th>
                            <th>Name</th>
                            <th>Island ID</th>
                            <th>Atoll</th>
                            <th>Island</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($assigns as $assign)
                            @if (!$assign->islandsAssignedToUser->isEmpty() && $assign->islandsAssignedToUser != null )<!--If islandsAssignedToUser are not empty and null -->
                                @foreach ($assign->islandsAssignedToUser as $island)
                                    <tr>
                                        <td>{{ $island->pivot['id'] }}</td>
                                        <td>{{ $assign['id'] }}</td>
                                        <td>{{ $assign['first_name'] }} {{ $assign['last_name'] }}</td>
                                        <td>{{ $island['id'] }}</td>
                                        <td>{{ $island['atoll'] }}</td>
                                        <td>{{ $island['name'] }}</td>
                                        <td>
                                            <div class="datatableActionButton">
                                                <a href="/admin/island/agent-island/{{ $island->pivot['id'] }}" data-toggle="tooltip" data-placement="auto" title="Show"><i class="far fa-eye"></i></a>
                                                @can('assignIslandEdit', $island)
                                                    <a href="/admin/island/agent-island/{{ $island->pivot['id'] }}/edit" data-toggle="tooltip" data-placement="auto" title="Edit"><i class="far fa-edit"></i></a>
                                                @endcan
                                                <a href="javascript:void(0)" data-assignID="{{$island->pivot['id']}}" onclick="deleteAssign(this)" data-toggle="tooltip" data-placement="auto" title="Delete"><i class="far fa-trash-alt"></i></a>
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

    @section('js_delete_agent_island')
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
                                url:'{{url("/admin/island/agent-island")}}/' +id,
                                data:{
                                    "_token": "{{ csrf_token() }}",
                                },
                                success: function(response) {
                                    Swal.fire(
                                        'Deleted!',
                                        'Assigned Agent Island has been deleted.',
                                        'success'
                                    )
                                    location.reload(true);
                                },
                                failure: function (response) {
                                    Swal.fire(
                                        'Internal Error',
                                        'Assigned Agent Island was not deleted.',
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