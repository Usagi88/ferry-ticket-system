<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    /**
     * Booking status relationship
     * One to Many relationship
     * returns booking status of booking
     */
    public function booking_status()
    {
        return $this->belongsTo(BookingStatus::class);
    }

    /**
     * Schedule relationship
     * One to Many relationship
     * returns schedule of booking
     */
    
    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    /**
     * Vessel relationship
     * One to Many relationship
     * returns vessel of booking
     */
    public function vessel()
    {
        return $this->belongsTo(Vessel::class);
    }

    /**
     * Ticket type relationship
     * One to Many relationship
     * returns ticket type of booking
     */
    public function ticket_type()
    {
        return $this->belongsTo(TicketType::class);
    }

    /**
     * user relationship
     * One to Many relationship
     * returns users of booking
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
