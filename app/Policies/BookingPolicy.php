<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BookingPolicy
{
    use HandlesAuthorization;

    /**
     * Perform pre-authorization checks.
     *
     * @param  \App\Models\User  $user
     * @param  string  $ability
     * @return void|bool
     */
    public function before(User $user, $ability)
    {
        if ($user->isAdmin()) {
            return true;
        }
    }

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Booking  $booking
     * @return mixed
     */
    public function view(User $user, Booking $booking)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        if($user->roles->contains('slug', 'staff')){//if user has staff then he can create
            return true;
        }elseif($user->permissions->contains('slug', 'create-booking')){//if user has permission to create booking then he can create
            return true;
        }
        return false;
    }

    /**
     * edit function
     *
     * @param User $user
     * @return void
     */
    public function edit(User $user)
    {   
        if($user->roles->contains('slug', 'staff')){//if user has staff role then he can edit
            return true;
        }elseif($user->permissions->contains('slug', 'edit-booking')){//if user has permission to edit booking then he can edit
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Booking  $booking
     * @return mixed
     */
    public function update(User $user, Booking $booking)
    {
        if($user->roles->contains('slug', 'staff')){//if user has staff role then he can update
            return true;
        }elseif($user->permissions->contains('slug', 'edit-booking')){//if user has permission to edit booking then he can update
            return true;
        }

        return false;//if they don't have role or permission then return false
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Booking  $booking
     * @return mixed
     */
    public function delete(User $user, Booking $booking)
    {
        if($user->roles->contains('slug', 'staff')){//if user has staff role then he can delete
            return true;
        }elseif($user->permissions->contains('slug', 'delete-booking')){//if user has permission to delete booking then he can delete
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Booking  $booking
     * @return mixed
     */
    public function restore(User $user, Booking $booking)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Booking  $booking
     * @return mixed
     */
    public function forceDelete(User $user, Booking $booking)
    {
        //
    }
}
