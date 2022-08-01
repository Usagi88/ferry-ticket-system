<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vessel extends Model
{
    use HasFactory;


    /**
     * Vessel type relationship
     * One to Many relationship
     * returns vessel type of a vessel
     */
    public function vessel_type()
    {
        return $this->belongsTo(VesselType::class);
    }

    /**
     * Schedules relationship
     * One to Many relationship
     * returns schedules of a vessel
     */
    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    /**
     * Bookings relationship
     * One to Many relationship
     * returns bookings of a vessel
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Owner relationship
     * One to Many relationship
     * returns owner of a vessel
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Assignees relationship
     * One to Many relationship
     * returns assignees of a vessel
     */
    public function assignees()
    {
        //return $this->belongsToMany(User::class,'user_vessels','user_id','vessel_id');
        return $this->belongsToMany(User::class);
    }


    //public function vesselAssigned()
    //{
        //return $this->belongsToMany(Vessel::class,'user_vessels','user_id','vessel_id');
    //    return $this->belongsToMany(User::class,'user_vessel','vessel_id','user_id')->withPivot('id','owner_id');
    //}
    
}
