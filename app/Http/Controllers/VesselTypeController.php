<?php

namespace App\Http\Controllers;

use App\Models\VesselType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
use Throwable;
use Illuminate\Support\Facades\Cache;

class VesselTypeController extends Controller
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
        $vesselTypes = Cache::remember('vesselTypeIndex', 60, function(){//Right now we are doing caching for 60 seconds
            return VesselType::orderBy('id','desc')->get();//get all vessel types
        });
        return view('admin.vessel-type.index', compact('vesselTypes'));//sending the variable to view
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     * 
     * This method is used to create vessel type
     */
    public function create()
    {
        return view('admin.vessel-type.create');//sending the variables to the view
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
            'name'=>'required|unique:vessel_types,name|max:255',
            'description'=>'nullable|max:255'
        ]);

        try {
            $vesselType = new VesselType;//making a new vessel type
            $vesselType->name = $request->name;//initializing with what user entered
            $vesselType->description = $request->description;
            $vesselType->save();//save

            DB::commit();//This means nothing went wrong so we can commit/save to the database//commit/save to the database

            /**
             * We are forgetting all these caches because they are getting vessel type for their pages.
             * If there is a change then we must forget the cache so that the new vessel type will be seen on these pages.
             */
            Cache::forget('vesselTypeIndex');//when there is a change remove the cache.

            Cache::forget('myVesselCreate');
            Cache::forget('myVesselEdit');

            Cache::forget('vesselCreate');
            Cache::forget('vesselEdit');

        } catch(Exception $e) {
            DB::rollBack();//Something went wrong so we undo the transaction. It won't be saved to database
            return redirect('/admin/vessel-type/create')->withErrors([ $e->getMessage() ])->withInput();//if it is an sql error then it will show the entire sql error. If we want we can put custom message
        } catch(Throwable $e) {
            DB::rollBack();//same thing here
            return redirect('/admin/vessel-type/create')->withErrors([ $e->getMessage() ])->withInput();
        }

        return redirect('/admin/vessel-type/')->with('success', 'Vessel Type Created!');//if no errors then redirect back to page with alert
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\VesselType  $vesselType
     * @return \Illuminate\Http\Response
     * 
     * This method is used to show data. Individual data.
     */
    public function show(VesselType $vesselType)
    {
        return view('admin.vessel-type.show', compact('vesselType'));//sending the variable to the view
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\VesselType  $vesselType
     * @return \Illuminate\Http\Response
     * 
     * This method is used to edit vessel type
     */
    public function edit(VesselType $vesselType)
    {
        return view('admin.vessel-type.edit', compact('vesselType'));//sending the variable to the view
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\VesselType  $vesselType
     * @return \Illuminate\Http\Response
     * 
     * This method is what happens when we submit in edit page
     */
    public function update(Request $request, VesselType $vesselType)
    {
        DB::beginTransaction();//start transaction, This is to have control over rollbacks and commits

        $request->validate([//validating the data that is being passed through. If an error occurs it will redirect back to page
            'name'=>'required|max:255|unique:vessel_types,name,'.$vesselType->id,
            'description'=>'nullable|max:255'
        ]);

        try {
            $vesselType->name = $request->name;//initialzing with what user entered
            $vesselType->description = $request->description;
            $vesselType->save();//save

            DB::commit();//This means nothing went wrong so we can commit/save to the database

            /**
             * We are forgetting all these caches because they are getting vessel type for their pages.
             * If there is a change then we must forget the cache so that the new vessel type will be seen on these pages.
             */

            Cache::forget('vesselTypeIndex');//when there is a change remove the cache.

            Cache::forget('myVesselCreate');
            Cache::forget('myVesselEdit');

            Cache::forget('vesselCreate');
            Cache::forget('vesselEdit');

        } catch(Exception $e) {
            DB::rollBack();//Something went wrong so we undo the transaction. It won't be saved to database
            return redirect("/admin/vessel-type/{$vesselType->id}/edit")->withErrors([ $e->getMessage() ])->withInput();//if it is an sql error then it will show the entire sql error. If we want we can put custom message
        } catch(Throwable $e) {
            DB::rollBack();//same thing here
            return redirect("/admin/vessel-type/{$vesselType->id}/edit")->withErrors([ $e->getMessage() ])->withInput();
        }

        return redirect('/admin/vessel-type/')->with('Edit-success','Vessel Type Updated!');//if no errors then redirect back to page with alert
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\VesselType  $vesselType
     * @return \Illuminate\Http\Response
     * 
     * This method is used for deleting vessel type
     */
    public function destroy(VesselType $vesselType)
    {
        DB::beginTransaction();//start transaction, This is to have control over rollbacks and commits
        try {
            $vesselType->delete();//deleting it

            DB::commit();//This means nothing went wrong so we can commit/save to the database

            /**
             * We are forgetting all these caches because they are getting vessel type for their pages.
             * If there is a change then we must forget the cache so that the new vessel type will be seen on these pages.
             */
            Cache::forget('vesselTypeIndex');//when there is a change remove the cache.

            Cache::forget('myVesselCreate');
            Cache::forget('myVesselEdit');

            Cache::forget('vesselCreate');
            Cache::forget('vesselEdit');

        } catch(Exception $e) {
            DB::rollBack();//Something went wrong so we undo the transaction. It won't be saved to database
            return redirect("/admin/vessel-type/")->withErrors([ $e->getMessage() ])->withInput();//if it is an sql error then it will show the entire sql error. If we want we can put custom message
        } catch(Throwable $e) {
            DB::rollBack();//same thing here
            return redirect("/admin/vessel-type/")->withErrors([ $e->getMessage() ])->withInput();
        }
        
        return redirect('/admin/vessel-type/')->with('Delete-success','Vessel Type deleted successfully!');//if no errors then redirect back to page with alert
    }
}
