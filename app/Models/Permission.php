<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    /**
     * roles relationship
     * Many to Many relationship
     * returns roles of permission
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'permission_roles');
    }

    
}
