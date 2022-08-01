@extends('admin.layouts.dashboard')

@section('content')

    <div class="container">       
        <div class="card">
            <div class="card-header">
                <h3>User ID: {{$myAssign[0]->user_id}}</h3> 
                <h4>Island ID: {{$myAssign[0]->island_id}}</h4> 
            </div>
            <div class="card-footer">
                <a href="{{ url()->previous() }}" class="btn btn-primary">Go Back</a>
            </div>
        </div>
    </div>

@endsection