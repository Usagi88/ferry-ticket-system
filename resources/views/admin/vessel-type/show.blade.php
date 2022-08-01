@extends('admin.layouts.dashboard')

@section('content')

    <div class="container">       
        <div class="card">
            <div class="card-header">
                <h3>Name: {{$vesselType->name}}</h3> 
                <h4>Description: {{$vesselType->description}}</h4>
            </div>
            <div class="card-footer">
                <a href="{{ url()->previous() }}" class="btn btn-primary">Go Back</a>
            </div>
        </div>
    </div>

@endsection