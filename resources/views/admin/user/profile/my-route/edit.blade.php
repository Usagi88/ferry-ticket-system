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

    <div class="bookingCreateGridContainer">
        <div class="gridUserCreateFirstRow">
            <div class="element-wrapper">
                <div class="element-box">
                   <form method="POST" action="/admin/user/{{$user->id}}/profile/my-route/{{ $route->id }}">
                        @method('PATCH')
                        @csrf()
                        <h5 class="form-header">Edit Route</h5>
                        <div class="form-desc">Edit route or ticket prices</div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-4" for="">Select Origin</label>
                            <div class="col-sm-8">
                                <select class="search-single custom-select" name="origin" required>
                                    <optgroup label="Origin">
                                        @if (!$islands->isEmpty() && $islands != null )
                                            @foreach ($islands as $island)
                                                <option value="{{ $island['atoll'] }}.{{ $island['name'] }}" {{old('origin',$route->origin) == ($island->atoll.'.'.$island->name) ? 'selected' : ''}}>{{ $island->atoll }}.{{ $island->name }}</option>
                                            @endforeach
                                        @endif
                                    </optgroup>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-4" for="">Select Destination</label>
                            <div class="col-sm-8">
                                <select class="search-single custom-select" name="destination" required>
                                    <optgroup label="Destination">
                                        @if (!$islands->isEmpty() && $islands != null )
                                            @foreach ($islands as $island)
                                                <option value="{{ $island['atoll'] }}.{{ $island['name'] }}" {{old('destination',$route->destination) == ($island->atoll.'.'.$island->name) ? 'selected' : ''}}>{{ $island->atoll }}.{{ $island->name }}</option>
                                            @endforeach
                                        @endif
                                    </optgroup>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-4" for="">Duration</label>
                            <div class="col-sm-8"><input type="number" min="1" max="10000" name="duration" class="form-control" placeholder="Duration" value="{{ $route->duration }}" required></div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-4" for="">Route Code</label>
                            <div class="col-sm-8"><input type="text" name="route_code" class="form-control" placeholder="Route Code" value="{{ $route->route_code }}" required></div>
                        </div>
                        
                        <fieldset class="form-group">
                            <legend><span>Ticket Price</span></legend>
                            <div class="form-group row">
                                <div>
                                    <input type="hidden" value="1" name="ticket_type_id[]">
                                    <input type="hidden" value="2" name="ticket_type_id[]">
                                    <input type="hidden" value="3" name="ticket_type_id[]">
                                </div>
                                <div class="prices">
                                    <div class="form-group">
                                        <label for="price">Adult</label>
                                        <input type="number" size="6" min="1" max="10000" name="price[]" class="form-control" placeholder="Price" value="{{ $route->allTicketTypeOfRoute[0]->pivot->price }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="price">Child</label>
                                        <input type="number" size="6" min="1" max="10000" name="price[]" class="form-control" placeholder="Price" value="{{ $route->allTicketTypeOfRoute[1]->pivot->price }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="price">Infant</label>
                                        <input type="number" size="6" min="1" max="10000" name="price[]" class="form-control" placeholder="Price" value="{{ $route->allTicketTypeOfRoute[2]->pivot->price }}" required>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                      <div class="form-buttons-w formBtnPosition">
                        <button class="btn btn-primary" type="submit">Submit</button>
                        <a class="btn btn-outline-primary" href="/admin/user/{{$user->id}}/profile/my-route/">Back</a>
                      </div>
                   </form>
                </div>
             </div>
        </div>
    </div>

    @section('js_my_route_edit_select')
        <script defer>
            $(document).ready(function() {
                $('.search-single').select2();
            });
        </script>
    @endsection

@endsection