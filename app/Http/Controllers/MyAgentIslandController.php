<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Island;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
use Throwable;
use Illuminate\Support\Facades\Cache;

class MyAgentIslandController extends Controller
{
    /**
     * Auth Middleware. If the user isn't logged in then redirect back to login
     * pageView Middleware. If the user has merchant role then he cannot access the page. 
     * The reason for doing that is because this page belongs to agents only.
     */
    public function __construct()
    {
        $this->middleware(['auth','pageView:merchant']);
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
        $myAssignIslands = Cache::remember('myAgentIslandIndexID'.$user->id, 60, function() use($user){//Right now we are doing caching for 60 seconds
            return User::where('id',$user->id)->with('islandsAssignedToUser')->get();//get user with islandsAssignedToUser
        });
        return view('admin.user.profile.my-agent-island.index', compact('myAssignIslands','user'));//sending the variables to view
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     * 
     * This method is used to create (my) assign island
     */
    public function create(User $user)
    {
        $myAgentIslandCreate = Cache::remember('myAgentIslandCreate', 60, function () {//Right now we are doing caching for 60 seconds
            $islands = Island::get();//get all islands
            return compact('islands');//returning it as variable
        });
        $islands = $myAgentIslandCreate["islands"];//accessing the variable and initializing 
        return view('admin.user.profile.my-agent-island.create', compact('islands','user'));//sending the variables to the view
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
            'island_id'=>'required',
        ]);

        try {

            $user->islandsAssignedToUser()->attach($request->island_id);//we attach island id using the islandsAssignedToUser relationship. It will save to agent_island table
            $user->save();//save

            DB::commit();//This means nothing went wrong so we can commit/save to the database
            /**
             * For individual user pages we are using user's id to define the cache. If everyone used the same cache then the values will be the same
             * If there is a change then we must forget the cache so that the changes will be seen on these pages.
             */
            Cache::forget('myAgentIslandIndexID'.$user->id);//forget the cache so we see the changes.
            Cache::forget('agentIslandIndex');

        } catch(Exception $e) {
            DB::rollBack();//Something went wrong so we undo the transaction. It won't be saved to database
            return redirect("/admin/user/{$user->id}/profile/my-agent-island/create")->withErrors([ $e->getMessage() ])->withInput();//if it is an sql error then it will show the entire sql error. If we want we can put custom message
            
        } catch(Throwable $e) {
            DB::rollBack();//same thing here
            return redirect("/admin/user/{$user->id}/profile/my-agent-island/create")->withErrors([ $e->getMessage() ])->withInput();
            
        }

        return redirect("/admin/user/{$user->id}/profile/my-agent-island/")->with('success', 'My Assign Created!');//if no errors then redirect back to page with alert
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     * 
     * This method is used to show data. Individual data.
     */
    public function show(User $user, $id)
    {
        $myAssign = DB::table('agent_islands')->where('id', $id)->get();//getting agent island data from agent island table
        return view('admin.user.profile.my-agent-island.show',compact('myAssign'));//sending the variable to the view
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     * 
     * This method is used to edit (my) agent island
     */
    public function edit(User $user, $id)
    {
        $myAgentIslandEdit = Cache::remember('myAgentIslandEdit', 60, function () use($id) {//Right now we are doing caching for 60 seconds
            $myAssign = DB::table('agent_islands')->where('id', $id)->first();//getting agent island data from agent island table
            $islands = Island::get();//get all islands
            return compact('myAssign','islands');//returning it as variables
        });
        $myAssign = $myAgentIslandEdit["myAssign"];//accessing the variable and initializing
        $islands = $myAgentIslandEdit["islands"];
        return view('admin.user.profile.my-agent-island.edit',compact('myAssign','islands','user'));//sending the variables to the view
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
    public function update(Request $request, User $user, $id)
    {
        DB::beginTransaction();//start transaction, This is to have control over rollbacks and commits

        $request->validate([//validating the data that is being passed through. If an error occurs it will redirect back to page
            'island_id'=>'required',
        ]);

        try {
            $assign = DB::table('agent_islands')->where('id', $id)->update(['island_id' => $request->island_id]);//updating user_id and island_id directly to the table
            
            DB::commit();//This means nothing went wrong so we can commit/save to the database
            /**
             * For individual user pages we are using user's id to define the cache. If everyone used the same cache then the values will be the same
             * If there is a change then we must forget the cache so that the changes will be seen on these pages.
             */
            Cache::forget('myAgentIslandIndexID'.$user->id);//forget the cache so we see the changes.
            Cache::forget('agentIslandIndex');

        } catch(Exception $e) {
            DB::rollBack();//Something went wrong so we undo the transaction. It won't be saved to database
            return redirect("/admin/user/{$user->id}/profile/my-agent-island/{$id}/edit")->withErrors([ $e->getMessage() ])->withInput();//if it is an sql error then it will show the entire sql error. If we want we can put custom message
            
        } catch(Throwable $e) {
            DB::rollBack();//same thing here
            return redirect("/admin/user/{$user->id}/profile/my-agent-island/{$id}/edit")->withErrors([ $e->getMessage() ])->withInput();
            
        }

        return redirect("/admin/user/{$user->id}/profile/my-agent-island/")->with('Edit-success','My Assign Updated!');//if no errors then redirect back to page with alert
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     * 
     * This method is used for deleting (my) agent island assign
     */
    public function destroy(User $user, $id)
    {
        DB::beginTransaction();//start transaction, This is to have control over rollbacks and commits
        try {
            $assign = DB::table('agent_islands')->where('id', $id)->delete();//deleting it directly from the table

            DB::commit();//This means nothing went wrong so we can commit/save to the database

            /**
             * For individual user pages we are using user's id to define the cache. If everyone used the same cache then the values will be the same
             * If there is a change then we must forget the cache so that the changes will be seen on these pages.
             */
            Cache::forget('myAgentIslandIndexID'.$user->id);//forget the cache so we see the changes.
            Cache::forget('agentIslandIndex');
            
        } catch(Exception $e) {
            DB::rollBack();//Something went wrong so we undo the transaction. It won't be saved to database
            return redirect("/admin/user/{$user->id}/profile/my-agent-island/")->withErrors([ $e->getMessage() ])->withInput();//if it is an sql error then it will show the entire sql error. If we want we can put custom message
        } catch(Throwable $e) {
            DB::rollBack();//same thing here
            return redirect("/admin/user/{$user->id}/profile/my-agent-island/")->withErrors([ $e->getMessage() ])->withInput();
            
        }
        return redirect("/admin/user/{$user->id}/profile/my-agent-island/")->with('Delete-success','My Assign deleted successfully!');//if no errors then redirect back to page with alert
    }
}
