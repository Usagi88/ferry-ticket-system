<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Schedule extends Model
{
    use HasFactory;


    public function getStartAttribute($value)
    {
        try {
            if (Carbon::createFromFormat('Y-m-d H:i:s', $value) !== false) {//if time format is is correct. E.g. 2021/8/3 10:00:00
                $dateStart = Carbon::createFromFormat('Y-m-d H:i:s', $value)->format('Y-m-d');
                $timeStart = Carbon::createFromFormat('Y-m-d H:i:s', $value)->format('H:i:s');
                return $this->start = ($timeStart == '00:00:00' ? $dateStart : $value);
            }else{//if it's not then it means there is no time. Value wouldn't show time because it is 00:00:00. So we set it up again with date & time
                $timeStart = '00:00:00';
                $dateStartWithTime = Carbon::parse($value)->startOfDay();//this will contain 00:00:00
                return $this->start = ($timeStart == '00:00:00' ? $dateStartWithTime : $value);
            }
                
        } catch (\Throwable $th) {
           //dd($value);
        }
        
    }

    public function getEndAttribute($value)
    {
        try {
            if (Carbon::createFromFormat('Y-m-d H:i:s', $value) !== false) {//if time format is is correct. E.g. 2021/8/3 10:00:00
                $dateEnd = Carbon::createFromFormat('Y-m-d H:i:s', $value)->format('Y-m-d');
                $timeEnd = Carbon::createFromFormat('Y-m-d H:i:s', $value)->format('H:i:s');
                return $this->end = ($timeEnd == '00:00:00' ? $dateEnd : $value);
            }else{//if it's not then it means there is no time. Value wouldn't show time because it is 00:00:00. So we set it up again with date & time
                $timeEnd = '00:00:00';
                $dateEndWithTime = Carbon::parse($value)->startOfDay();//this will contain 00:00:00
                return $this->end = ($timeEnd == '00:00:00' ? $dateEndWithTime : $value);
            }

        } catch (\Throwable $th) {
           //dd($value);
        }
    }
    /**
     * Route relationship
     * One to Many relationship
     * returns a route of a schedule
     */

    public function route()
    {
        return $this->belongsTo(Route::class);
    }

    /**
     * Route relationship
     * One to Many relationship
     * returns a vessel of a schedule
     */
    public function vessel()
    {
        return $this->belongsTo(Vessel::class);
    }

    /**
     * Route relationship
     * One to Many relationship
     * returns a user of a schedule
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
