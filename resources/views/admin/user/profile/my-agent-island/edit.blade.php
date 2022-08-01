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

    <div class="assignVesselCreateGridContainer">
        <div class="gridAssignVesselCreateFirstRow">
            <div class="element-wrapper">
                <div class="element-box">
                   <form method="POST" action="/admin/user/{{ $user->id }}/profile/my-agent-island/{{$myAssign->id}}">
                        @method('PATCH')
                        @csrf()
                        <h5 class="form-header">Edit Assign Agent</h5>
                        <div class="form-desc">Edit the Island</div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-4" for="">Select Island</label>
                            <div class="col-sm-8">
                                <select class="search-single custom-select" name="island_id" required>
                                    <optgroup label="Island">
                                        @if (!$islands->isEmpty() && $islands != null )
                                            @foreach ($islands as $island)
                                                <option value="{{ $island['id'] }}" {{old('island_id',$myAssign->island_id) == $island->id ? 'selected' : ''}}>{{ $island['id'] }} ● {{ $island['atoll'] }}.{{ $island['name'] }}</option>
                                            @endforeach
                                        @endif
                                    </optgroup>
                                </select>
                            </div>
                        </div>
                        
                      <div class="form-buttons-w formBtnPosition">
                        <button class="btn btn-primary" type="submit">Submit</button>
                        <a class="btn btn-outline-primary" href="/admin/user/{{ $user->id }}/profile/my-agent-island/">Back</a>
                      </div>
                   </form>
                </div>
             </div>
        </div>
    </div>

    @section('js_my_agent_island_edit_select')
        <script>
            $(document).ready(function() {
                $('.search-single').select2();
            });
        </script>
    @endsection

@endsection