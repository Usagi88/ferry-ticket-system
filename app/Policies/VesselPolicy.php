<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vessel;
use Illuminate\Auth\Access\HandlesAuthorization;

class VesselPolicy
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
        if($user->roles->contains('slug', 'staff')){//if user has staff role then he can edit
            return true;
        }elseif($user->permissions->contains('slug', 'create-assign-vessel')){//if user has permission to create assign vessel then he can edit
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Vessel  $vessel
     * @return mixed
     */
    public function view(User $user, Vessel $vessel)
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
        if($user->roles->contains('slug', 'staff') || $user->roles->contains('slug', 'merchant')){//if user has staff or merchant role then he can create
            return true;
        }elseif($user->permissions->contains('slug', 'create-vessel')){//if user has permission to create vessel then he can create
            return true;
        }
        return false;
    }

    /**
     * edit function
     *
     * @param User $user
     * @param Vessel $vessel
     * @return void
     */
    public function edit(User $user, Vessel $vessel)
    {   
        if($vessel->owner_id == $user->id){//if vessel's user id matches user id then he can edit
            return true;
        }elseif($user->roles->contains('slug', 'staff')){//if user has staff role then he can edit
            return true;
        }elseif($user->permissions->contains('slug', 'edit-vessel')){//if user has permission to edit vessel then he can edit
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Vessel  $vessel
     * @return mixed
     */
    public function update(User $user, Vessel $vessel)
    {
        if($vessel->owner_id == $user->id){//if vessel's user id matches user id then he can update
            return true;
        }elseif($user->roles->contains('slug', 'staff')){//if user has staff role then he can update
            return true;
        }elseif($user->permissions->contains('slug', 'edit-vessel')){//if user has permission to edit vessel then he can update
            return true;
        }

        return false;//if they don't have role or permission then return false
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Vessel  $vessel
     * @return mixed
     */
    public function delete(User $user, Vessel $vessel)
    {
        if($vessel->owner_id == $user->id){//if vessel's user id matches user id then he can delete
            return true;
        }elseif($user->roles->contains('slug', 'staff')){//if user has staff role then he can delete
            return true;
        }elseif($user->permissions->contains('slug', 'delete-vessel')){//if user has permission to delete vessel then he can delete
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Vessel  $vessel
     * @return mixed
     */
    public function restore(User $user, Vessel $vessel)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Vessel  $vessel
     * @return mixed
     */
    public function forceDelete(User $user, Vessel $vessel)
    {
        //
    }


    public function assignEdit(User $user, Vessel $vessel)
    {
        if($vessel->owner_id == $user->id){//if vessel's user id matches user id then he can edit
            return true;
        }elseif($user->roles->contains('slug', 'staff')){//if user has staff role then he can edit
            return true;
        }elseif($user->permissions->contains('slug', 'edit-assign-vessel')){//if user has permission to edit assign vessel then he can edit
            return true;
        }
        return false;
    }

    
}
