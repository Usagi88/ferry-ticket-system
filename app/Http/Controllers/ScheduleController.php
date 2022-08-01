<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Vessel;
use App\Models\Route;
use App\Models\TicketType;
use App\Models\User;
use App\Models\ScheduleFCFastEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;
use Throwable;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\ScheduleFCEventRequest;
use Carbon\Carbon;

class ScheduleController extends Controller
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
    public function index(Request $request)
    {
        
        $scheduleIndex = Cache::remember('scheduleIndex', 60, function(){//Right now we are doing caching for 60 seconds
            $schedules =  Schedule::with('route.allTicketTypeOfRoute','vessel')->get();//get all schedules with route's ticket type,vessel relationship
            $vessels = Vessel::get();
            $users = User::get();
            $ticket_types = TicketType::get();
            return compact('schedules','vessels','users','ticket_types');
        });
        $schedules = $scheduleIndex["schedules"];
        $vessels = $scheduleIndex["vessels"];
        $users = $scheduleIndex["users"];
        $ticket_types = $scheduleIndex["ticket_types"];

        return view('admin.schedule.index', compact('schedules','users','vessels','ticket_types'));//sending the variable to view
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     * 
     * This method is used to create schedule
     */
    public function create(Request $request)
    {
        if($request->ajax()){//if it is a ajax request
            //find route where route code and ticket id is same as request         
            $route_ids = Route::where('route_code', $request->routeCodeID)->with('ticket_type')->get();
            return $route_ids;//return list of price
        }  
        
        $routes = Route::where('user_id', Auth::id())->simplePaginate(3);
        $user_id = Auth::id();
        $userName = User::find($user_id)->first();
        if($request->user_id != null){//if there is user id in request then get that user's schedules
            $routes = Route::where('user_id', $request->user_id)->simplePaginate(3);
            $user_id = $request->user_id;
            $userName = User::find($user_id)->first();
        }
        
        $vessels = Vessel::all();//get all vessels
        $routesAll = Route::all();//get all routes
        $users = User::all();//get all users

        return view('admin.schedule.create',compact('routes','vessels','routesAll','users','user_id','userName'));//sending the variables to the view
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * 
     * This method is what happens when we submit in create page
     */
    public function store(Request $request)
    {
        DB::beginTransaction();//start transaction, This is to have control over rollbacks and commits

        $request->validate([//validating the data that is being passed through. If an error occurs it will redirect back to page
            'user_id'=>'required',
            'route_id'=>'required',
            'vessel_id'=>'required',
            'schedule_date'=>'required'
        ]);

        try {
            $schedule = new Schedule;//making new schedule
            $schedule->user_id = $request->user_id;//initializing with what user entered
            $schedule->route_id = $request->route_id;
            $schedule->vessel_id = $request->vessel_id;
            $schedule->available_seats = Vessel::where('id',$request->vessel_id)->first()->seat_capacity;
            $schedule->schedule_date = $request->schedule_date;
            $schedule->save();//save

            DB::commit();//This means nothing went wrong so we can commit/save to the database//commit/save to the database

            /**
             * We are forgetting all these caches because they are getting schedule for their pages.
             * If there is a change then we must forget the cache so that the new schedule will be seen on these pages.
             */
            Cache::forget('scheduleIndex');//when there is a change remove the cache.

            Cache::forget('bookingCreate');
            Cache::forget('bookingEdit');

            Cache::forget('myBookingCreate');
            Cache::forget('myBookingEdit');

        } catch(Exception $e) {
            DB::rollBack();//Something went wrong so we undo the transaction. It won't be saved to database
            return redirect('/admin/schedule/create')->withErrors([ $e->getMessage() ])->withInput();//if it is an sql error then it will show the entire sql error. If we want we can put custom message
        } catch(Throwable $e) {
            DB::rollBack();//same thing here
            return redirect('/admin/schedule/create')->withErrors([ $e->getMessage() ])->withInput();
        }

        return redirect('/admin/schedule/')->with('success', 'Schedule Created!');//if no errors then redirect back to page with alert
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Schedule  $schedule
     * @return \Illuminate\Http\Response
     * 
     * This method is used to show data. Individual data.
     */
    public function show(Schedule $schedule)
    {
        return view('admin.schedule.show',compact('schedule'));//sending the variable to the view
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Schedule  $schedule
     * @return \Illuminate\Http\Response
     * 
     * This method is used to edit schedule
     */
    public function edit(Schedule $schedule)
    {
        $scheduleEdit = Cache::remember('scheduleEdit', 60, function () {//Right now we are doing caching for 60 seconds
            $vessels = Vessel::all();//get all vessels
            $routes = Route::all();//get all routes
            $users = User::all();//get all users
            return compact('users','vessels','routes');//returning it as variables
        });

        $vessels = $scheduleEdit["vessels"];//accessing the variable and initializing 
        $routes = $scheduleEdit["routes"];
        $users = $scheduleEdit["users"];
        return view('admin.schedule.edit',compact('schedule','vessels','routes','users'));//sending the variables to the view
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Schedule  $schedule
     * @return \Illuminate\Http\Response
     * 
     * This method is what happens when we submit in edit page
     */
    public function update(Request $request, Schedule $schedule)
    {
        DB::beginTransaction();//start transaction, This is to have control over rollbacks and commits

        $request->validate([//validating the data that is being passed through. If an error occurs it will redirect back to page
            'user_id'=>'required',
            'route_id'=>'required',
            'vessel_id'=>'required',
            'schedule_date'=>'required'
        ]);
        try {
            $schedule->user_id = $request->user_id;//initialzing with what user entered
            $schedule->route_id = $request->route_id;
            $schedule->vessel_id = $request->vessel_id;
            $schedule->available_seats = Vessel::where('id',$request->vessel_id)->first()->seat_capacity;
            $schedule->schedule_date = $request->schedule_date;
            $schedule->save();//save

            DB::commit();//This means nothing went wrong so we can commit/save to the database

            /**
             * We are forgetting all these caches because they are getting schedule for their pages.
             * If there is a change then we must forget the cache so that the new schedule will be seen on these pages.
             */
            Cache::forget('scheduleIndex');//when there is a change remove the cache.

            Cache::forget('bookingCreate');
            Cache::forget('bookingEdit');
            
            Cache::forget('myBookingCreate');
            Cache::forget('myBookingEdit');

        } catch(Exception $e) {
            DB::rollBack();//Something went wrong so we undo the transaction. It won't be saved to database
            return redirect("/admin/schedule/{$schedule->id}/edit")->withErrors([ $e->getMessage() ])->withInput();//if it is an sql error then it will show the entire sql error. If we want we can put custom message
        } catch(Throwable $e) {
            DB::rollBack();//same thing here
            return redirect("/admin/schedule/{$schedule->id}/edit")->withErrors([ $e->getMessage() ])->withInput();
        }
        
        return redirect('/admin/schedule/')->with('Edit-success','Schedule Updated!');//if no errors then redirect back to page with alert
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Schedule  $schedule
     * @return \Illuminate\Http\Response
     * 
     * This method is used for deleting schedule
     */
    public function destroy(Schedule $schedule)
    {
        DB::beginTransaction();//start transaction, This is to have control over rollbacks and commits
        try {
            $schedule->delete();//deleting it

            DB::commit();//This means nothing went wrong so we can commit/save to the database

            /**
             * We are forgetting all these caches because they are getting schedule for their pages.
             * If there is a change then we must forget the cache so that the new schedule will be seen on these pages.
             */
            Cache::forget('scheduleIndex');//when there is a change remove the cache.

            Cache::forget('bookingCreate');
            Cache::forget('bookingEdit');

            Cache::forget('myBookingCreate');
            Cache::forget('myBookingEdit');

        } catch(Exception $e) {
            DB::rollBack();//Something went wrong so we undo the transaction. It won't be saved to database
            return redirect("/admin/schedule/")->withErrors([ $e->getMessage() ])->withInput();//if it is an sql error then it will show the entire sql error. If we want we can put custom message
        } catch(Throwable $e) {
            DB::rollBack();//same thing here
            return redirect("/admin/schedule/")->withErrors([ $e->getMessage() ])->withInput();
        }
        
        return redirect('/admin/schedule/')->with('Delete-success','Schedule deleted successfully!');//if no errors then redirect back to page with alert
    }

    /**
     * Schedule Full Calendar controller
     */

    public function scheduleFCLoadEvent(Request $request, $id){
        
        $returnedColumns = ['id', 'title', 'start', 'end', 'user_id', 'route_id', 'vessel_id', 'available_seats'];

        $start = (!empty($request->start)) ? ($request->start) : ('');
        $end = (!empty($request->end)) ? ($request->end) : ('');
        //return events between the start and end dates 
        $scheduleFCEvents = Schedule::where('user_id',Auth::id())->whereBetween('start', [$start, $end])->get($returnedColumns);

        if($id != null){//if there is user id in request then get that user's schedules
            $scheduleFCEvents = Schedule::where('user_id',$id)->whereBetween('start', [$start, $end])->get($returnedColumns);
        }

        return response()->json($scheduleFCEvents);
    }

    public function scheduleFCStoreEvent(ScheduleFCEventRequest $request)
    {
        $schedule = new Schedule;
        $schedule->title = $request->title;
        $schedule->start = $request->start;
        $schedule->end = $request->end;
        $schedule->user_id = $request->user_id;
        $schedule->route_id = $request->route_id;
        
        if($request->vessel_id != null){
            $schedule->vessel_id = $request->vessel_id;
            $schedule->available_seats = Vessel::where('id',$request->vessel_id)->first()->seat_capacity;
        }
        $schedule->save();

        return response()->json(true);
        
    }

    public function scheduleFCUpdateEvent(ScheduleFCEventRequest $request)
    {
        $schedule = Schedule::where('id', $request->id)->first();
        $schedule->route_id = $request->route_id;
        $schedule->title = $request->title;
        $schedule->start = $request->start;
        $schedule->end = $request->end;
        $schedule->user_id = $request->user_id;
        
        if($request->vessel_id != null){
            $schedule->vessel_id = $request->vessel_id;
            $schedule->available_seats = Vessel::where('id',$request->vessel_id)->first()->seat_capacity;
        }
        
        $schedule->save();//save

        return response()->json(true);
    }

    public function scheduleFCDeleteEvent(Request $request)
    {
        Schedule::where('id', $request->id)->delete();
        return response()->json(true);
    }
}
