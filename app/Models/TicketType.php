<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketType extends Model
{
    use HasFactory;

    /**
     * Route relationship
     * Many to Many relationship
     * returns routes of a ticket type
     */
    public function routes()
    {
        return $this->hasMany(Route::class);
    }

    /**
     * Route relationship
     * Many to Many relationship
     * returns bookings of a ticket type
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function allRoutesOfTicketType()
    {
        return $this->belongsToMany(TicketType::class)->withPivot('id','price','user_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class,'user_ticket_types','custom_ticket_type_id','user_id')->withPivot('id');
    }
}
