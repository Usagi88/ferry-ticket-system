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
    
    @section('js_vessel_create_alert')
        <script>
            @if(!empty(Session::get('success')))
                var popupId = "{{ uniqid() }}";
                if(!sessionStorage.getItem('shown-' + popupId)) {
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: 'Vessel has been created',
                        showConfirmButton: false,
                        timer: 1200
                    })
                }
                sessionStorage.setItem('shown-' + popupId, '1');
            @endif
        </script>
    @endsection

    @section('js_vessel_edit_alert')
        <script>
            @if(!empty(Session::get('Edit-success')))
                var popupId = "{{ uniqid() }}";
                if(!sessionStorage.getItem('shown-' + popupId)) {
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: 'Vessel has been edited',
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
                    <i class="i-Add-Vessel"></i>
                    <div class="content">
                        <p class="text-muted mt-2 mb-0">Vessels</p>
                        <p class="lead text-primary-black text-24 mb-2">{{$vessels->count()}}</p>
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

        <div class="tableLabel">Vessels</div>
        @can('create', App\Models\Vessel::class)
            <div class="tableCreateBtn">
                <a href="/admin/vessel/create" class="btnTest btn-primaryTest rippleTest" role="button" aria-pressed="true">Create Vessel</a>
            </div>
        @endcan
        <div class="gridDataTable">
            <div class="table dataTable hover">
                <table class="myTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th style="width: 100px">Owner ID</th>
                            <th style="width: 150px">Name</th>
                            <th>Type</th>
                            <th style="width: 120px">Seat Capacity</th>
                            <th>Max Accompanied Cargo</th>
                            <th>Max Unaccompanied Cargo</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($vessels as $vessel)
                            <tr>
                                <td>{{ $vessel['id'] }}</td>
                                <td>{{ $vessel['owner_id'] }}</td>
                                <td>{{ $vessel['name'] }}</td>
                                <td>{{ $vessel->vessel_type->name }}</td>
                                <td>{{ $vessel['seat_capacity'] }}</td>
                                <td>{{ $vessel['max_accompanied_cargo'] }}</td>
                                <td>{{ $vessel['max_unaccompanied_cargo'] }}</td>
                                <td>
                                    <div class="datatableActionButton">
                                        <a href="/admin/vessel/{{ $vessel['id']}}" data-toggle="tooltip" data-placement="auto" title="Show"><i class="far fa-eye"></i></a>
                                        @can('edit', $vessel)
                                            <a href="/admin/vessel/{{ $vessel['id'] }}/edit" data-toggle="tooltip" data-placement="auto" title="Edit"><i class="far fa-edit"></i></a>
                                        @endcan
                                        @can('delete', $vessel)
                                            <a href="javascript:void(0)" data-vesselID="{{$vessel['id']}}" data-toggle="tooltip" data-placement="auto" title="Delete" onclick="deleteVessel(this)"><i class="far fa-trash-alt"></i></a>
                                        @endcan
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
    
    @section('js_delete_vessel')
        <script defer>
                function deleteVessel(e){

                    let id = e.getAttribute('data-vesselID');
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
                                url:'{{url("/admin/vessel")}}/' +id,
                                data:{
                                    "_token": "{{ csrf_token() }}",
                                },
                                success: function(response) {
                                    //
                                    //window.location.href = "/admin/vessel";
                                    Swal.fire(
                                        'Deleted!',
                                        'Vessel has been deleted.',
                                        'success'
                                    )
                                    location.reload(true);
                                },
                                failure: function (response) {
                                    Swal.fire(
                                        'Internal Error',
                                        'Vessel was not deleted.',
                                        'error'
                                    )
                                }
                            });
                        }
                    })
                }
                $(document).ready(function() {
                    var ctx = $("#chart-line");
                    var myLineChart = new Chart(ctx, {
                        type: 'pie',
                        data: {
                            labels: ["Ferry", "Speed Boat"],
                            datasets: [{
                                data: [{{$ferry}}, {{$speedBoat}}],
                                backgroundColor: ["rgba(86, 226, 207, 0.5)", "rgba(86, 104, 226, 0.5)"]
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