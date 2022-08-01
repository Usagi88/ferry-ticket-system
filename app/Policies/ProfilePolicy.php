<?php

namespace App\Policies;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProfilePolicy
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
        
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Profile  $profile
     * @return mixed
     */
    public function view(User $user, Profile $profile)
    {
        
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Profile  $profile
     * @return mixed
     */
    public function update(User $user, Profile $profile)
    {
        if($profile->user_id == $user->id){//if profile's user id matches user id then he can update
            return true;
        }elseif($user->roles->contains('slug', 'staff')){//if user has staff role then he can update
            return true;
        }elseif($user->permissions->contains('slug', 'edit-profile')){//if user has permission to edit profile then he can update
            return true;
        }

        return false;//if they don't have same id or role or permission then return false
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Profile  $profile
     * @return mixed
     */
    public function delete(User $user, Profile $profile)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Profile  $profile
     * @return mixed
     */
    public function restore(User $user, Profile $profile)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Profile  $profile
     * @return mixed
     */
    public function forceDelete(User $user, Profile $profile)
    {
        //
    }


    public function show_vessel_assign_to_users_card(User $user)
    {
        if($user->roles->contains('slug', 'staff') || $user->roles->contains('slug', 'agent')){//if user has staff or merchant role then he can update
            return true;
            //haven't made this permission yet
        }elseif($user->permissions->contains('slug', 'view-vessels-assign-to-user')){//if user has permission to view vessels assigned to user then he can view
            return true;
        }

        return false;//if they don't have same id or role or permission then return false
    }

    public function show_assigned_vessels_card(User $user)
    {
        if($user->roles->contains('slug', 'staff') || $user->roles->contains('slug', 'merchant')){//if user has staff or merchant role then he can update
            return true;
            //haven't made this permission yet
        }elseif($user->permissions->contains('slug', 'view-assign-vessel')){//if user has permission to view assign then he can view
            return true;
        }

        return false;//if they don't have same id or role or permission then return false
    }

    public function show_island_assign_to_users_card(User $user)
    {
        if($user->roles->contains('slug', 'staff') || $user->roles->contains('slug', 'agent')){//if user has staff or merchant role then he can update
            return true;
            //haven't made this permission yet
        }elseif($user->permissions->contains('slug', 'view-islands-assign-to-user')){//if user has permission to view islands assigned to user then he can view
            return true;
        }

        return false;//if they don't have same id or role or permission then return false
    }
    
}
