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

    <div class="scheduleCreateGridContainer">
        <div class="titleArea">
            <div class="header">Create Schedule</div>
            <div>{{$userName->first_name}} {{$userName->last_name}}</div>
        </div>
        <div class="userFilter">
            <form action="/admin/schedule/create" method="get">
                <h4>User</h4>
                <select class="search-single custom-select" name="user_id" id="user_id" required>
                    <optgroup label="User ID">
                        @if (!$users->isEmpty() && $users != null )
                            @foreach ($users as $user)
                            <option value="{{ $user['id'] }}" {{old('user_id',$user_id) == $user->id ? 'selected' : ''}}>{{ $user['id'] }} ● {{ $user['first_name'] }} {{ $user['last_name'] }}</option>
                            @endforeach
                        @endif
                    </optgroup>
                </select>
                <button class="btn btn-primary" type="submit">Filter</button>
            </form>
        </div>
      
        <div id='external-events'>
            
            <div class="title">Routes</div>
            <div id='external-events-list'>
                @isset($routes)
                    @forelse($routes as $route)
                        <div 
                            class='fc-event fc-h-event fc-daygrid-event fc-daygrid-block-event'
                            data-event='{"route_id":"{{ $route->id }}","title":"{{ $route->route_name }}","start":"00:00:00","end":"00:00:01","user_id":"{{ $route->user_id }}"}'>
                            <div class='fc-event-main'>{{$route->route_name}}</div>
                        </div>
                    @empty
                        <p>There are no routes</p>
                    @endforelse
                @endisset
            </div>
            <p>
                <input type='checkbox' id='drop-remove' />
                <label for='drop-remove'>remove after drop</label>
            </p>
            {!! $routes->withQueryString()->links() !!}
        </div>
        <div id='calendar-wrap'>
            <div 
                id='calendar' 
                data-schedule-fc-load-event="{{ route('schedule.scheduleFCLoadEvent',$user_id) }}"
                data-schedule-fc-update-event="{{ route('schedule.scheduleFCUpdateEvent') }}"
                data-schedule-fc-store-event="{{ route('schedule.scheduleFCStoreEvent') }}"
                data-schedule-fc-delete-event="{{ route('schedule.scheduleFCDeleteEvent') }}"
                
                data-schedule-fc-fast-event-delete-event="{{ route('schedule.scheduleFCFastEventDeleteEvent') }}">
            </div>
        </div>
    </div>

    <div class="modal fade" id="scheduleFCModal" tabindex="-1" role="dialog" aria-labelledby="titleModal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="titleModal">Popup modal</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
  
                    <div class="message"></div>
  
                    <form id="formScheduleFCEvent">
                        <div class="form-group row">
                            <label for="title" class="col-sm-4 col-form-label">Title</label>
                            <div class="col-sm-8">
                                <input type="text" name="title" class="form-control" id="title">
                                <input type="hidden" name="id">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="start" class="col-sm-4 col-form-label">Start date</label>
                            <div class="col-sm-8">
                                <input type="text" name="start" class="form-control date-time" id="start">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="end" class="col-sm-4 col-form-label">End date</label>
                            <div class="col-sm-8">
                                <input type="text" name="end" class="form-control date-time" id="end">
                            </div>
                        </div>
                        <div>
                            <input type="hidden" name="user_id">
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Select Route</label>
                            <div class="col-sm-8">
                                <select class="search-single custom-select" name="route_id" id="route_id" required>
                                    <optgroup label="Route">
                                        @if (!$routesAll->isEmpty() && $routesAll != null )
                                            @foreach ($routesAll as $route)
                                                <option value="{{ $route['id'] }}">{{ $route['id'] }} ● {{ $route['route_name'] }}</option>
                                            @endforeach
                                        @endif
                                    </optgroup>
                                </select>
                            </div>
                        </div>
                      
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Select Vessel</label>
                            <div class="col-sm-8">
                                <select class="search-single custom-select" name="vessel_id" id="vessel_id" required>
                                    <optgroup label="Vessel">
                                        @if (!$vessels->isEmpty() && $vessels != null )
                                            @foreach ($vessels as $vessel)
                                                <option value="{{ $vessel['id'] }}">{{ $vessel['id'] }} ● {{ $vessel['name'] }}</option>
                                            @endforeach
                                        @endif
                                    </optgroup>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger deleteScheduleFCEvent">Delete</button>
                    <button type="button" class="btn btn-primary saveScheduleFCEvent">Save</button>
                </div>
             </div>
        </div>
    </div>
    
    
  
    @section('js_schedule_create_datetimepicker')
        <script type="text/javascript">
            $(document).ready(function() {
                $('.search-single').select2();

                $(function () {
                    $('#datetimepicker2').datetimepicker({
                        format: 'YYYY-MM-DD HH:mm:ss'
                    });
                });
            });
        </script>
        
        <script>
            let objCalendar;
            $(document).ready(function () {

              
                /* initialize the external events
                -----------------------------------------------------------------*/
                var containerEl = document.getElementById('external-events-list');
                new FullCalendar.Draggable(containerEl, {
                    itemSelector: '.fc-event',
                    eventData: function(eventEl) {
                        return {
                        title: eventEl.innerText.trim()
                        }
                    }
                });
            
                /* initialize the calendar
                -----------------------------------------------------------------*/
                var calendarEl = document.getElementById('calendar');
                var calendar = new FullCalendar.Calendar(calendarEl, {
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
                    },
                    editable: true,
                    droppable: true, // this allows things to be dropped onto the calendar
                    dayMaxEvents: true,
                    selectable: true,
                    events: scheduleFCEvent('scheduleFcLoadEvent'),
                    drop: function(element) {
                        let Event = JSON.parse(element.draggedEl.dataset.event);
                        // is the "remove after drop" checkbox checked?
                        if (document.getElementById('drop-remove').checked) {
                            // if so, remove the element from the "Draggable Events" list
                            element.draggedEl.parentNode.removeChild(element.draggedEl);

                            Event._method = "POST";
                            //sendScheduleFCEvent(scheduleFCEvent('scheduleFcFastEventDeleteEvent'), Event);
                        }
                        let start = moment(`${element.dateStr} ${Event.start}`).format("YYYY-MM-DD HH:mm:ss");
                        let end = moment(`${element.dateStr} ${Event.end}`).format("YYYY-MM-DD HH:mm:ss");

                        Event.start = start;
                        Event.end = end;

                        //delete Event.id;
                        //delete Event._method;

                        sendScheduleFCEvent(scheduleFCEvent('scheduleFcStoreEvent'), Event);
                    },
                    eventDrop: function(element){
                        let start = moment(element.event.start).format("YYYY-MM-DD HH:mm:ss");
                        let end = moment(element.event.end).format("YYYY-MM-DD HH:mm:ss");
                        let newEvent = {
                            _method:'PUT',
                            id: element.event.id,
                            title: element.event.title,
                            start: start,
                            end: end,
                            vessel_id: element.event.extendedProps.vessel_id,
                            user_id: element.event.extendedProps.user_id,
                            route_id: element.event.extendedProps.route_id,
                            available_seats: element.event.extendedProps.available_seats
                        };

                        sendScheduleFCEvent(scheduleFCEvent('scheduleFcUpdateEvent'),newEvent,calendar);
                    },
                    eventClick: function(element){
                        clearErrorMessages('.message');
                        resetForm("#formScheduleFCEvent");
                        $("#scheduleFCModal").modal('show');
                        $("#scheduleFCModal #titleModal").text('Change Event');
                        $("#scheduleFCModal button.deleteScheduleFCEvent").css("display","flex");//css
                        
                        let id = element.event.id;
                        $("#scheduleFCModal input[name='id']").val(id);

                        let title = element.event.title;
                        $("#scheduleFCModal input[name='title']").val(title);

                        let start = moment(element.event.start).format("DD/MM/YYYY HH:mm:ss");
                        $("#scheduleFCModal input[name='start']").val(start);

                        let end = moment(element.event.end).format("DD/MM/YYYY HH:mm:ss");
                        $("#scheduleFCModal input[name='end']").val(end);

                        let vessel_id = element.event.extendedProps.vessel_id;
                        if(vessel_id != null){
                            $("#scheduleFCModal #vessel_id").val([vessel_id]).trigger("change");
                        }else{
                            $("#scheduleFCModal #vessel_id").val([1]).trigger("change");//reset to id 1 if no vessel id
                        }
                        
                        let route_id = element.event.extendedProps.route_id;
                        $("#scheduleFCModal #route_id").val(route_id).trigger("change");
                        
                        let user_id = element.event.extendedProps.user_id;
                        $("#scheduleFCModal input[name='user_id']").val(user_id);
                        
                        
                    },
                    select: function(element){
                        clearErrorMessages('.message');
                        resetForm("#formScheduleFCEvent");
                        $("#scheduleFCModal input[name='id']").val('');

                        $("#scheduleFCModal").modal('show');
                        $("#scheduleFCModal #titleModal").text('Add Event');
                        $("#scheduleFCModal button.deleteScheduleFCEvent").css("display","none");//css
                        
                        let start = moment(element.start).format("DD/MM/YYYY HH:mm:ss");
                        $("#scheduleFCModal input[name='start']").val(start);

                        let end = moment(element.end).format("DD/MM/YYYY HH:mm:ss");
                        $("#scheduleFCModal input[name='end']").val(end);

                        //let vessel_id = element.event.extendedProps.vessel_id;
                        $("#scheduleFCModal #vessel_id").val(1).trigger("change");//reset select2

                        //let route_id = element.event.extendedProps.route_id;
                        $("#scheduleFCModal #route_id").val(1).trigger("change");//reset select2

                        let user_id = document.getElementById('user_id').value;
                        $("#scheduleFCModal input[name='user_id']").val(user_id);

                        calendar.unselect();
                    },
                    eventResize: function(element){
                        let start = moment(element.event.start).format("YYYY-MM-DD HH:mm:ss");
                        let end = moment(element.event.end).format("YYYY-MM-DD HH:mm:ss");
                        let newEvent = {
                            _method:'PUT',
                            vessel_id: element.event.extendedProps.vessel_id,
                            user_id: element.event.extendedProps.user_id,
                            route_id: element.event.extendedProps.route_id,
                            available_seats: element.event.extendedProps.available_seats,
                            title: element.event.title,
                            id: element.event.id,
                            start: start,
                            end: end
                        };

                        sendScheduleFCEvent(scheduleFCEvent('scheduleFcUpdateEvent'),newEvent,calendar);
                    },
                    eventReceive: function(element){
                        element.event.remove();
                    }
                });
                
                objCalendar = calendar;
                calendar.render();
            
            });

            
            //get route to update
            function scheduleFCEvent(route) {
                return document.getElementById('calendar').dataset[route];
            }

            //update new schedule FC Event
            function sendScheduleFCEvent(route, data_) {
                $.ajax({
                    url: route,
                    data: data_,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    method: 'POST',
                    dataType: 'json',
                    success:function (json) {
                        if(json){
                            objCalendar.refetchEvents();
                        }
                    },
                    error:function (json) {
                        let responseJSON = json.responseJSON.errors;
                        $(".message").html(loadErrors(responseJSON));
                    }
                });
            }
            //reset popup modal values
            function resetForm(form){
                $(form)[0].reset();
            }

            $(".saveScheduleFCEvent").click(function () {
                let id = $("#scheduleFCModal input[name='id']").val();
                let title = $("#scheduleFCModal input[name='title']").val();
                let start = moment($("#scheduleFCModal input[name='start']").val(),"DD/MM/YYYY HH:mm:ss").format("YYYY-MM-DD HH:mm:ss");
                let end = moment($("#scheduleFCModal input[name='end']").val(),"DD/MM/YYYY HH:mm:ss").format("YYYY-MM-DD HH:mm:ss");
                let vessel_id = $("#scheduleFCModal #vessel_id").val();
                let route_id = $("#scheduleFCModal #route_id").val();
                let user_id = $("#scheduleFCModal input[name='user_id']").val();

                let Event = {
                    title: title,
                    start: start,
                    end: end,
                    vessel_id: vessel_id,
                    route_id: route_id,
                    user_id: user_id,
                };

                let route;

                if(id == ''){//if clicked in empty day then there will be no id so it will create a new one
                    route = scheduleFCEvent('scheduleFcStoreEvent');
                }else{
                    route = scheduleFCEvent('scheduleFcUpdateEvent');
                    Event.id = id;//schedule id
                    Event._method = 'PUT';
                }
                $("#scheduleFCModal").modal('hide');
                sendScheduleFCEvent(route,Event);

            });
            
            $(".deleteScheduleFCEvent").click(function () {
                let id = $("#scheduleFCModal input[name='id']").val();//get id
                let Event = {//placing the data
                    id: id,
                    _method: 'POST'
                };
                let webRoute = scheduleFCEvent('scheduleFcDeleteEvent');//get route
                $("#scheduleFCModal").modal('hide');
                sendScheduleFCEvent(webRoute,Event);
            });
            
            //append error message. Placing it inside popup modal
            function loadErrors(response) {
                let boxAlert = `<div class="alert alert-danger">`;
                for (let fields in response){
                    boxAlert += `<span>${response[fields]}</span><br/>`;
                }
                boxAlert += `</div>`;
                return boxAlert.replace(/\,/g,"<br/>");
            }
            
            //Clear error messages
            function clearErrorMessages(element){
                $(element).text('');
            }
        </script>
    @endsection


@endsection
