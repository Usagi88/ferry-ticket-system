<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Island;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
use Throwable;
use Illuminate\Support\Facades\Cache;

class AgentIslandController extends Controller
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
        $assignIndex = Cache::remember('agentIslandIndex', 60, function(){//Right now we are doing caching for 60 seconds
            $assigns = User::with('islandsAssignedToUser')->get();//we are getting users with islandsAssignedToUser relationship
            $assignCount = DB::table('agent_islands')->get()->count();
            return compact('assigns','assignCount');
        });
        $assigns = $assignIndex['assigns'];
        $assignCount = $assignIndex['assignCount'];
        return view('admin.island.agent-island.index', compact('assigns','assignCount'));//sending the variable to view
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     * 
     * This method is used to create a new agent island assign
     */
    public function create()
    {
        
        $agentIslandCreate = Cache::remember('agentIslandCreate', 60, function () {//Right now we are doing caching for 60 seconds
            $users = User::whereHas('roles', function($q){//getting all users with role agent 
                $q->where('name', 'Agent');
            })->get();
            $islands = Island::get();//getting all islands
            return compact('users','islands');//returning it as variables
            
        });
        $users = $agentIslandCreate["users"];//accessing the variable and initializing 
        $islands = $agentIslandCreate["islands"];
        return view('admin.island.agent-island.create',compact('users','islands'));//sending the variables to the view
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
            'island_id'=>'required',
        ]);
        try {
            $user = User::where('id', $request->user_id)->first();//finding the id the user entered and getting user
            //$user->islandsAssignedToUser()->attach($request->island_id);//then we attach island id using the islandsAssignedToUser relationship. It will save to agent_island table
            if($user->islandsAssignedToUser->contains($request->island_id)){//check table if it contains a row with user id and vessel id
                $user->islandsAssignedToUser()->detach($request->island_id);//detach it so it won't cause duplication
                $user->islandsAssignedToUser()->attach($request->island_id);//then attach it
            } else {
                $user->islandsAssignedToUser()->attach($request->island_id);//attach if not found
            }
            $user->save();//save

            DB::commit();//This means nothing went wrong so we can commit/save to the database
            Cache::forget('agentIslandIndex');//forget the cache so we see the changes.

        } catch(Exception $e) {
            DB::rollBack();//Something went wrong so we undo the transaction. It won't be saved to database
            return redirect('/admin/island/agent-island/create')->withErrors([ $e->getMessage() ])->withInput();//if it is an sql error then it will show the entire sql error. If we want we can put custom message
        } catch(Throwable $e) {
            DB::rollBack();//same thing here
            return redirect('/admin/island/agent-island/create')->withErrors([ $e->getMessage() ])->withInput();
        }

        return redirect('/admin/island/agent-island/')->with('success', 'Assign Created!');//if no errors then redirect back to page with alert
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AgentIsland  $agentIsland
     * @return \Illuminate\Http\Response
     * This method is used to show data. Individual data.
     */
    public function show($id)
    {
        $assign = DB::table('agent_islands')->where('id', $id)->get();//getting user from agent_islands table
        return view('admin.island.agent-island.show',compact('assign'));//sending variable to view
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\AgentIsland  $agentIsland
     * @return \Illuminate\Http\Response
     * 
     * This method is used to edit an agent island assign
     */
    public function edit($id)
    {
        $agentIslandEdit = Cache::remember('agentIslandEdit', 60, function () use($id) {//Right now we are doing caching for 60 seconds
            $assign = DB::table('agent_islands')->where('id', $id)->first();//getting user from agent_islands table
            $user = User::where('id', $assign->user_id)->first();
            $islands = Island::get();//getting all islands
            return compact('assign','user','islands');//returning it as variables
        });
        $assign = $agentIslandEdit["assign"];//accessing the variable and initializing 
        $user = $agentIslandEdit["user"];
        $islands = $agentIslandEdit["islands"];
        return view('admin.island.agent-island.edit',compact('assign','user','islands'));//sending the variables to the view
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AgentIsland  $agentIsland
     * @return \Illuminate\Http\Response
     * 
     * This method is what happens when we submit in edit page
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();//start transaction, This is to have control over rollbacks and commits
        $request->validate([//validating the data that is being passed through. If an error occurs it will redirect back to page
            'user_id'=>'required',
            'island_id'=>'required',
        ]);
        try {
            //updating user_id and island_id directly to the table
            DB::table('agent_islands')->where('id', $id)->update(['user_id' => $request->user_id, 'island_id' => $request->island_id]);
            
            DB::commit();//This means nothing went wrong so we can commit/save to the database
            Cache::forget('agentIslandIndex');//forget the cache so we see the changes.

        } catch(Exception $e) {
            DB::rollBack();//Something went wrong so we undo the transaction. It won't be saved to database
            return redirect('/admin/island/agent-island/edit')->withErrors([ $e->getMessage() ])->withInput();//if it is an sql error then it will show the entire sql error. If we want we can put custom message
        } catch(Throwable $e) {
            DB::rollBack();//same thing here
            return redirect('/admin/island/agent-island/edit')->withErrors([ $e->getMessage() ])->withInput();
        }

        return redirect('/admin/island/agent-island')->with('Edit-success', 'Assign Updated!');//if no errors then redirect back to page with alert
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AgentIsland  $agentIsland
     * @return \Illuminate\Http\Response
     * 
     * This method is used for deleting an agent island assign
     */
    public function destroy($id)
    {
        DB::beginTransaction();//start transaction, This is to have control over rollbacks and commits

        try {
            $assign = DB::table('agent_islands')->where('id', $id)->delete();//deleting it directly from the table

            DB::commit();//This means nothing went wrong so we can commit/save to the database
            
            Cache::forget('agentIslandIndex');//forget the cache so we see the changes.

        } catch(Exception $e) {
            DB::rollBack();//Something went wrong so we undo the transaction. It won't be saved to database
            return redirect("/admin/island/agent-island")->withErrors([ $e->getMessage() ])->withInput();//if it is an sql error then it will show the entire sql error. If we want we can put custom message
        } catch(Throwable $e) {
            DB::rollBack();
            return redirect("/admin/island/agent-island")->withErrors([ $e->getMessage() ])->withInput();
        }

        return redirect('/admin/island/agent-island')->with('Delete-success','Assign deleted successfully!');//if no errors then redirect back to page with alert
    }
}
