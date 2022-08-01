@extends('admin.layouts.dashboard')

@section('content')

    <div class="container">       
        <div class="card">
            <div class="card-header">
                <h3>Name: {{$vessel->name}}</h3> 
                <h4>Type: {{$vessel->vessel_type_id}}</h4> 
                <h4>Seat Capacity: {{$vessel->seat_capacity}}</h4>
                <h4>Max Accompanied Cargo: {{$vessel->max_accompanied_cargo}}</h4>
                <h4>Max Unaccompanied Cargo: {{$vessel->max_unaccompanied_cargo}}</h4>
            </div>
            <div class="card-footer">
                <a href="{{ url()->previous() }}" class="btn btn-primary">Go Back</a>
            </div>
        </div>
    </div>

@endsection