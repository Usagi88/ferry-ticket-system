<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Vessel;
use App\Models\FcmToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
use Throwable;
use Illuminate\Support\Facades\Cache;
use App\Notifications\VesselAssigned;

class AssignToVesselController extends Controller
{
    /**
     * Auth Middelware. If the user isn't logged in then redirect back to login
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
        $assignIndex = Cache::remember('assignToVesselIndex', 60, function(){//Right now we are doing caching for 60 seconds
            $assigns = User::with('vesselsAssignedToUser')->get();//we are getting users with vesselsAssignedToUser relationship
            $assignCount = DB::table('user_vessel')->get()->count();
            return compact('assigns','assignCount');
        });
        $assigns = $assignIndex['assigns'];
        $assignCount = $assignIndex['assignCount'];
        return view('admin.vessel.assign-vessel.index', compact('assigns','assignCount'));//sending the variable to view
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     * 
     * This method is used to create a new assign vessel
     */
    public function create()
    {
        $assignVesselCreate = Cache::remember('assignToVesselCreate', 60, function () {//Right now we are doing caching for 60 seconds
            $users = User::whereHas('roles', function($q){//getting all users with role agent 
                $q->where('name', 'Agent');
            })->get();
            $vessels = Vessel::get();//getting all vessels
            return compact('users','vessels');//returning it as variable
            
        });
        $users = $assignVesselCreate["users"];//accessing the variable and initializing 
        $vessels = $assignVesselCreate["vessels"];
        return view('admin.vessel.assign-vessel.create',compact('users','vessels'));//sending the variables to the view
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
            'vessel_id'=>'required',
        ]);
        try {

            $user = User::where('id', $request->user_id)->first();
            $vessel = Vessel::where('id', $request->vessel_id)->first();
            
            /**
             * attaching to user vessel table with additional field since our table require it now. Usually it would be just vessel id
             * we can do this using DB query too but I want to show the relationship. If we use DB query here the user variable wouldn't be needed
             */
            //$user->vesselsAssignedToUser()->attach($request->vessel_id,array('owner_id' => $vessel->owner_id));
            if($user->vesselsAssignedToUser->contains($vessel->id)){//check table if it contains a row with user id and vessel id
                $user->vesselsAssignedToUser()->detach($vessel);//detach it so it won't cause duplication
                $user->vesselsAssignedToUser()->attach($vessel, ['owner_id' => $vessel->owner_id]);//then attach it
            } else {
                $user->vesselsAssignedToUser()->attach($vessel, ['owner_id' => $vessel->owner_id]);//attach if not found
            }
            $user->save();//save

            $listOfTokens = [];
            foreach ($user->fcmTokens as $token) {//getting fcm tokens 
                $listOfTokens[] = $token->fcm_token;
            }
            
            if (count($listOfTokens) > 0 && $listOfTokens != null ){//If listTokens are not empty and null
                $user->notify(new VesselAssigned($user->first_name,$listOfTokens));//sending notification
            }
            
            
            DB::commit();//This means nothing went wrong so we can commit/save to the database
            Cache::forget('assignToVesselIndex');//forget the cache so we see the changes.

        } catch(Exception $e) {
            DB::rollBack();//Something went wrong so we undo the transaction. It won't be saved to database
            return redirect('/admin/vessel/assign-vessel/create')->withErrors([ $e->getMessage() ])->withInput();//if it is an sql error then it will show the entire sql error. If we want we can put custom message
            
        } catch(Throwable $e) {
            DB::rollBack();//same thing here
            return redirect('/admin/vessel/assign-vessel/create')->withErrors([ $e->getMessage() ])->withInput();
            
        }

        return redirect('/admin/vessel/assign-vessel/')->with('success', 'Assign Created!');//if no errors then redirect back to page with alert
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\UserVessel  $userVessel
     * @return \Illuminate\Http\Response
     * 
     * This method is used to show data. Individual data.
     */
    public function show($id)
    {
        $assign = DB::table('user_vessel')->where('id', $id)->get();//getting user from user_vessel table
        return view('admin.vessel.assign-vessel.show',compact('assign'));//sending the variable to the view
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\UserVessel  $userVessel
     * @return \Illuminate\Http\Response
     * 
     * This method is used to edit an assign vessel
     */
    public function edit($id)
    {
        $assignVesselEdit = Cache::remember('assignVesselEdit', 60, function () use($id) {//Right now we are doing caching for 60 seconds
            $assign = DB::table('user_vessel')->where('id', $id)->first();//getting user from user_vessel table
            //$users = User::whereHas('roles', function($q){//getting all users with agent role
            //    $q->where('name', 'Agent');
            //})->get();
            $user = User::where('id', $assign->user_id)->first();
            $vessels = Vessel::get();//getting all vessels
            return compact('assign','vessels','user');//returning it as variables
        });
        $assign = $assignVesselEdit["assign"];//accessing the variable and initializing 
        $user = $assignVesselEdit["user"];
        //$users = $assignVesselEdit["users"];
        $vessels = $assignVesselEdit["vessels"];
        return view('admin.vessel.assign-vessel.edit',compact('assign','vessels','user'));//sending the variables to the view
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\UserVessel  $userVessel
     * @return \Illuminate\Http\Response
     * 
     * This method is what happens when we submit in edit page
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();//start transaction, This is to have control over rollbacks and commits
        $request->validate([//validating the data that is being passed through. If an error occurs it will redirect back to page
            'user_id'=>'required',
            'vessel_id'=>'required',
        ]);
        try {
            $vessel = Vessel::where('id', $request->vessel_id)->first();
            //$user = User::where('id', $request->user_id)->first();
            
            //$assign = DB::table('user_vessel')->where('id', $id)->update(['user_id' => $request->user_id,
            // 'vessel_id' => $request->vessel_id, 'owner_id' => $vessel->owner_id
            // ]);
            
            //updating user_id, vessel_id, and owner_id directly to the table
            DB::table('user_vessel')->where('id', $id)->update(['vessel_id' => $request->vessel_id, 'owner_id' => $vessel->owner_id]);
            
            DB::commit();//This means nothing went wrong so we can commit/save to the database
            Cache::forget('assignToVesselIndex');//forget the cache so we see the changes.

        } catch(Exception $e) {
            DB::rollBack();//Something went wrong so we undo the transaction. It won't be saved to database
            return redirect('/admin/vessel/assign-vessel/edit')->withErrors([ $e->getMessage() ])->withInput();//if it is an sql error then it will show the entire sql error. If we want we can put custom message
        } catch(Throwable $e) {
            DB::rollBack();//same thing here
            return redirect('/admin/vessel/assign-vessel/edit')->withErrors([ $e->getMessage() ])->withInput();
        }

        return redirect('/admin/vessel/assign-vessel')->with('Edit-success', 'Assign Updated!');//if no errors then redirect back to page with alert
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\UserVessel  $userVessel
     * @return \Illuminate\Http\Response
     * 
     * This method is used for deleting an assign vessel
     */
    public function destroy($id)
    {
        DB::beginTransaction();//start transaction, This is to have control over rollbacks and commits

        try {
            $assign = DB::table('user_vessel')->where('id', $id)->delete();//deleting it directly from the table

            DB::commit();//This means nothing went wrong so we can commit/save to the database

            Cache::forget('assignToVesselIndex');//forget the cache so we see the changes.
            
        } catch(Exception $e) {
            DB::rollBack();//Something went wrong so we undo the transaction. It won't be saved to database
            return redirect("/admin/vessel/assign-vessel")->withErrors([ $e->getMessage() ])->withInput();//if it is an sql error then it will show the entire sql error. If we want we can put custom message
        } catch(Throwable $e) {
            DB::rollBack();
            return redirect("/admin/vessel/assign-vessel")->withErrors([ $e->getMessage() ])->withInput();
            
        }

        return redirect('/admin/vessel/assign-vessel')->with('Delete-success','Assign deleted successfully!');//if no errors then redirect back to page with alert
    }
}
