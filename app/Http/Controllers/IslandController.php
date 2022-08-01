<?php

namespace App\Http\Controllers;

use App\Models\Island;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
use Throwable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class IslandController extends Controller
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
        $islands = Cache::remember('islandIndex', 60, function(){//Right now we are doing caching for 60 seconds
            return Island::orderBy('id','desc')->get();//get all islands ordered with descend
        });
        return view('admin.island.index', compact('islands'));//sending the variable to view
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     * 
     * This method is used to create an island
     */
    public function create()
    {
        return view('admin.island.create');//return to the view
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
            'atoll'=>'required|max:255',
            'name'=>'required|unique:islands,name|max:255'
        ]);
        try {
            $island = new Island;//making a new island
            $island->atoll = $request->atoll;//initializing it with what user entered
            $island->name = $request->name;
            $island->save();//save

            DB::commit();//This means nothing went wrong so we can commit/save to the database
            /**
             * We are forgetting all these caches because, they are getting island for their pages.
             * If there is a change then we must forget the cache so that the new island will be seen on these pages.
             */
            Cache::forget('islandIndex');//forget the cache so we see the changes.

            Cache::forget('myRouteCreate');//when there is a change remove the cache.
            Cache::forget('myRouteEdit');

            Cache::forget('routeCreate');
            Cache::forget('routeEdit');

            Cache::forget('agentIslandCreate');
            Cache::forget('agentIslandEdit');

            Cache::forget('myAgentIslandCreate');
            Cache::forget('myAgentIslandEdit');
            

        } catch(Exception $e) {
            DB::rollBack();//Something went wrong so we undo the transaction. It won't be saved to database
            return redirect('/admin/island/create')->withErrors([ $e->getMessage() ])->withInput();//if it is an sql error then it will show the entire sql error. If we want we can put custom message
        } catch(Throwable $e) {
            DB::rollBack();//same thing here
            return redirect('/admin/island/create')->withErrors([ $e->getMessage() ])->withInput();
        }
        

        return redirect('/admin/island/')->with('success', 'Island Created!');//if no errors then redirect back to page with alert
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Island  $island
     * @return \Illuminate\Http\Response
     * 
     * This method is used to show data. Individual data.
     */
    public function show(Island $island)
    {
        return view('admin.island.show', compact('island'));//sending the variable to the view
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Island  $island
     * @return \Illuminate\Http\Response
     * 
     * This method is used to edit an island
     */
    public function edit(Island $island)
    {
        return view('admin.island.edit', compact('island'));//sending the variable to the view
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Island  $island
     * @return \Illuminate\Http\Response
     * 
     * This method is what happens when we submit in edit page
     */
    public function update(Request $request, Island $island)
    {
        DB::beginTransaction();//start transaction, This is to have control over rollbacks and commits
        
        $request->validate([//validating the data that is being passed through. If an error occurs it will redirect back to page
            'atoll'=>'required|max:255',
            'name'=>'required|max:255',
        ]);

        try {
            $island->atoll = $request->atoll;//initialzing with what user entered
            $island->name = $request->name;
            $island->save();//save

            DB::commit();//This means nothing went wrong so we can commit/save to the database

            Cache::forget('islandIndex');//forget the cache so we see the changes.
            /**
             * We are forgetting all these caches because they are getting island for their pages.
             * If there is a change then we must forget the cache so that the new island will be seen on these pages.
             */
            Cache::forget('myRouteCreate');//when there is a change remove the cache.
            Cache::forget('myRouteEdit');
            
            Cache::forget('routeCreate');
            Cache::forget('routeEdit');

            Cache::forget('agentIslandCreate');
            Cache::forget('agentIslandEdit');

            Cache::forget('myAgentIslandCreate');
            Cache::forget('myAgentIslandEdit');

        } catch(Exception $e) {
            DB::rollBack();//Something went wrong so we undo the transaction. It won't be saved to database
            return redirect("/admin/island/{$island->id}/edit")->withErrors([ $e->getMessage() ])->withInput();//if it is an sql error then it will show the entire sql error. If we want we can put custom message
            
        } catch(Throwable $e) {
            DB::rollBack();//same thing here
            return redirect("/admin/island/{$island->id}/edit")->withErrors([ $e->getMessage() ])->withInput();
            
        }
        
        return redirect('/admin/island/')->with('Edit-success','Island Updated!');//if no errors then redirect back to page with alert
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Island  $island
     * @return \Illuminate\Http\Response
     * 
     * This method is used for deleting an island
     */
    public function destroy(Island $island)
    {
        DB::beginTransaction();//start transaction, This is to have control over rollbacks and commits
        try {
            $island->delete();//deleting it

            DB::commit();//This means nothing went wrong so we can commit/save to the database

            Cache::forget('islandIndex');//forget the cache so we see the changes.
            /**
             * We are forgetting all these caches because they are getting island for their pages.
             * If there is a change then we must forget the cache so that the new island will be seen on these pages.
             */
            Cache::forget('myRouteCreate');//when there is a change remove the cache.
            Cache::forget('myRouteEdit');

            Cache::forget('routeCreate');
            Cache::forget('routeEdit');

            Cache::forget('agentIslandCreate');
            Cache::forget('agentIslandEdit');

            Cache::forget('myAgentIslandCreate');
            Cache::forget('myAgentIslandEdit');
            
        } catch(Exception $e) {
            DB::rollBack();//Something went wrong so we undo the transaction. It won't be saved to database
            return redirect("/admin/island/")->withErrors([ $e->getMessage() ])->withInput();//if it is an sql error then it will show the entire sql error. If we want we can put custom message
        } catch(Throwable $e) {
            DB::rollBack();//same thing here
            return redirect("/admin/island/")->withErrors([ $e->getMessage() ])->withInput();
        }
        
        return redirect('/admin/island/')->with('Delete-success','Island deleted successfully!');//if no errors then redirect back to page with alert
    }
}
