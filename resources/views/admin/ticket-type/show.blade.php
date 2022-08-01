@extends('admin.layouts.dashboard')

@section('content')

    <div class="container">       
        <div class="card">
            <div class="card-header">
                <h3>Name: {{$ticketType->name}}</h3> 
                <h4>Description: {{$ticketType->description}}</h4>
            </div>
            <div class="card-footer">
                <a href="{{ url()->previous() }}" class="btn btn-primary">Go Back</a>
            </div>
        </div>
    </div>

@endsection