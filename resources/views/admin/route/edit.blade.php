@extends('admin.layouts.dashboard')

@section('content')

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="routeCreateGridContainer">
        <div class="gridRouteCreateFirstRow">
            <div class="card">
                <div class="card-header bg-transparent">
                    <h3 class="card-title">Edit Route</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="/admin/route/{{ $route->id }}" id="myForm">
                        @method('PATCH')
                        @csrf()
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label class="ul-form__label">Route Name</label>
                                <input class="form-control" name="route_name" type="text" placeholder="Name" value="{{ $route->route_name }}" required>
                                <small class="ul-form__text form-text ">
                                    Please enter route name
                                </small>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="ul-form__label">Select User</label>
                                <select class="search-single custom-select" name="user_id" required>
                                    <optgroup label="User">
                                        @if (!$users->isEmpty() && $users != null )
                                            @foreach ($users as $user)
                                                <option value="{{ $user['id'] }}" {{old('user_id',$route->user_id) == $user->id ? 'selected' : ''}}>{{ $user['id'] }} ● {{ $user['first_name'] }} {{ $user['last_name'] }}</option>
                                            @endforeach
                                        @endif
                                    </optgroup>
                                </select>
                                <small class="ul-form__text form-text ">
                                    Please select user
                                </small>
                            </div>
                        </div>
                        <div class="originRoute">
                            <div class="addFieldBox">
                                <div class="routeCounter">1</div>
                                <a href="#" data-toggle="tooltip" data-placement="auto" title="Add Route" class="btn btn-outline-primary addField">+</a>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label class="ul-form__label">Origin</label>
                                    <input data-nametag="tags-manual-suggestions" name="origin[]" class="form-control" value="{{ $route->data[0]['Origin'] }}" required>
                                    <small class="ul-form__text form-text ">
                                        Please select origin
                                    </small>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="ul-form__label">Destination</label>
                                    <input data-nametag="tags-manual-suggestions" name="destination[]" class="form-control" value="{{ $route->data[0]['Destination'] }}" required>
                                    <small class="ul-form__text form-text ">
                                        Please select destination
                                    </small>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="ul-form__label">Departure Time</label>
                                    <div class="datetime">
                                        <input type="text" name="departure_time[]" class="form-control" placeholder="Departure Time" value="{{ $route->data[0]['Departure_time'] }}">
                                    </div>
                                    <small class="ul-form__text form-text ">
                                        Please enter departure time
                                    </small> 
                                </div>
                            </div>
                            <div class="form-row">
                                <div>
                                    <input type="hidden" value="1" name="ticket_type_id[]">
                                    <input type="hidden" value="2" name="ticket_type_id[]">
                                    <input type="hidden" value="3" name="ticket_type_id[]">
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="ul-form__label">Adult</label>
                                    <input type="number" size="6" min="1" max="10000" name="price[]" class="form-control" placeholder="Price" value="{{ $route->data[0]['Price_list']['Adult'] }}" required>
                                    <small class="ul-form__text form-text ">
                                        Please enter price
                                    </small>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="ul-form__label">Child</label>
                                    <input type="number" size="6" min="1" max="10000" name="price[]" class="form-control" placeholder="Price" value="{{ $route->data[0]['Price_list']['Child'] }}" required>
                                    <small class="ul-form__text form-text ">
                                        Please enter price
                                    </small>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="ul-form__label">Infant</label>
                                    <input type="number" size="6" min="1" max="10000" name="price[]" class="form-control" placeholder="Price" value="{{ $route->data[0]['Price_list']['Infant'] }}" required>
                                    <small class="ul-form__text form-text ">
                                        Please enter price
                                    </small>
                                </div>
                            </div>
                            <div class="originAddTicketFieldBox">
                                <a href="#" data-toggle="tooltip" data-placement="auto" title="Add Custom Ticket" class="btn btn-outline-primary originAddTicketField">Add Custom Ticket</a>
                            </div>
                            <div class="customTicketContainer">
                                @if($route->data[0]['Custom_ticket'] != null)
                                    @foreach ($route->data[0]['Custom_ticket'] as $key => $value)
                                        <div class="dynamicCustomTicketFieldContainer">
                                            <hr>
                                            <div class="removeTicketFieldBox">
                                                <div class="customTicketCounter"></div>
                                                <a href="#" data-toggle="tooltip" data-placement="auto" title="Remove" class="btn btn-danger removeTicketField">-</a>
                                            </div>
                                            <div class="form-row">
                                                <div class="form-group col-md-4">
                                                    <label class="ul-form__label">Custom Ticket</label>
                                                    <select class="search-single custom-select" name="custom_ticket_id[]" required>
                                                        <optgroup label="Custom Ticket">
                                                            @if (!$customTicket->ticket_types->isEmpty() && $customTicket->ticket_types != null )
                                                                @foreach ($customTicket->ticket_types as $ticketType)
                                                                    <option value="{{ $ticketType['id'] }}" {{old('custom_ticket_id[]',$key) == $ticketType->name ? 'selected' : ''}}>{{ $ticketType['name'] }}</option>
                                                                @endforeach
                                                            @endif
                                                        </optgroup>
                                                    </select>
                                                    <small class="ul-form__text form-text ">
                                                        Please select custom ticket
                                                    </small>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label class="ul-form__label">Price</label>
                                                    <input type="number" min="1" max="10000" name="custom_ticket_price[]" class="form-control" placeholder="Price" value="{{ $value }}" required>
                                                    <small class="ul-form__text form-text ">
                                                        Please enter price
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                                
                            </div>
                        </div>
                        <input type="hidden" value="" name="customTicketCount" id="customTicketCountID">
                        <div class="appendHere">
                            @foreach ($route->data as $key => $value)
                                @if($key > 0)
                                    <div class="dynamicRoute">
                                        <div class="deleteFieldBox">
                                            <div class="routeCounter"></div>
                                            <a href="#" data-toggle="tooltip" data-placement="auto" title="Delete Route" class="btn btn-danger deleteField">-</a>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-4">
                                                <label class="ul-form__label">Origin</label>
                                                <input data-nametag="tags-manual-suggestions" name="origin[]" class="form-control" value="{{ $value['Origin'] }}" required>
                                                <small class="ul-form__text form-text ">
                                                    Please select origin
                                                </small>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label class="ul-form__label">Destination</label>
                                                <input data-nametag="tags-manual-suggestions" name="destination[]" class="form-control" value="{{ $value['Destination'] }}" required>
                                                <small class="ul-form__text form-text ">
                                                    Please select destination
                                                </small>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label class="ul-form__label">Departure Time</label>
                                                <div class="datetime">
                                                    <input type="text" name="departure_time[]" class="form-control" placeholder="Departure Time" value="{{ $value['Departure_time'] }}">
                                                </div>
                                                <small class="ul-form__text form-text ">
                                                    Please enter departure time
                                                </small>
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div>
                                                <input type="hidden" value="1" name="ticket_type_id[]">
                                                <input type="hidden" value="2" name="ticket_type_id[]">
                                                <input type="hidden" value="3" name="ticket_type_id[]">
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label class="ul-form__label">Adult</label>
                                                <input type="number" size="6" min="1" max="10000" name="price[]" class="form-control" placeholder="Price" value="{{ $value['Price_list']['Adult'] }}" required>
                                                <small class="ul-form__text form-text ">
                                                    Please enter price
                                                </small>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label class="ul-form__label">Child</label>
                                                <input type="number" size="6" min="1" max="10000" name="price[]" class="form-control" placeholder="Price" value="{{ $value['Price_list']['Child'] }}" required>
                                                <small class="ul-form__text form-text ">
                                                    Please enter price
                                                </small>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label class="ul-form__label">Infant</label>
                                                <input type="number" size="6" min="1" max="10000" name="price[]" class="form-control" placeholder="Price" value="{{ $value['Price_list']['Infant'] }}" required>
                                                <small class="ul-form__text form-text ">
                                                Please enter price
                                                </small>
                                            </div>
                                        </div>
                                        <div class="originAddTicketFieldBox">
                                            <a href="#" data-toggle="tooltip" data-placement="auto" title="Add Custom Ticket" class="btn btn-outline-primary originAddTicketField">Add Custom Ticket</a>
                                        </div>
                                        <div class="customTicketContainer">
                                            @if($route->data[$key]['Custom_ticket'] != null)
                                                @foreach ($route->data[$key]['Custom_ticket'] as $key2 => $value2)
                                                    <div class="dynamicCustomTicketFieldContainer">
                                                        <hr>
                                                        <div class="removeTicketFieldBox">
                                                            <div class="customTicketCounter"></div>
                                                            <a href="#" data-toggle="tooltip" data-placement="auto" title="Remove" class="btn btn-danger removeTicketField">-</a>
                                                        </div>
                                                        <div class="form-row">
                                                            <div class="form-group col-md-4">
                                                                <label class="ul-form__label">Custom Ticket</label>
                                                                <select class="search-single custom-select" name="custom_ticket_id[]" required>
                                                                    <optgroup label="Custom Ticket">
                                                                        @if (!$customTicket->ticket_types->isEmpty() && $customTicket->ticket_types != null )
                                                                            @foreach ($customTicket->ticket_types as $ticketType)
                                                                                <option value="{{ $ticketType['id'] }}" {{old('custom_ticket_id[]',$key2) == $ticketType->name ? 'selected' : ''}}>{{ $ticketType['name'] }}</option>
                                                                            @endforeach
                                                                        @endif
                                                                    </optgroup>
                                                                </select>
                                                                <small class="ul-form__text form-text ">
                                                                    Please select custom ticket
                                                                </small>
                                                            </div>
                                                            <div class="form-group col-md-4">
                                                                <label class="ul-form__label">Price</label>
                                                                <input type="number" min="1" max="10000" name="custom_ticket_price[]" class="form-control" placeholder="Price" value="{{ $value2 }}" required>
                                                                <small class="ul-form__text form-text ">
                                                                    Please enter price
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif
                                            
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>    
                        <div class="card-footer">
                            <div class="row text-center">
                                <div class="col-lg-12 ">
                                    <button class="btn btn-primary" type="submit">Submit</button>
                                    <a class="btn btn-outline-primary" href="/admin/route">Back</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
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
    @section('js_route_edit_select')
        <script defer>
            $(document).ready(function() {
                $('.search-single').select2();
            });

            $('.datetime > input').each(function() {
                $(this).datetimepicker({ format: 'HH:mm' });
            });
        </script>
    @endsection
    @section('js_route_edit_tagify')
        <script>
            //update custom ticket counter
            customTicketCounterLength = document.getElementsByClassName('customTicketCounter').length;
            if(customTicketCounterLength != 0){
                for (let i = 0; i < customTicketCounterLength; i++) {
                    document.getElementsByClassName('customTicketCounter')[i].innerHTML = i+1;
                }
            }
            //update route counter
            routeCounterLength = document.getElementsByClassName('routeCounter').length;
            for (let i = 0; i < routeCounterLength; i++) {
                document.getElementsByClassName('routeCounter')[i].innerHTML = i+1;
            }

            var islandNames = @json($islandNames);
            var input = document.querySelectorAll('input[data-nametag=tags-manual-suggestions]');
            
            // initialize all tagify
            for (let i = 0; i < input.length; i++) {
                tagify = new Tagify(input[i], {
                    maxTags: 1,
                    whitelist : islandNames.toString().split(","),
                    originalInputValueFormat: valuesArr => valuesArr.map(item => item.value).join(', '),
                    dropdown: {
                        position: "all",
                        maxItems: Infinity,
                        enabled: 0,
                        closeOnSelect: false, 
                        classname: "customSuggestionsList"
                    },
                    enforceWhitelist: true
                })
                
            }
                
            //for dynamic appending. This is field area
            var fields = '<div class="dynamicRoute">'+
                            '<div class="deleteFieldBox">'+
                                '<div class="routeCounter"></div>'+
                                '<a href="#" data-toggle="tooltip" data-placement="auto" title="Delete Route" class="btn btn-danger deleteField">-</a>'+
                            '</div>'+
                            '<div class="form-row">'+
                                '<div class="form-group col-md-4">'+
                                    '<label class="ul-form__label">Origin</label>'+
                                    '<input data-nametag="tags-manual-suggestions" name="origin[]" class="form-control" id="origin_id" value="{{ old('origin') }}" required>'+
                                    '<small class="ul-form__text form-text ">'+
                                        'Please select origin'+
                                    '</small>'+
                                '</div>'+
                                '<div class="form-group col-md-4">'+
                                    '<label class="ul-form__label">Destination</label>'+
                                    '<input data-nametag="tags-manual-suggestions" name="destination[]" class="form-control" id="destination_id" value="{{ old('destination') }}" required>'+
                                    '<small class="ul-form__text form-text ">'+
                                        'Please select destination'+
                                    '</small>'+
                                '</div>'+
                                '<div class="form-group col-md-4">'+
                                    '<label class="ul-form__label">Departure Time</label>'+
                                    '<div class="datetime">'+
                                        '<input type="text" name="departure_time[]" class="form-control" placeholder="Departure Time" value="{{ old('departure_time') }}">'+
                                    '</div>'+
                                    '<small class="ul-form__text form-text ">'+
                                        'Please enter departure time'+
                                    '</small>'+
                                '</div>'+
                            '</div>'+
                            '<div class="form-row">'+
                                '<div>'+
                                    '<input type="hidden" value="1" name="ticket_type_id[]">'+
                                    '<input type="hidden" value="2" name="ticket_type_id[]">'+
                                    '<input type="hidden" value="3" name="ticket_type_id[]">'+
                                '</div>'+
                                '<div class="form-group col-md-4">'+
                                    '<label class="ul-form__label">Adult</label>'+
                                    '<input type="number" size="6" min="1" max="10000" name="price[]" class="form-control" placeholder="Price" value="{{ old('price') }}" required>'+
                                    '<small class="ul-form__text form-text ">'+
                                        'Please enter price'+
                                    '</small>'+
                                '</div>'+
                                '<div class="form-group col-md-4">'+
                                    '<label class="ul-form__label">Child</label>'+
                                    '<input type="number" size="6" min="1" max="10000" name="price[]" class="form-control" placeholder="Price" value="{{ old('price') }}" required>'+
                                    '<small class="ul-form__text form-text ">'+
                                        'Please enter price'+
                                    '</small>'+
                                '</div>'+
                                '<div class="form-group col-md-4">'+
                                    '<label class="ul-form__label">Infant</label>'+
                                    '<input type="number" size="6" min="1" max="10000" name="price[]" class="form-control" placeholder="Price" value="{{ old('price') }}" required>'+
                                    '<small class="ul-form__text form-text ">'+
                                       'Please enter price'+
                                    '</small>'+
                                '</div>'+
                            '</div>'+
                            '<div class="originAddTicketFieldBox">'+
                                '<a href="#" data-toggle="tooltip" data-placement="auto" title="Add Custom Ticket" class="btn btn-outline-primary originAddTicketField">Add Custom Ticket</a>'+
                            '</div>'+
                            '<div class="customTicketContainer">'+
                            '</div>'+
                        '</div>';

            //Counter for list to work in dynamic field.
            
  
            $('.addField').on('click',function(){
                //append route to this class
                $('.appendHere').append(fields);
                //reinitialize it so it gets all current inputs
                input = document.querySelectorAll('input[data-nametag=tags-manual-suggestions]');

                //reinitialize search2
                $('.search-single').select2();

                $('.datetime > input').each(function() {
                    $(this).datetimepicker({ format: 'HH:mm' });
                });

                $('[data-toggle="tooltip"]').tooltip();

                //get route counter
                routeCounterLength = document.getElementsByClassName('routeCounter').length;
                document.getElementsByClassName('routeCounter')[routeCounterLength-1].innerHTML = routeCounterLength;

                //get newest input length which will be added by 2 so to make it work in tagify we have to subtract
                var countTagifyLists = input.length;
                //element will start from 0,1,2.. etc. In here it starts from 2
                tagify = new Tagify(input[countTagifyLists-2], {
                    maxTags: 1,
                    whitelist : islandNames.toString().split(","),
                    originalInputValueFormat: valuesArr => valuesArr.map(item => item.value).join(', '),
                    dropdown: {
                        position: "all",
                        maxItems: Infinity,
                        enabled: 0,
                        closeOnSelect: false, 
                        classname: "customSuggestionsList"
                    },
                    enforceWhitelist: true
                }) 
                //element will start from 3 
                tagify = new Tagify(input[countTagifyLists-1], {
                    maxTags: 1,
                    whitelist : islandNames.toString().split(","),
                    originalInputValueFormat: valuesArr => valuesArr.map(item => item.value).join(', '),
                    dropdown: {
                        position: "all",
                        maxItems: Infinity,
                        enabled: 0,
                        closeOnSelect: false, 
                        classname: "customSuggestionsList"
                    },
                    enforceWhitelist: true
                }) 
            });
            
            var ticketField = '<div class="dynamicCustomTicketFieldContainer">'+
                                    '<hr>'+
                                    '<div class="removeTicketFieldBox">'+
                                        '<div class="customTicketCounter"></div>'+
                                        '<a href="#" data-toggle="tooltip" data-placement="auto" title="Remove" class="btn btn-danger removeTicketField">-</a>'+
                                    '</div>'+
                                    '<div class="form-row">'+
                                        '<div class="form-group col-md-4">'+
                                            '<label class="ul-form__label">Custom Ticket</label>'+
                                            '<select class="search-single custom-select" name="custom_ticket_id[]" required>'+
                                                '<optgroup label="Custom Ticket">'+
                                                    '@if (!$customTicket->ticket_types->isEmpty() && $customTicket->ticket_types != null )'+
                                                        '@foreach ($customTicket->ticket_types as $ticketType)'+
                                                            '<option value="{{ $ticketType->id }}">{{ $ticketType['name'] }}</option>'+
                                                        '@endforeach'+
                                                    '@endif'+
                                                '</optgroup>'+
                                            '</select>'+
                                            '<small class="ul-form__text form-text ">'+
                                                'Please select custom ticket'+
                                            '</small>'+
                                        '</div>'+
                                        '<div class="form-group col-md-4">'+
                                            '<label class="ul-form__label">Price</label>'+
                                            '<input type="number" min="1" max="10000" name="custom_ticket_price[]" class="form-control" id="customTicketPriceID" placeholder="Price" value="{{ old('custom_ticket_price') }}" required>'+
                                            '<small class="ul-form__text form-text ">'+
                                                'Please enter price'+
                                            '</small>'+
                                        '</div>'+
                                    '</div>'+
                                '</div>';

            //When the add ticket button is clicked it will append to the closest originCustomTicketFieldContainer class
            $('.card-body').on('click', '.originAddTicketField',function(){
                $(this).parent().next('.customTicketContainer').append(ticketField);
                //reinitialize tooltip
                $('[data-toggle="tooltip"]').tooltip();

                //reinitialize search
                $('.search-single').select2();
               
                //updating the counter for index
                customTicketCounterLength = document.getElementsByClassName('customTicketCounter').length;
                for (let i = 0; i < customTicketCounterLength; i++) {
                    document.getElementsByClassName('customTicketCounter')[i].innerHTML = i+1;
                }
            });

            

            //When the add ticket button is clicked it will append to the closest dynamicAddTicketField class
            $('.card-body').on('click', '.dynamicAddTicketField',function(){
                $(this).closest('.dynamicCustomTicketFieldContainer').append(ticketField);
                
                //reinitialize tooltip
                $('[data-toggle="tooltip"]').tooltip();

                //reinitialize search
                $('.search-single').select2();

                //updating the counter for index
                customTicketCounterLength = document.getElementsByClassName('customTicketCounter').length;
                for (let i = 0; i < customTicketCounterLength; i++) {
                    document.getElementsByClassName('customTicketCounter')[i].innerHTML = i+1;
                }
            });

            //When remove ticket button is clicked it will remove the container (The parent) of removeTicketField 
            $('.card-body').on('click', '.removeTicketField',function(){
                $(this).parent().parent().remove();
                customTicketCounterLength = document.getElementsByClassName('customTicketCounter').length;
                for (let i = 0; i < customTicketCounterLength; i++) {
                    document.getElementsByClassName('customTicketCounter')[i].innerHTML = i+1;
                }
            });
            //When remove field button is clicked it will remove the container (The parent) of deleteField
            $('.card-body').on('click', '.deleteField',function(){
                $(this).parent().parent().remove();
                countTagifyLists = countTagifyLists - 2;

                routeCounterLength = document.getElementsByClassName('routeCounter').length;
                for (let i = 0; i < routeCounterLength; i++) {
                    document.getElementsByClassName('routeCounter')[i].innerHTML = i+1;
                }
            });

        </script>
        <script>
            //used for testing instead submitting each time
            $('.testBtn').on('click',function(){
                var count = [];
                var originRoute = document.getElementsByClassName('originRoute');
                var dynamicRoute = document.getElementsByClassName('dynamicRoute');
                
                //get the amount of custom tickets in first route
                let originCustomTicketFieldContainer = originRoute[0].getElementsByClassName('originCustomTicketFieldContainer').length;
                let originRouteCustomTicket= originRoute[0].getElementsByClassName('dynamicCustomTicketFieldContainer').length;
                //add it to array
                count.push(originCustomTicketFieldContainer + originRouteCustomTicket);
                //get the amount of custom tickets in dynamically generated route and add it to array
                for(let i = 0; i < dynamicRoute.length; i++){
                    let dynamicCustomTicketFieldContainer = dynamicRoute[i].getElementsByClassName('dynamicCustomTicketFieldContainer').length;
                    let dynamicRouteCustomTicket = dynamicRoute[i].getElementsByClassName('customTicketFieldContainer').length;
                    count.push(dynamicCustomTicketFieldContainer + dynamicRouteCustomTicket );
                }
                //update hidden field with array value
                console.log(count);
                $('#customTicketCountID').val(JSON.stringify(count));
            });
            
        </script>
        <script>
            //when submit button is clicked, it will do this function
            $('#myForm').submit(function() {
                var count = [];
                var originRoute = document.getElementsByClassName('originRoute');
                var dynamicRoute = document.getElementsByClassName('dynamicRoute');
                
                //get the amount of custom tickets in first route
                let originCustomTicketFieldContainer = originRoute[0].getElementsByClassName('originCustomTicketFieldContainer').length;
                let originRouteCustomTicket= originRoute[0].getElementsByClassName('dynamicCustomTicketFieldContainer').length;
                
                //add it to array
                count.push(originCustomTicketFieldContainer + originRouteCustomTicket);

                //get the amount of custom tickets in dynamically generated route and add it to array
                for(let i = 0; i < dynamicRoute.length; i++){
                    let dynamicCustomTicketFieldContainer = dynamicRoute[i].getElementsByClassName('dynamicCustomTicketFieldContainer').length;
                    let dynamicRouteCustomTicket = dynamicRoute[i].getElementsByClassName('customTicketFieldContainer').length;
                    count.push(dynamicCustomTicketFieldContainer + dynamicRouteCustomTicket );
                }
                //update hidden field with array value
                $('#customTicketCountID').val(JSON.stringify(count));
                return true;
            });
        </script>
    @endsection
@endsection