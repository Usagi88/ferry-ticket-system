@extends('admin.layouts.dashboard')

@section('content')

    <div class="container">       
        <div class="card">
            <div class="card-header">
                <h3>User ID: {{$schedule->user_id}}</h3> 
                <h3>Route ID: {{$schedule->route_id}}</h4> 
                <h4>Vessel ID: {{$schedule->vessel_id}}</h4>
                <h4>Schedule Date: {{$schedule->schedule_date}}</h4>
            </div>
            <div class="card-footer">
                <a href="{{ url()->previous() }}" class="btn btn-primary">Go Back</a>
            </div>
        </div>
    </div>

@endsection