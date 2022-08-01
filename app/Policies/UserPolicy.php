<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
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
     * @param  \App\Models\User  $model
     * @return mixed
     */
    public function view(User $user, User $model)
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
        }elseif($user->permissions->contains('slug', 'create-user')){//if user has permission to create user then he can create
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
        }elseif($user->permissions->contains('slug', 'edit-user')){//if user has permission to edit user then he can edit
            return true;
        }
        return false;
    }


    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return mixed
     */
    public function update(User $user, User $model)
    {
        if($user->roles->contains('slug', 'staff')){//if user has staff role then he can update
            return true;
        }elseif($user->permissions->contains('slug', 'edit-user')){//if user has permission to edit user then he can update
            return true;
        }

        return false;//if they don't have role or permission then return false
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return mixed
     */
    public function delete(User $user, User $model)
    {
        if($user->roles->contains('slug', 'staff')){//if user has staff role then he can delete
            return true;
        }elseif($user->permissions->contains('slug', 'delete-user')){//if user has permission to delete user then he can delete
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return mixed
     */
    public function restore(User $user, User $model)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return mixed
     */
    public function forceDelete(User $user, User $model)
    {
        //
    }
}
