<?php

namespace App\Http\Controllers;

use App\Models\Vessel;
use App\Models\VesselType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
use Throwable;
use Illuminate\Support\Facades\Cache;


class VesselController extends Controller
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
     public function index()
    {
        $vesselIndex = Cache::remember('vesselIndex', 60, function(){//Right now we are doing caching for 60 seconds
            $vessels = Vessel::with('owner','vessel_type')->orderBy('id','desc')->get();//get all vessels with owner,vessel_type relationship
            $ferry = Vessel::whereHas('vessel_type', function($q){//getting all users with role 
                $q->where('name', 'Ferry');
            })->get()->count();
            $speedBoat = Vessel::whereHas('vessel_type', function($q){//getting all users with role 
                $q->where('name', 'Speed boat');
            })->get()->count();

            return compact('vessels','ferry','speedBoat');
        });
        $vessels = $vesselIndex['vessels'];
        $ferry = $vesselIndex['ferry'];
        $speedBoat = $vesselIndex['speedBoat'];
        return view('admin.vessel.index', compact('vessels','ferry','speedBoat'));//sending the variable to view
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     * 
     * This method is used to create vessel
     */
    public function create()
    {
        $this->authorize('create', Vessel::class);//check if user can create vessel using vessel policy's create method
        
        $vesselCreate = Cache::remember('vesselCreate', 60, function () {//Right now we are doing caching for 60 seconds
            $vessel_types = VesselType::all();//get all vessel types
            $users = User::all();//get all users
            return compact('vessel_types','users');//sending the variables to the view
        });
        $vessel_types = $vesselCreate["vessel_types"];//accessing the variable and initializing 
        $users = $vesselCreate["users"];
        return view('admin.vessel.create', compact('vessel_types','users'));//sending the variables to the view
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
            'owner_id'=>'required',
            'name'=>'required|max:255',
            'seat_capacity'=>'required',
            'max_accompanied_cargo'=>'required',
            'max_unaccompanied_cargo'=>'required'
        ]);

        try {
            $vessel = new Vessel;//making a new vessel
            $vessel->owner_id = $request->owner_id;//initializing with what user entered
            $vessel->name = $request->name;
            $vessel->seat_capacity = $request->seat_capacity;
            $vessel->max_accompanied_cargo = $request->max_accompanied_cargo;
            $vessel->max_unaccompanied_cargo = $request->max_unaccompanied_cargo;
            $vessel->vessel_type_id = $request->vessel_type_id;
            $vessel->save();//save

            DB::commit();//This means nothing went wrong so we can commit/save to the database//commit/save to the database

            /**
             * We are forgetting all these caches because they are getting vessel for their pages.
             * If there is a change then we must forget the cache so that the new vessel will be seen on these pages.
             */
            Cache::forget('vesselIndex');//when there is a change remove the cache.

            Cache::forget('bookingCreate');
            Cache::forget('bookingEdit');

            Cache::forget('myBookingCreate');
            Cache::forget('myBookingEdit');

            Cache::forget('myScheduleCreate');
            Cache::forget('myScheduleEdit');

            Cache::forget('scheduleCreate');
            Cache::forget('scheduleEdit');

            Cache::forget('assignToVesselCreate');
            Cache::forget('assignToVesselEdit');

            Cache::forget('myAssignToVesselCreate');
            Cache::forget('myAssignToVesselEdit');

            Cache::forget('myAssignedVesselCreate');
            Cache::forget('myAssignedVesselEdit');

        } catch(Exception $e) {
            DB::rollBack();//Something went wrong so we undo the transaction. It won't be saved to database
            return redirect('/admin/vessel/create')->withErrors([ $e->getMessage() ])->withInput();//if it is an sql error then it will show the entire sql error. If we want we can put custom message
        } catch(Throwable $e) {
            DB::rollBack();//same thing here
            return redirect('/admin/vessel/create')->withErrors([ $e->getMessage() ])->withInput();
        }
       
        return redirect('/admin/vessel/')->with('success', 'Vessel Created!');//if no errors then redirect back to page with alert
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Vessel  $vessel
     * @return \Illuminate\Http\Response
     * 
     * This method is used to show data. Individual data.
     */
    public function show(Vessel $vessel)
    {
        return view('admin.vessel.show', compact('vessel'));//sending the variable to the view
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Vessel  $vessel
     * @return \Illuminate\Http\Response
     * 
     * This method is used to edit vessel
     */
    public function edit(Vessel $vessel)
    {
        $this->authorize('edit', $vessel);//check if user can edit vessel using vessel policy's edit method

        $vesselEdit = Cache::remember('vesselEdit', 60, function(){//Right now we are doing caching for 60 seconds
            $vessel_types = VesselType::all();//get all vessel types
            $users = User::all();//get all users
            return compact('vessel_types','users');//returning it as variables
        });

        $vessel_types = $vesselEdit["vessel_types"];//accessing the variable and initializing 
        $users = $vesselEdit["users"];

        return view('admin.vessel.edit', compact('vessel','vessel_types','users'));//sending the variables to the view
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Vessel  $vessel
     * @return \Illuminate\Http\Response
     * 
     * This method is what happens when we submit in edit page
     */
    public function update(Request $request, Vessel $vessel)
    {
        $this->authorize('update', $vessel);//check if user can update vessel using vessel policy's update method
        
        DB::beginTransaction();//start transaction, This is to have control over rollbacks and commits

        $request->validate([//validating the data that is being passed through. If an error occurs it will redirect back to page
            'owner_id'=>'required',
            'name'=>'required|max:255',
            'seat_capacity'=>'required',
            'max_accompanied_cargo'=>'required',
            'max_unaccompanied_cargo'=>'required'
        ]);

        try {
            $vessel->owner_id = $request->owner_id;//initialzing with what user entered
            $vessel->name = $request->name;
            $vessel->seat_capacity = $request->seat_capacity;
            $vessel->max_accompanied_cargo = $request->max_accompanied_cargo;
            $vessel->max_unaccompanied_cargo = $request->max_unaccompanied_cargo;
            $vessel->vessel_type_id = $request->vessel_type_id;
            $vessel->vesselAssigned;
            $vessel->save();//save
            
            DB::commit();//This means nothing went wrong so we can commit/save to the database

            /**
             * We are forgetting all these caches because they are getting vessel for their pages.
             * If there is a change then we must forget the cache so that the new vessel will be seen on these pages.
             */
            Cache::forget('vesselIndex');//when there is a change remove the cache.

            Cache::forget('bookingCreate');
            Cache::forget('bookingEdit');

            Cache::forget('myBookingCreate');
            Cache::forget('myBookingEdit');

            Cache::forget('myScheduleCreate');
            Cache::forget('myScheduleEdit');

            Cache::forget('scheduleCreate');
            Cache::forget('scheduleEdit');

            Cache::forget('assignToVesselCreate');
            Cache::forget('assignToVesselEdit');

            Cache::forget('myAssignToVesselCreate');
            Cache::forget('myAssignToVesselEdit');

            Cache::forget('myAssignedVesselCreate');
            Cache::forget('myAssignedVesselEdit');

        } catch(Exception $e) {
            DB::rollBack();//Something went wrong so we undo the transaction. It won't be saved to database
            return redirect("/admin/vessel/{$vessel->id}/edit")->withErrors([ $e->getMessage() ])->withInput();//if it is an sql error then it will show the entire sql error. If we want we can put custom message
        } catch(Throwable $e) {
            DB::rollBack();//same thing here
            return redirect("/admin/vessel/{$vessel->id}/edit")->withErrors([ $e->getMessage() ])->withInput();
        }
        
        return redirect('/admin/vessel/')->with('Edit-success','Vessel Updated!');//if no errors then redirect back to page with alert
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Vessel  $vessel
     * @return \Illuminate\Http\Response
     * 
     * This method is used for deleting vessel
     */
    public function destroy(Vessel $vessel)
    {
        $this->authorize('delete', $vessel);//check if user can update vessel using vessel policy's update method

        DB::beginTransaction();//start transaction, This is to have control over rollbacks and commits

        try {
            $vessel->delete();//deleting it

            DB::commit();//This means nothing went wrong so we can commit/save to the database

            /**
             * We are forgetting all these caches because they are getting vessel for their pages.
             * If there is a change then we must forget the cache so that the new vessel will be seen on these pages.
             */
            Cache::forget('vesselIndex');//when there is a change remove the cache.

            Cache::forget('bookingCreate');
            Cache::forget('bookingEdit');

            Cache::forget('myBookingCreate');
            Cache::forget('myBookingEdit');

            Cache::forget('myScheduleCreate');
            Cache::forget('myScheduleEdit');

            Cache::forget('scheduleCreate');
            Cache::forget('scheduleEdit');

            Cache::forget('assignToVesselCreate');
            Cache::forget('assignToVesselEdit');

            Cache::forget('myAssignToVesselCreate');
            Cache::forget('myAssignToVesselEdit');

            Cache::forget('myAssignedVesselCreate');
            Cache::forget('myAssignedVesselEdit');
            
        } catch(Exception $e) {
            DB::rollBack();//Something went wrong so we undo the transaction. It won't be saved to database
            return redirect("/admin/vessel/")->withErrors([ $e->getMessage() ])->withInput();//if it is an sql error then it will show the entire sql error. If we want we can put custom message
        } catch(Throwable $e) {
            DB::rollBack();//same thing here
            return redirect("/admin/vessel/")->withErrors([ $e->getMessage() ])->withInput();
        }
        
        return redirect('/admin/vessel/')->with('Delete-success','Vessel deleted successfully!');//if no errors then redirect back to page with alert
    }
}
