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
                   <form method="POST" action="/admin/island/{{ $island->id }}">
                        @method('PATCH')
                        @csrf()
                        <h5 class="form-header">Edit Island</h5>
                        <div class="form-desc">Edit Island or Atoll</div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-4" for=""> Atoll</label>
                            <div class="col-sm-8"><input class="form-control" name="atoll" type="text" placeholder="Atoll" value="{{ $island->atoll }}" required></div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-4" for=""> Island Name</label>
                            <div class="col-sm-8"><input class="form-control" name="name" type="text" placeholder="Island" value="{{ $island->name }}" required></div>
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
    @section('js_island_edit_select')
        <script>
            $(document).ready(function() {
                $('.search-single').select2();
            });
        </script>
    @endsection

@endsection