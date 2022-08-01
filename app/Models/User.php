<?php

namespace App\Models;

use App\Traits\HasRolesAndPermissions;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasRolesAndPermissions;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'username',
        'first_name',
        'last_name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    /**
     * Profile relationship
     * One to One relationship
     * returns profile of a user
     */
    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    /**
     * Schedules relationship
     * One to Many relationship
     * returns schedules of a user
     */
    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    /**
     * Bookings relationship
     * One to Many relationship
     * returns bookings of a user
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Routes relationship
     * One to Many relationship
     * returns routes of a user
     */
    public function routes()
    {
        return $this->hasMany(Route::class);
    }

    /**
     * ownedVessels relationship
     * One to Many relationship
     * returns owned vessels of a user
     */
    public function ownedVessels()
    {
        return $this->hasMany(Vessel::class, 'owner_id');
    }

    /**
     * vesselsAssignedToUser relationship
     * One to Many relationship
     * returns vessels assigned to user
     */
    public function vesselsAssignedToUser()
    {
        //return $this->belongsToMany(Vessel::class,'user_vessels','user_id','vessel_id');
        return $this->belongsToMany(Vessel::class)->withPivot('id');
    }


    public function assignedVessels()
    {
        //return $this->belongsToMany(Vessel::class,'user_vessels','user_id','vessel_id');
        return $this->belongsToMany(Vessel::class,'user_vessel','owner_id','vessel_id')->withPivot('id');
    }

    /**
     * islandsAssignedToUser relationship
     * One to Many relationship
     * returns assignedIslands of a user
     */
    public function islandsAssignedToUser()
    {
        //return $this->belongsToMany(Vessel::class,'user_vessels','user_id','vessel_id');
        return $this->belongsToMany(Island::class,'agent_islands','user_id','island_id')->withPivot('id');
    }

    /**
     * fcmTokens relationship
     * One to Many relationship
     * returns fcmTokens of a user
     */
    public function fcmTokens()
    {
        //return $this->belongsToMany(Vessel::class,'user_vessels','user_id','vessel_id');
        return $this->belongsToMany(FcmToken::class,'user_fcm_tokens','user_id','fcm_token_id')->withPivot('id');
    }

    public function ticket_types()
    {
        return $this->belongsToMany(TicketType::class,'user_ticket_types','user_id','custom_ticket_type_id')->withPivot('id');
    }
}
