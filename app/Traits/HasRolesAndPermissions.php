<?php

namespace App\Traits;

use App\Models\Role;
use App\Models\Permission;

trait HasRolesAndPermissions
{
    /**
     * isAdmin function
     *
     * @return boolean
     * This is to check if user is admin or not.
     */
    public function isAdmin()
    {
        if($this->roles->contains('slug', 'admin')){
            return true;
        }
    }

    /**
     * roles function
     *
     * @return void
     * This returns all roles user has
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_users');
    }

    /**
     * permissions function
     *
     * @return void
     * this returns all permission user has
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_users');
    }

    /**
     * hasRole function
     *
     * @param [type] $role
     * @return boolean
     * This checks if user has "this" role
     */
    public function hasRole($role)
    {
        if ($this->roles->contains('slug', $role)){
            return true;
        }
        
        return false;
    }


    public function hasPermission($permission)
    {
        if ($this->permissions->contains('slug', $permission)){
            return true;
        }
        
        return false;
    }

}