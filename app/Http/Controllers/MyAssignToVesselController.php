<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Vessel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
use Throwable;
use Illuminate\Support\Facades\Cache;
use App\Notifications\VesselAssigned;

class MyAssignToVesselController extends Controller
{
    /**
     * Auth Middleware. If the user isn't logged in then redirect back to login
     * pageView Middleware. If the user has agent role then he cannot access the page. 
     * The reason for doing that is because this page belongs to merchants only.
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
        /**
         * Using user id to make the cache unique.
         */
        $myAssigns = Cache::remember('myAssignToVesselIndexID'.$user->id, 60, function() use($user){//Right now we are doing caching for 60 seconds
            return User::where('id',$user->id)->with('vesselsAssignedToUser')->get();//get user with vesselsAssignedToUser
        });
        return view('admin.user.profile.vessel-assigned-to.index', compact('myAssigns','user'));//sending the variables to view
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     * 
     * This method is used to create (my) assign vessel
     */
    public function create(User $user)
    {
        $myAssignVesselCreate = Cache::remember('myAssignToVesselCreate', 60, function () {//Right now we are doing caching for 60 seconds
            //$users = User::whereHas('roles', function($q){//getting all users with role agent 
            //    $q->where('name', 'Agent');
            //})->get();
            $vessels = Vessel::get();//get all vessels
            return compact('vessels');//returning it as variables
        });
        //$users = $myAssignVesselCreate["users"];//accessing the variable and initializing 
        $vessels = $myAssignVesselCreate["vessels"];
        return view('admin.user.profile.vessel-assigned-to.create', compact('vessels','user'));//sending the variables to the view
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
            'vessel_id'=>'required',
        ]);

        try {
            //$agent = User::where('id', $request->user_id)->first();
            $vessel = Vessel::where('id', $request->vessel_id)->first();
            
            /**
             * attaching to user vessel table with additional field since our table require it now. Usually it would be just vessel id
             * we can do this using DB query too but I want to show the relationship. If we use DB query here the agent variable wouldn't be needed
             */
            $user->vesselsAssignedToUser()->attach($request->vessel_id,array('owner_id' => $vessel->owner_id));
            $user->save();//save

            $listOfTokens = [];
            foreach ($user->fcmTokens as $token) {
                $listOfTokens[] = $token->fcm_token;
            }

            $user->notify(new VesselAssigned($user->first_name,$listOfTokens));

            DB::commit();//This means nothing went wrong so we can commit/save to the database
            /**
             * For individual user pages we are using user's id to define the cache. If everyone used the same cache then the values will be the same
             * If there is a change then we must forget the cache so that the changes will be seen on these pages.
             */
            Cache::forget('myAssignToVesselIndexID'.$user->id);//forget the cache so we see the changes.
            Cache::forget('assignToVesselIndex');

        } catch(Exception $e) {
            DB::rollBack();//Something went wrong so we undo the transaction. It won't be saved to database
            return redirect("/admin/user/{$user->id}/profile/vessel-assigned-to/create")->withErrors([ $e->getMessage() ])->withInput();//if it is an sql error then it will show the entire sql error. If we want we can put custom message
        } catch(Throwable $e) {
            DB::rollBack();//same thing here
            return redirect("/admin/user/{$user->id}/profile/vessel-assigned-to/create")->withErrors([ $e->getMessage() ])->withInput();
        }

        return redirect("/admin/user/{$user->id}/profile/vessel-assigned-to/")->with('success', 'My Assign Created!');//if no errors then redirect back to page with alert
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Vessel  $vessel
     * @return \Illuminate\Http\Response
     * 
     * This method is used to show data. Individual data.
     */
    public function show(User $user, $id)
    {
        $myAssign = DB::table('user_vessel')->where('id', $id)->get();//getting user vessel data from user vessel table
        return view('admin.user.profile.vessel-assigned-to.show',compact('myAssign'));//sending the variable to the view
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Vessel  $vessel
     * @return \Illuminate\Http\Response
     * 
     * This method is used to edit (my) assign vessel
     */
    public function edit(User $user, $id)
    {
        $myAssignVesselEdit = Cache::remember('myAssignToVesselEdit', 60, function () use($id) {//Right now we are doing caching for 60 seconds
            $myAssign = DB::table('user_vessel')->where('id', $id)->first();//getting user vessel data from user vessel table
            //$users = User::whereHas('roles', function($q){//getting all users with agent role
            //    $q->where('name', 'Agent');
            //})->get();
            $vessels = Vessel::get();//get all vessels
            return compact('myAssign','vessels');//returning it as variables
        });

        $myAssign = $myAssignVesselEdit["myAssign"];//accessing the variable and initializing
        //$users = $myAssignVesselEdit["users"];
        $vessels = $myAssignVesselEdit["vessels"];

        return view('admin.user.profile.vessel-assigned-to.edit',compact('myAssign','vessels','user'));//sending the variables to the view
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
    public function update(Request $request, User $user, $id)
    {
        DB::beginTransaction();//start transaction, This is to have control over rollbacks and commits

        $request->validate([//validating the data that is being passed through. If an error occurs it will redirect back to page
            'vessel_id'=>'required',
        ]);

        try {
            $vessel = Vessel::where('id', $request->vessel_id)->first();
            //updating user_id, vessel_id, and owner_id directly to the table
            $assign = DB::table('user_vessel')->where('id', $id)->update(['user_id' => $user->id,
             'vessel_id' => $request->vessel_id, 'owner_id' => $vessel->owner_id
             ]);

            
            DB::commit();//This means nothing went wrong so we can commit/save to the database

            /**
             * For individual user pages we are using user's id to define the cache. If everyone used the same cache then the values will be the same
             * If there is a change then we must forget the cache so that the changes will be seen on these pages.
             */
            Cache::forget('myAssignToVesselIndexID'.$user->id);//forget the cache so we see the changes.
            Cache::forget('assignToVesselIndex');

        } catch(Exception $e) {
            DB::rollBack();//Something went wrong so we undo the transaction. It won't be saved to database
            return redirect("/admin/user/{$user->id}/profile/vessel-assigned-to/{$id}/edit")->withErrors([ $e->getMessage() ])->withInput();//if it is an sql error then it will show the entire sql error. If we want we can put custom message
        } catch(Throwable $e) {
            DB::rollBack();//same thing here
            return redirect("/admin/user/{$user->id}/profile/vessel-assigned-to/{$id}/edit")->withErrors([ $e->getMessage() ])->withInput();
        }

        return redirect("/admin/user/{$user->id}/profile/vessel-assigned-to/")->with('Edit-success','My Assign Updated!');//if no errors then redirect back to page with alert
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Vessel  $vessel
     * @return \Illuminate\Http\Response
     * 
     * This method is used for deleting (my) assign vessel
     */
    public function destroy(User $user, $id)
    {
        DB::beginTransaction();//start transaction, This is to have control over rollbacks and commits

        try {
            $assign = DB::table('user_vessel')->where('id', $id)->delete();//deleting it directly from the table

            DB::commit();//This means nothing went wrong so we can commit/save to the database

            /**
             * For individual user pages we are using user's id to define the cache. If everyone used the same cache then the values will be the same
             * If there is a change then we must forget the cache so that the changes will be seen on these pages.
             */
            Cache::forget('myAssignToVesselIndexID'.$user->id);//forget the cache so we see the changes.
            Cache::forget('assignToVesselIndex');
            
        } catch(Exception $e) {
            DB::rollBack();//Something went wrong so we undo the transaction. It won't be saved to database
            return redirect("/admin/user/{$user->id}/profile/vessel-assigned-to/")->withErrors([ $e->getMessage() ])->withInput();//if it is an sql error then it will show the entire sql error. If we want we can put custom message
        } catch(Throwable $e) {
            DB::rollBack();//same thing here
            return redirect("/admin/user/{$user->id}/profile/vessel-assigned-to/")->withErrors([ $e->getMessage() ])->withInput();
        }
        
        return redirect("/admin/user/{$user->id}/profile/vessel-assigned-to/")->with('Delete-success','My Assign deleted successfully!');//if no errors then redirect back to page with alert
    }
}
