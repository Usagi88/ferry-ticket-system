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

    <div class="assignVesselCreateGridContainer">
        <div class="gridAssignVesselCreateFirstRow">
            <div class="element-wrapper">
                <div class="element-box">
                   <form method="POST" action="/admin/island/agent-island">
                        {{ csrf_field() }}
                        <h5 class="form-header">Assign Agent</h5>
                        <div class="form-desc">Assign an Agent to an Island</div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-4" for="">Select Agent</label>
                            <div class="col-sm-8">
                                <select class="search-single custom-select" name="user_id" required>
                                    <optgroup label="User">
                                        @if (!$users->isEmpty() && $users != null )
                                            @foreach ($users as $user)
                                                <option value="{{$user->id}}">{{$user->id}} ● {{$user->first_name}} {{$user->last_name}}</option>
                                            @endforeach
                                        @endif
                                    </optgroup>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-4" for="">Select Island</label>
                            <div class="col-sm-8">
                                <select class="search-single custom-select" name="island_id" required>
                                    <optgroup label="Island">
                                        @if (!$islands->isEmpty() && $islands != null )
                                            @foreach ($islands as $island)
                                                <option value="{{ $island['id'] }}">{{ $island['id'] }} ● {{ $island['atoll'] }}.{{ $island['name'] }}</option>
                                            @endforeach
                                        @endif
                                    </optgroup>
                                </select>
                            </div>
                        </div>
                        
                      <div class="form-buttons-w formBtnPosition">
                        <button class="btn btn-primary" type="submit">Submit</button>
                        <a class="btn btn-outline-primary" href="/admin/island/agent-island/">Back</a>
                      </div>
                   </form>
                </div>
             </div>
        </div>
    </div>
    @section('js_agent_island_create_select')
        <script>
            $(document).ready(function() {
                $('.search-single').select2();
            });
        </script>
    @endsection

@endsection
