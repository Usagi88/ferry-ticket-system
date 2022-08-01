<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VesselType extends Model
{
    use HasFactory;

    /**
     * Vessels relationship
     * One to Many relationship
     * returns vessels of a vessel type
     */
    public function vessels()
    {
        return $this->hasMany(Vessel::class);
    }
}
