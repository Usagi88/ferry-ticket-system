@extends('admin.layouts.dashboard')

@section('content')

    <div class="container">       
        <div class="card">
            <div class="card-header">
                <h3>Booking ID: {{$booking->id}}</h3> 
                <h4>User ID: {{$booking->user_id}}</h4>
                <h4>Vessel ID: {{$booking->vessel_id}}</h4>
                <h4>Schedule ID: {{$booking->schedule_id}}</h4>
                <h4>Ticket Type: {{$booking->ticket_type_id}}</h4>
                <h4>Quantity: {{$booking->ticket_quantity}}</h4>
                <h4>Total: {{$booking->total}}</h4>
                <h4>Status: {{$booking->booking_status_id}}</h4>
            </div>
            <div class="card-footer">
                <a href="{{ url()->previous() }}" class="btn btn-primary">Go Back</a>
            </div>
        </div>
    </div>

@endsection