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
                   <form method="POST" action="/admin/island">
                        {{ csrf_field() }}
                        <h5 class="form-header">Create Island</h5>
                        <div class="form-desc">Create Island with Atoll</div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-4" for=""> Atoll</label>
                            <div class="col-sm-8"><input class="form-control" name="atoll" type="text" placeholder="Atoll" value="{{ old('atoll') }}" required></div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-4" for=""> Island Name</label>
                            <div class="col-sm-8"><input class="form-control" name="name" type="text" placeholder="Island" value="{{ old('name') }}" required></div>
                        </div>
                        
                      <div class="form-buttons-w formBtnPosition">
                        <button class="btn btn-primary" type="submit">Submit</button>
                        <a class="btn btn-outline-primary" href="/admin/island">Back</a>
                      </div>
                   </form>
                </div>
             </div>
        </div>
    </div>
    @section('js_island_create_select')
        <script>
            $(document).ready(function() {
                $('.search-single').select2();
            });
        </script>
    @endsection

@endsection
