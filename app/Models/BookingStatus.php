<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingStatus extends Model
{
    use HasFactory;

    /**
     * bookings relationship
     * One to Many relationship
     * returns all bookings that has booking status
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
