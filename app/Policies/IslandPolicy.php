<?php

namespace App\Policies;

use App\Models\Island;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class IslandPolicy
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
     * @param  \App\Models\Island  $island
     * @return mixed
     */
    public function view(User $user, Island $island)
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
        }elseif($user->permissions->contains('slug', 'create-island')){//if user has permission to create island then he can create
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
        }elseif($user->permissions->contains('slug', 'edit-island')){//if user has permission to edit island then he can edit
            return true;
        }
        return false;
    }
    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Island  $island
     * @return mixed
     */
    public function update(User $user, Island $island)
    {
        if($user->roles->contains('slug', 'staff')){//if user has staff role then he can update
            return true;
        }elseif($user->permissions->contains('slug', 'edit-island')){//if user has permission to edit island then he can update
            return true;
        }

        return false;//if they don't have role or permission then return false
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function delete(User $user)
    {
        if($user->roles->contains('slug', 'staff')){//if user has staff role then he can delete
            return true;
        }elseif($user->permissions->contains('slug', 'delete-island')){//if user has permission to delete island then he can delete
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Island  $island
     * @return mixed
     */
    public function restore(User $user, Island $island)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Island  $island
     * @return mixed
     */
    public function forceDelete(User $user, Island $island)
    {
        //
    }


    public function assignIslandCreate(User $user)
    {
        $pageUserID = request()->route()->parameter('user');
        if($user->id == $pageUserID->id){//if vessel's user id matches user id then he can delete
            return true;
        }elseif($user->roles->contains('slug', 'staff')){//if user has staff then he can edit
            return true;
            //haven't made permission yet
        }elseif($user->permissions->contains('slug', 'create-agent-island')){//if user has permission to create agent island then he can edit
            return true;
        }
        return false;
    }

    public function assignIslandEdit(User $user, Island $island)
    {
        if($island->pivot->user_id == $user->id){//if island's pivot's user id matches user id then he can edit
            return true;
        }elseif($user->roles->contains('slug', 'staff')){//if user has staff role then he can edit
            return true;
        }elseif($user->permissions->contains('slug', 'edit-agent-island')){//if user has permission to edit agent island then he can edit
            return true;
        }
        return false;
    }

    
}
