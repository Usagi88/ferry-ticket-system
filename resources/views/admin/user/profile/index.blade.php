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
    
    @section('js_profile_edit_alert')
        <script>
            @if(!empty(Session::get('Edit-success')))
                var popupId = "{{ uniqid() }}";
                if(!sessionStorage.getItem('shown-' + popupId)) {
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: 'Profile has been edited',
                        showConfirmButton: false,
                        timer: 1200
                    })
                }
                sessionStorage.setItem('shown-' + popupId, '1');
            @endif
        </script>
    @endsection
    @section('js_tooltip')
        <script>
            //tooltip
            $(document).ready(function(){
                $('[data-toggle="tooltip"]').tooltip();
            });
        </script>
    @endsection
    <div class="profileIndexGridContainer">
        <div class="gridProfileFirstRow">
            <div class="container">
                <div class="d-flex mb-xl"><img class="avatar-xl mr-lg" src="{{ asset('temp/vendor/css/admin/img/avatar.svg') }}" alt="">
                    <div class="w-full">
                        <div class="d-flex flex-column flex-wrap flex-sm-row align-items-sm-center mb-sm">
                            <div class="mr-xl">
                                <h4 class="m-0 d-flex align-items-center">{{$user->username}}</h4>
                                <p class="m-0 text-small text-muted">{{$user->profile->title}}</p>
                                <p class="m-0 text-small text-muted">{{$user->first_name}} {{$user->last_name}}</p>
                            </div>
                            <div class="my-sm">
                                @if($user->hasRole("admin"))
                                    <div class="roleBadge roleBadge-pill roleBadge-outline-admin p-2 m-1">
                                        Admin
                                    </div>
                                @elseif($user->hasRole("staff"))
                                    <div class="roleBadge roleBadge-pill roleBadge-outline-staff p-2 m-1">
                                        Staff
                                    </div>
                                @elseif($user->hasRole("merchant"))
                                    <div class="roleBadge roleBadge-pill roleBadge-outline-merchant p-2 m-1">
                                        Merchant
                                    </div>
                                @elseif($user->hasRole("agent"))
                                    <div class="roleBadge roleBadge-pill roleBadge-outline-agent p-2 m-1">
                                        Agent
                                    </div>
                                @endif
                            </div>
                            <div class="flex-grow-1"></div>
                            <div class="datatableActionButton">
                                <a class="btn-primaryTest" href="/admin/user/{{ $user['id'] }}/profile/edit" data-toggle="tooltip" data-placement="auto" title="Edit"><i class="far fa-edit"></i></a>
                            </div>
                        </div>
                        <div class="stats">
                            <p class="mr-xl"><span class="font-weight-semi">{{ $user->ownedVessels->count() }} </span><span class="text-small text-muted">Vessels</span></p>
                            <p class="mr-xl"><span class="font-weight-semi">{{ count($user->bookings) }} </span><span class="text-small text-muted">Bookings</span></p>
                            <p class="mr-xl"><span class="font-weight-semi">{{ count($user->schedules) }} </span><span class="text-small text-muted">Schedules</span></p>
                            <p class="mr-xl"><span class="font-weight-semi">{{ count($user->routes) }} </span><span class="text-small text-muted">Routes</span></p>
                        </div>
                        <div class="d-flex mb-sm desc">
                            <p class="text-muted">{{$user->profile->description}}</p>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    
        <div class="gridProfileSecondRow">
            <div class="cardPf">
                <div class="card-body">
                    <div class="d-flex flex-column justify-content-center align-items-center"><span class="badge badge-opacity badge-primary rounded-circle mb-md"><i class="fas fa-ship"></i></span>
                        <h3 class="font-weight-bold m-0">{{ $user->ownedVessels->count() }}</h3>
                        <h6 class="heading-muted mb-xl">My Vessels</h6><a class="btn btn-opacity-primary rounded btn-sm d-flex" href="/admin/user/{{$user->id}}/profile/my-vessel">Show Vessels</a>
                    </div>
                </div>
            </div>
            <div class="cardPf">
                <div class="card-body">
                    <div class="d-flex flex-column justify-content-center align-items-center"><span class="badge badge-opacity badge-red rounded-circle mb-md"><i class="fas fa-address-book"></i></span>
                        <h3 class="font-weight-bold m-0">{{ count($user->bookings) }}</h3>
                        <h6 class="heading-muted mb-xl">My Bookings</h6><a class="btn btn-opacity-red rounded btn-sm d-flex" href="/admin/user/{{$user->id}}/profile/my-booking">Show Bookings</a>
                    </div>
                </div>
            </div>
            <div class="cardPf">
                <div class="card-body">
                    <div class="d-flex flex-column justify-content-center align-items-center"><span class="badge badge-opacity badge-green rounded-circle mb-md"><i class="fas fa-clock"></i></span>
                        <h3 class="font-weight-bold m-0">{{ count($user->schedules) }}</h3>
                        <h6 class="heading-muted mb-xl">My Schedules</h6><a class="btn btn-opacity-green rounded btn-sm d-flex" href="/admin/user/{{$user->id}}/profile/my-schedule">Show Schedules</a>
                    </div>
                </div>
            </div>
            <div class="cardPf">
                <div class="card-body">
                    <div class="d-flex flex-column justify-content-center align-items-center"><span class="badge badge-opacity badge-purple rounded-circle mb-md"><i class="fas fa-route"></i></span>
                        <h3 class="font-weight-bold m-0">{{ count($user->routes) }}</h3>
                        <h6 class="heading-muted mb-xl">My Routes</h6><a class="btn btn-opacity-purple rounded btn-sm d-flex" href="/admin/user/{{$user->id}}/profile/my-route">Show Routes</a>
                    </div>
                </div>
            </div>
            <div class="cardPf">
                <div class="card-body">
                    <div class="d-flex flex-column justify-content-center align-items-center"><span class="badge badge-opacity badge-yellow rounded-circle mb-md"><i class="fas fa-ticket-alt"></i></span>
                        <h3 class="font-weight-bold m-0">{{ count($user->ticket_types) }}</h3>
                        <h6 class="heading-muted mb-xl">My Ticket Types</h6><a class="btn btn-opacity-yellow rounded btn-sm d-flex" href="/admin/user/{{$user->id}}/profile/my-ticket-type">Show Ticket Types</a>
                    </div>
                </div>
            </div>
            @can('show_vessel_assign_to_users_card', App\Models\Profile::class)
                <div class="cardPf">
                    <div class="card-body">
                        <div class="d-flex flex-column justify-content-center align-items-center"><span class="badge badge-opacity badge-primary rounded-circle mb-md"><i class="fas fa-ship"></i></span>
                            <h3 class="font-weight-bold m-0">{{ $user->vesselsAssignedToUser->count() }}</h3>
                            <h6 class="heading-muted mb-xl">Vessels I'm assigned to</h6><a class="btn btn-opacity-primary rounded btn-sm d-flex" href="/admin/user/{{$user->id}}/profile/vessel-assigned-to">Show Vessels I'm assigned to</a>
                        </div>
                    </div>
                </div>
            @endcan
            @can('show_assigned_vessels_card', App\Models\Profile::class)
                <div class="cardPf">
                    <div class="card-body">
                        <div class="d-flex flex-column justify-content-center align-items-center"><span class="badge badge-opacity badge-primary rounded-circle mb-md"><i class="fas fa-ship"></i></span>
                            <h3 class="font-weight-bold m-0">{{ $user->assignedVessels->count() }}</h3>
                            <h6 class="heading-muted mb-xl">My Assigned Vessels to Agents</h6><a class="btn btn-opacity-primary rounded btn-sm d-flex" href="/admin/user/{{$user->id}}/profile/my-assigned-vessel">Show My Assigned Vessels</a>
                        </div>
                    </div>
                </div>
            @endcan
            @can('show_island_assign_to_users_card', App\Models\Profile::class)
                <div class="cardPf">
                    <div class="card-body">
                        <div class="d-flex flex-column justify-content-center align-items-center"><span class="badge badge-opacity badge-orange rounded-circle mb-md"><i class="fas fa-map-marker-alt"></i></span>
                            <h3 class="font-weight-bold m-0">{{ $user->islandsAssignedToUser->count() }}</h3>
                            <h6 class="heading-muted mb-xl">Islands I'm assigned to</h6><a class="btn btn-opacity-orange rounded btn-sm d-flex" href="/admin/user/{{$user->id}}/profile/my-agent-island">Show Islands I'm assigned to</a>
                        </div>
                    </div>
                </div>
            @endcan
        </div>
    </div>
    
@endsection