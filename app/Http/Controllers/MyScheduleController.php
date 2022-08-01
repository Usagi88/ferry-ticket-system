<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Schedule;
use App\Models\Vessel;
use App\Models\Route;
use App\Models\TicketType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
use Throwable;
use Illuminate\Support\Facades\Cache;

class MyScheduleController extends Controller
{
    /**
     * Auth Middleware. If the user isn't logged in then redirect back to login
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * 
     * This method is used to show all data
     */
    public function index(User $user)
    {
        $mySchedulesIndex = Cache::remember('myScheduleIndexID'.$user->id, 60, function() use($user){//Right now we are doing caching for 60 seconds
            //return Schedule::where('user_id',$user->id)->with('user','route','vessel')->get();//get user's schedule with user,route,vessel relationship
            $schedules =  Schedule::where('user_id',$user->id)->with('route.allTicketTypeOfRoute','vessel','user')->get();//get all schedules with route's ticket type,vessel relationship
            $ticket_types = TicketType::get();
            return compact('schedules','ticket_types');
        });
        $schedules = $mySchedulesIndex["schedules"];
        $ticket_types = $mySchedulesIndex["ticket_types"];
        return view('admin.user.profile.my-schedule.index', compact('user','schedules','ticket_types'));//sending the variables to the view
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     * 
     * This method is used to create (my) schedule
     */
    public function create(User $user, Request $request)
    {
        if($request->ajax()){//if it is a ajax request
            //find route where route code and ticket id is same as request         
            $route_ids = Route::where('route_code', $request->routeCodeID)->with('ticket_type')->get();
            return $route_ids;//return list of price
        }
        $scheduleCreate = Cache::remember('myScheduleCreate', 60, function () {//Right now we are doing caching for 60 seconds
            $vessels = Vessel::get();//get all vessels
            $routes = Route::get();//get all routes
            return compact('vessels','routes');//returning it as variables
            
        });
        $vessels = $scheduleCreate["vessels"];//accessing the variable and initializing 
        $routes = $scheduleCreate["routes"];
        return view('admin.user.profile.my-schedule.create', compact('vessels','routes','user'));//sending the variables to the view

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * 
     * This method is what happens when we submit in create page
     */
    public function store(Request $request, User $user)
    {
        DB::beginTransaction();//start transaction, This is to have control over rollbacks and commits

        $request->validate([//validating the data that is being passed through. If an error occurs it will redirect back to page
            'route_id'=>'required',
            'vessel_id'=>'required',
            'schedule_date'=>'required'
        ]);

        try {
            $schedule = new Schedule;//making a new schedule
            $schedule->user_id = $user->id;
            $schedule->route_id = $request->route_id;//initializing it with what user entered
            $schedule->vessel_id = $request->vessel_id;
            $schedule->available_seats = Vessel::where('id',$request->vessel_id)->first()->seat_capacity;
            $schedule->schedule_date = $request->schedule_date;
            $schedule->save();//save

            DB::commit();//This means nothing went wrong so we can commit/save to the database

            /**
             * For individual user pages we are using user's id to define the cache. If everyone used the same cache then the values will be the same
             * If there is a change then we must forget the cache so that the changes will be seen on these pages.
             */
            Cache::forget('myScheduleIndexID'.$user->id);//forget the cache so we see the changes.
            Cache::forget('scheduleIndex');

        } catch(Exception $e) {
            DB::rollBack();//Something went wrong so we undo the transaction. It won't be saved to database
            return redirect("/admin/user/{$user->id}/profile/my-schedule/create")->withErrors([ $e->getMessage() ])->withInput();//if it is an sql error then it will show the entire sql error. If we want we can put custom message
        } catch(Throwable $e) {
            DB::rollBack();//same thing here
            return redirect("/admin/user/{$user->id}/profile/my-schedule/create")->withErrors([ $e->getMessage() ])->withInput();
        }
        
        return redirect("/admin/user/{$user->id}/profile/my-schedule/")->with('success', 'Schedule Created!');//if no errors then redirect back to page with alert
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     * 
     * This method is used to show data. Individual data.
     */
    public function show(User $user, Schedule $schedule)
    {
        return view('admin.user.profile.my-schedule.show', compact('schedule'));//sending the variable to the view
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     * 
     * This method is used to edit (my) schedule
     */
    public function edit(User $user, Schedule $schedule)
    {
        $scheduleEdit = Cache::remember('myScheduleEdit', 60, function () {//Right now we are doing caching for 60 seconds
            $vessels = Vessel::get();//get all vessels
            $routes = Route::get();//get all routes
            return compact('vessels','routes');//returning it as variables
        });
        $vessels = $scheduleEdit["vessels"];//accessing the variable and initializing 
        $routes = $scheduleEdit["routes"];
        return view('admin.user.profile.my-schedule.edit',compact('schedule','vessels','routes','user'));//sending the variables to the view
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     * 
     * This method is what happens when we submit in edit page
     */
    public function update(Request $request, User $user, Schedule $schedule)
    {
        DB::beginTransaction();//start transaction, This is to have control over rollbacks and commits

        $request->validate([//validating the data that is being passed through. If an error occurs it will redirect back to page
            'route_id'=>'required',
            'vessel_id'=>'required',
            'schedule_date'=>'required'
        ]);

        try {
            $schedule->user_id = $user->id;//initialzing with what user entered
            $schedule->route_id = $request->route_id;
            $schedule->vessel_id = $request->vessel_id;
            $schedule->available_seats = Vessel::where('id',$request->vessel_id)->first()->seat_capacity;
            $schedule->schedule_date = $request->schedule_date;
            $schedule->save();//save

            DB::commit();//This means nothing went wrong so we can commit/save to the database

            /**
             * For individual user pages we are using user's id to define the cache. If everyone used the same cache then the values will be the same
             * If there is a change then we must forget the cache so that the changes will be seen on these pages.
             */
            Cache::forget('myScheduleIndexID'.$user->id);//forget the cache so we see the changes.
            Cache::forget('scheduleIndex');//forget the cache so we see the changes.

        } catch(Exception $e) {
            DB::rollBack();//Something went wrong so we undo the transaction. It won't be saved to database
            return redirect("/admin/user/{$user->id}/profile/my-schedule/{$schedule->id}/edit")->withErrors([ $e->getMessage() ])->withInput();//if it is an sql error then it will show the entire sql error. If we want we can put custom message
        } catch(Throwable $e) {
            DB::rollBack();//same thing here
            return redirect("/admin/user/{$user->id}/profile/my-schedule/{$schedule->id}/edit")->withErrors([ $e->getMessage() ])->withInput();
        }
        
        return redirect("/admin/user/{$user->id}/profile/my-schedule/")->with('Edit-success','Schedule Updated!');//if no errors then redirect back to page with alert
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     * 
     * This method is used for deleting (my) schedule
     */
    public function destroy(User $user, Schedule $schedule)
    {
        DB::beginTransaction();//start transaction, This is to have control over rollbacks and commits
        try {
            $schedule->delete();//deleting it

            DB::commit();//This means nothing went wrong so we can commit/save to the database

            /**
             * For individual user pages we are using user's id to define the cache. If everyone used the same cache then the values will be the same
             * If there is a change then we must forget the cache so that the changes will be seen on these pages.
             */
            Cache::forget('myScheduleIndexID'.$user->id);//forget the cache so we see the changes.
            Cache::forget('scheduleIndex');
            
        } catch(Exception $e) {
            DB::rollBack();//Something went wrong so we undo the transaction. It won't be saved to database
            return redirect("/admin/user/{$user->id}/profile/my-schedule/")->withErrors([ $e->getMessage() ])->withInput();//if it is an sql error then it will show the entire sql error. If we want we can put custom message
        } catch(Throwable $e) {
            DB::rollBack();//same thing here
            return redirect("/admin/user/{$user->id}/profile/my-schedule/")->withErrors([ $e->getMessage() ])->withInput();
        }
        
        return redirect("/admin/user/{$user->id}/profile/my-schedule/")->with('Delete-success','Schedule deleted successfully!');//if no errors then redirect back to page with alert
    }
}
