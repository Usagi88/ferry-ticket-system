<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    /**
     * permissions relationship
     * Many to many relationship
     * returns permissions of a role
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_roles');
    }
}
