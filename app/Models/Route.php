<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    use HasFactory;

    protected $casts = [
        'data' => 'array'
    ];
    /**
     * allTicketTypeOfRoute relationship
     * Many to Many relationship
     * returns ticket type with pivot data of a route
     * pivot contains price, user id, and id.
     */
    public function allTicketTypeOfRoute()
    {
        return $this->belongsToMany(TicketType::class)->withPivot('id','price','user_id');
    }

    /**
     * Schedule relationship
     * One to Many relationship
     * returns a schedule of a route
     */
    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    /**
     * Ticket type relationship
     * One to Many relationship
     * returns a ticket type of a route
     */
    public function ticket_type()
    {
        return $this->belongsTo(TicketType::class);
    }

    /**
     * User relationship
     * One to Many relationship
     * returns a user of a route
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
