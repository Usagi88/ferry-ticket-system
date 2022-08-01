@extends('admin.layouts.dashboard')

@section('content')

    <div class="container">       
        <div class="card">
            <div class="card-header">
                <h3>Origin: {{$route->origin}}</h3> 
                <h3>Destination: {{$route->destination}}</h3>
                <h4>Duration: {{$route->duration}}</h4>
            </div>
            <div class="card-footer">
                <a href="{{ url()->previous() }}" class="btn btn-primary">Go Back</a>
            </div>
        </div>
    </div>

@endsection