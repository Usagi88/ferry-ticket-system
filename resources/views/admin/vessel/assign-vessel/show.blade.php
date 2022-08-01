@extends('admin.layouts.dashboard')

@section('content')

    <div class="container">       
        <div class="card">
            <div class="card-header">
                <h3>User ID: {{$assign[0]->user_id}}</h3> 
                <h4>Vessel ID: {{$assign[0]->vessel_id}}</h4> 
            </div>
            <div class="card-footer">
                <a href="{{ url()->previous() }}" class="btn btn-primary">Go Back</a>
            </div>
        </div>
    </div>

@endsection