@extends('admin.layouts.dashboard')

@section('content')

    @if ($errors->any())
        <div class="alert alert-danger" route="alert">
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
                   <form method="POST" action="/admin/user/{{$user->id}}/profile/my-route">
                        {{ csrf_field() }}
                        <h5 class="form-header">Create Route</h5>
                        <div class="form-desc">Create route with ticket prices</div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-4" for="">Origin</label>
                            <div class="col-sm-8"><input data-nametag="tags-manual-suggestions" name="origin" class="form-control" id="origin_id" value="{{ old('origin') }}" required></div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-4" for="">Destination</label>
                            <div class="col-sm-8"><input data-nametag="tags-manual-suggestions2" name="destination" class="form-control" id="destination_id" value="{{ old('destination') }}" required></div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-4" for="">Duration</label>
                            <div class="col-sm-8"><input type="number" min="1" max="10000" name="duration" class="form-control" id="duration" placeholder="Duration" value="{{ old('duration') }}" required></div>
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
                                        <input type="number" size="6" min="1" max="10000" name="price[]" class="form-control" placeholder="Price" value="{{ old('price') }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="price">Child</label>
                                        <input type="number" size="6" min="1" max="10000" name="price[]" class="form-control" placeholder="Price" value="{{ old('price') }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="price">Infant</label>
                                        <input type="number" size="6" min="1" max="10000" name="price[]" class="form-control" placeholder="Price" value="{{ old('price') }}" required>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                      <div class="form-buttons-w formBtnPosition">
                        <button class="btn btn-primary" type="submit">Submit</button>
                        <a class="btn btn-outline-primary" href="/admin/user/{{$user->id}}/profile/my-route">Back</a>
                      </div>
                   </form>
                </div>
             </div>
        </div>
    </div>

    @section('css_my_route_create_tags')
        <link rel="stylesheet" href="{{ asset('temp/vendor/css/admin/bootstrap-tagsinput.css') }}">
    @endsection

    @section('js_route_create_tagify')
        <script>
            var islandNames = @json($islandNames);
            var input = document.querySelector('input[data-nametag=tags-manual-suggestions]'),
                // init Tagify script on the above inputs
                
                tagify = new Tagify(input, {
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
                tagify.on("dropdown:show", onSuggestionsListUpdate)
                    .on("dropdown:hide", onSuggestionsListHide)
                    .on('dropdown:scroll', onDropdownScroll)

                // ES2015 argument destructuring
                function onSuggestionsListUpdate({ detail:suggestionsElm }){
                    //console.log(  suggestionsElm  )
                }

                function onSuggestionsListHide(){
                    //console.log("hide dropdown")
                }

                function onDropdownScroll(e){
                    //console.log(e.detail)
                }

            var input2 = document.querySelector('input[data-nametag=tags-manual-suggestions2]'),
            // init Tagify script on the above inputs
            
            tagify2 = new Tagify(input2, {
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
            tagify2.on("dropdown:show", onSuggestionsListUpdate)
                .on("dropdown:hide", onSuggestionsListHide)
                .on('dropdown:scroll', onDropdownScroll)

            // ES2015 argument destructuring
            function onSuggestionsListUpdate({ detail:suggestionsElm }){
                //console.log(  suggestionsElm  )
            }

            function onSuggestionsListHide(){
                //console.log("hide dropdown")
            }

            function onDropdownScroll(e){
                //console.log(e.detail)
            }

        </script>
    @endsection

@endsection