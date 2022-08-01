<?php

namespace App\Policies;

use App\Models\User;
use App\Models\VesselType;
use Illuminate\Auth\Access\HandlesAuthorization;

class VesselTypePolicy
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
     * @param  \App\Models\VesselType  $vesselType
     * @return mixed
     */
    public function view(User $user, VesselType $vesselType)
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
        if($user->roles->contains('slug', 'staff' || 'slug', 'merchant')){//if user has staff or merchant role then he can create
            return true;
        }elseif($user->permissions->contains('slug', 'create-vessel-type')){//if user has permission to create vessel type then he can create
            return true;
        }
        return false;
    }


    /**
     * edit function
     *
     * @param User $user
     * @param VesselType $vesselType
     * @return void
     */
    public function edit(User $user, VesselType $vesselType)
    {   
        if($user->roles->contains('slug', 'staff')){//if user has staff role then he can edit
            return true;
        }elseif($user->permissions->contains('slug', 'edit-vessel-type')){//if user has permission to edit vessel type then he can edit
            return true;
        }
        return false;
    }


    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\VesselType  $vesselType
     * @return mixed
     */
    public function update(User $user, VesselType $vesselType)
    {
        if($user->roles->contains('slug', 'staff')){//if user has staff role then he can update
            return true;
        }elseif($user->permissions->contains('slug', 'edit-vessel-type')){//if user has permission to edit vessel type then he can update
            return true;
        }

        return false;//if they don't have role or permission then return false
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\VesselType  $vesselType
     * @return mixed
     */
    public function delete(User $user, VesselType $vesselType)
    {
        if($user->roles->contains('slug', 'staff')){//if user has staff role then he can delete
            return true;
        }elseif($user->permissions->contains('slug', 'delete-vessel-type')){//if user has permission to delete vessel type then he can delete
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\VesselType  $vesselType
     * @return mixed
     */
    public function restore(User $user, VesselType $vesselType)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\VesselType  $vesselType
     * @return mixed
     */
    public function forceDelete(User $user, VesselType $vesselType)
    {
        //
    }
}
