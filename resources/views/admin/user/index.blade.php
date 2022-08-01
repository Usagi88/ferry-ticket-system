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
    
    @section('js_user_create_alert')
        <script>
            @if(!empty(Session::get('success')))
                var popupId = "{{ uniqid() }}";
                if(!sessionStorage.getItem('shown-' + popupId)) {
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: 'User has been created',
                        showConfirmButton: false,
                        timer: 1200
                    })
                }
                sessionStorage.setItem('shown-' + popupId, '1');
            @endif
        </script>
    @endsection

    @section('js_user_edit_alert')
        <script>
            @if(!empty(Session::get('Edit-success')))
                var popupId = "{{ uniqid() }}";
                if(!sessionStorage.getItem('shown-' + popupId)) {
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: 'User has been edited',
                        showConfirmButton: false,
                        timer: 1200
                    })
                }
                sessionStorage.setItem('shown-' + popupId, '1');
            @endif
        </script>
    @endsection
    
    <div class="userIndexGridContainer">
        
        <div class="cardGridOne">
            <div class="cardNew card-icon-bg card-icon-bg-primary o-hidden mb-4">
                <div class="card-body text-center">
                    <i class="i-Add-User"></i>
                    <div class="content">
                        <p class="text-muted mt-2 mb-0">Users</p>
                        <p class="lead text-primary-black text-24 mb-2">205</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="cardGridTwo">
            <div class="cardPieChart">

                <!--<div class="chartjs-size-monitor" style="position: absolute; left: 0px; top: 0px; right: 0px; bottom: 0px; overflow: hidden; pointer-events: none; visibility: hidden; z-index: -1;">
                    
                </div> 
                <canvas id="chart-line" width="199" height="40" class="chartjs-render-monitor" style="display: block; width: 299px; height: 200px;"></canvas>-->
                <canvas id="chart-line" width="280" height="150"></canvas>
            </div>
        </div>
            
            
        
        
        <div class="tableLabel">Users</div>
        <div class="tableCreateBtn">
            <a href="/admin/user/create" class="btnTest btn-primaryTest rippleTest" role="button" aria-pressed="true">Create User</a>
        </div>
        <div class="gridDataTable">
            <div class="table dataTable hover">
                <table class="myTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        @if(!\Auth::user()->hasRole('admin') && $user->hasRole('admin')) 
                            @continue; 
                        @endif                 
                        <tr {{ Auth::user()->id == $user->id ? 'bgcolor=#ddd' : '' }}>
                            <td>{{ $user['id'] }}</td>
                            <td>{{ $user['first_name'] }} {{ $user['last_name'] }}</td>
                            <td>{{ $user['email'] }}</td>
                            <td>       
                                @if ($user->roles->isNotEmpty())         
                                    @foreach ($user->roles as $role)
                                        @if($role->name == "Admin")
                                            <div class="roleBadge roleBadge-pill roleBadge-outline-admin p-2 m-1">
                                                {{ $role->name }} 
                                            </div>
                                        @elseif($role->name == "Staff")
                                            <div class="roleBadge roleBadge-pill roleBadge-outline-staff p-2 m-1">
                                                {{ $role->name }} 
                                            </div>
                                        @elseif($role->name == "Merchant")
                                            <div class="roleBadge roleBadge-pill roleBadge-outline-merchant p-2 m-1">
                                                {{ $role->name }} 
                                            </div>
                                        @elseif($role->name == "Agent")
                                            <div class="roleBadge roleBadge-pill roleBadge-outline-agent p-2 m-1">
                                                {{ $role->name }} 
                                            </div>
                                        @endif
                                    @endforeach
                                @endif
                            </td>
                            <td>
                                <div class="datatableActionButton">
                                    <a href="/admin/user/{{ $user['id'] }}" data-toggle="tooltip" data-placement="auto" title="Show"><i class="far fa-eye"></i></a>
                                    <a href="/admin/user/{{ $user['id'] }}/edit" data-toggle="tooltip" data-placement="auto" title="Edit"><i class="far fa-edit"></i></a>
                                    <a href="javascript:void(0)" data-userID="{{$user['id']}}" onclick="deleteUser(this)" data-toggle="tooltip" data-placement="auto" title="Delete"><i class="far fa-trash-alt"></i></a>
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
    
    @section('js_tooltip')
        <script>
            //tooltip
            $(document).ready(function(){
                $('[data-toggle="tooltip"]').tooltip();
            });
        </script>
    @endsection

    @section('js_delete_user')
            <script defer>
                    function deleteUser(e){

                        let id = e.getAttribute('data-userID');
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
                                    url:'{{url("/admin/user")}}/' +id,
                                    data:{
                                        "_token": "{{ csrf_token() }}",
                                    },
                                    success: function(response) {
                                        Swal.fire(
                                            'Deleted!',
                                            'User has been deleted.',
                                            'success'
                                        )
                                        location.reload(true);
                                    },
                                    failure: function (response) {
                                        Swal.fire(
                                            'Internal Error',
                                            'User was not deleted.',
                                            'error'
                                        )
                                    }
                                });
                            }
                        })
                    }
            </script>
            <script>
                $(document).ready(function() {
                    var ctx = $("#chart-line");
                    var myLineChart = new Chart(ctx, {
                        type: 'pie',
                        data: {
                            labels: ["Admin", "Staff", "Merchant", "Agent"],
                            datasets: [{
                                data: [{{$admin}}, {{$staff}}, {{$merchant}}, {{$agent}}],
                                backgroundColor: ["rgba(255, 0, 0, 0.5)", "rgba(200, 50, 255, 0.5)", "rgba(0, 100, 255, 0.5)", "rgba(100, 255, 0, 0.5)"]
                            }]
                        },
                        options: {
                            responsive: true,
                            legend: {
                                position: 'left',
                                //display: false,
                                labels: {
                                    fontColor: "black",
                                    boxWidth: 20,
                                    padding: 20
                                }
                            }
                        }
                    });
                });
            </script>
            
    @endsection

    

@endsection