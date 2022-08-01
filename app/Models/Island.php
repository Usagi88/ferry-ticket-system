<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Island extends Model
{
    use HasFactory;

    /**
     * assignedAgents relationship
     * Many to Many relationship
     * returns assignedAgents of Island
     */
    public function assignedAgents()
    {
        //return $this->belongsToMany(User::class,'user_vessels','user_id','vessel_id');
        return $this->belongsToMany(User::class,'agent_islands','user_id','island_id')->withPivot('id');
    }
}
