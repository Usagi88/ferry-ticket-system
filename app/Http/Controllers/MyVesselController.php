<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\VesselType;
use App\Models\Vessel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;
use Throwable;
use Illuminate\Support\Facades\Cache;

class MyVesselController extends Controller
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
        $vesselIndex = Cache::remember('myVesselIndexID'.$user->id, 60, function() use($user){//Right now we are doing caching for 60 seconds
            $vessels =  Vessel::where('owner_id',$user->id)->with('owner','vessel_type')->orderBy('id','desc')->get();//get user's vessel with owner,vessel_type relationship
            $ferry = Vessel::where('owner_id',$user->id)->whereHas('vessel_type', function($q){//getting all users with role 
                $q->where('name', 'Ferry');
            })->get()->count();
            $speedBoat = Vessel::where('owner_id',$user->id)->whereHas('vessel_type', function($q){//getting all users with role 
                $q->where('name', 'Speed boat');
            })->get()->count();
            return compact('vessels','ferry','speedBoat');
        });
        $vessels = $vesselIndex['vessels'];
        $ferry = $vesselIndex['ferry'];
        $speedBoat = $vesselIndex['speedBoat'];
        return view('admin.user.profile.my-vessel.index', compact('vessels','user','ferry','speedBoat'));//sending the variables to the view
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     * 
     * This method is used to create (my) vessel
     */
    public function create(User $user)
    {
        $vessel_types = Cache::remember('myVesselCreate', 60, function() {//Right now we are doing caching for 60 seconds
            return VesselType::get();//get all vessel types
        });
        return view('admin.user.profile.my-vessel.create', compact('vessel_types','user'));//sending the variables to the view
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
            'name'=>'required|max:255',
            'seat_capacity'=>'required',
            'max_accompanied_cargo'=>'required',
            'max_unaccompanied_cargo'=>'required'
        ]);
        try {
            $vessel = new Vessel;//making a new vessel
            $vessel->owner_id = $user->id;
            $vessel->name = $request->name;//initializing it with what user entered
            $vessel->seat_capacity = $request->seat_capacity;
            $vessel->max_accompanied_cargo = $request->max_accompanied_cargo;
            $vessel->max_unaccompanied_cargo = $request->max_unaccompanied_cargo;
            $vessel->vessel_type_id = $request->vessel_type_id;
            $vessel->save();//save

            DB::commit();//This means nothing went wrong so we can commit/save to the database

            /**
             * For individual user pages we are using user's id to define the cache. If everyone used the same cache then the values will be the same
             * If there is a change then we must forget the cache so that the changes will be seen on these pages.
             */
            Cache::forget('myVesselIndexID'.$user->id);//forget the cache so we see the changes.
            Cache::forget('vesselIndex');

        } catch(Exception $e) {
            DB::rollBack();//Something went wrong so we undo the transaction. It won't be saved to database
            return redirect("/admin/user/{$user->id}/profile/my-vessel/create")->withErrors([ $e->getMessage() ])->withInput();//if it is an sql error then it will show the entire sql error. If we want we can put custom message
        } catch(Throwable $e) {
            DB::rollBack();//same thing here
            return redirect("/admin/user/{$user->id}/profile/my-vessel/create")->withErrors([ $e->getMessage() ])->withInput();
            
        }
        
        return redirect("/admin/user/{$user->id}/profile/my-vessel/")->with('success', 'Vessel Created!');//if no errors then redirect back to page with alert
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     * 
     * This method is used to show data. Individual data.
     */
    public function show(User $user,Vessel $vessel)
    {
        return view('admin.user.profile.my-vessel.show', compact('vessel'));//sending the variable to the view
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     * 
     * This method is used to edit (my) vessel
     */
    public function edit(User $user, Vessel $vessel)
    {
        $vessel_types = Cache::remember('myVesselEdit', 60, function() {//Right now we are doing caching for 60 seconds
            return VesselType::get();//get all vessel types
        });
        return view('admin.user.profile.my-vessel.edit', compact('user','vessel_types','vessel'));//sending the variables to the view
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
    public function update(Request $request, User $user, Vessel $vessel)
    {
        DB::beginTransaction();//start transaction, This is to have control over rollbacks and commits

        $request->validate([//validating the data that is being passed through. If an error occurs it will redirect back to page
            'name'=>'required|max:255',
            'seat_capacity'=>'required',
            'max_accompanied_cargo'=>'required',
            'max_unaccompanied_cargo'=>'required',
            'vessel_type_id'=>'required'
        ]);

        try {
            $vessel->name = $request->name;//initialzing with what user entered
            $vessel->seat_capacity = $request->seat_capacity;
            $vessel->max_accompanied_cargo = $request->max_accompanied_cargo;
            $vessel->max_unaccompanied_cargo = $request->max_unaccompanied_cargo;
            $vessel->vessel_type_id = $request->vessel_type_id;
            $vessel->save();//save

            DB::commit();//This means nothing went wrong so we can commit/save to the database

            /**
             * For individual user pages we are using user's id to define the cache. If everyone used the same cache then the values will be the same
             * If there is a change then we must forget the cache so that the changes will be seen on these pages.
             */
            Cache::forget('myVesselIndexID'.$user->id);//forget the cache so we see the changes.
            Cache::forget('vesselIndex');//forget the cache so we see the changes.

        } catch(Exception $e) {
            DB::rollBack();//Something went wrong so we undo the transaction. It won't be saved to database
            return redirect("/admin/user/{$user->id}/profile/my-vessel/{$vessel->id}/edit")->withErrors([ $e->getMessage() ])->withInput();//if it is an sql error then it will show the entire sql error. If we want we can put custom message
        } catch(Throwable $e) {
            DB::rollBack();
            return redirect("/admin/user/{$user->id}/profile/my-vessel/{$vessel->id}/edit")->withErrors([ $e->getMessage() ])->withInput();
        }

        return redirect("/admin/user/{$user->id}/profile/my-vessel/")->with('Edit-success','Vessel Updated!');//if no errors then redirect back to page with alert
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     * 
     * This method is used for deleting (my) vessel
     */
    public function destroy(User $user, Vessel $vessel)
    {
        DB::beginTransaction();//start transaction, This is to have control over rollbacks and commits
        try {
            $vessel->delete();//deleting it

            DB::commit();//This means nothing went wrong so we can commit/save to the database

            /**
             * For individual user pages we are using user's id to define the cache. If everyone used the same cache then the values will be the same
             * If there is a change then we must forget the cache so that the changes will be seen on these pages.
             */
            Cache::forget('myVesselIndexID'.$user->id);//forget the cache so we see the changes.
            Cache::forget('vesselIndex');//forget the cache so we see the changes.

        } catch(Exception $e) {
            DB::rollBack();//Something went wrong so we undo the transaction. It won't be saved to database
            return redirect("/admin/user/{$user->id}/profile/my-vessel/")->withErrors([ $e->getMessage() ])->withInput();//if it is an sql error then it will show the entire sql error. If we want we can put custom message
        } catch(Throwable $e) {
            DB::rollBack();//same thing here
            return redirect("/admin/user/{$user->id}/profile/my-vessel/")->withErrors([ $e->getMessage() ])->withInput();
        }
        
        return redirect("/admin/user/{$user->id}/profile/my-vessel/")->with('Delete-success','Vessel deleted successfully!');//if no errors then redirect back to page with alert
    }
}
