<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Route;
use App\Models\TicketType;
use App\Models\Island;
use Illuminate\Http\Request;
use App\Http\Requests\StoreMyRouteRequest;
use Illuminate\Support\Facades\DB;
use Exception;
use Throwable;
use Illuminate\Support\Facades\Cache;

class MyRouteController extends Controller
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
        $myRouteIndex = Cache::remember('myRouteIndexID'.$user->id, 60, function() use($user){//Right now we are doing caching for 60 seconds
            $users = User::where('id',$user->id)->with('vesselsAssignedToUser')->get();//get user's ownedVessels
            $routes = Route::where('user_id',$user->id)->with('ticket_type')->get();//get user's route with ticket type relationship
            return compact('routes','users');//returning it as variables
        });
        $users = $myRouteIndex["users"];//accessing the variable and initializing 
        $routes = $myRouteIndex["routes"];
        return view('admin.user.profile.my-route.index', compact('routes','user','users'));//sending the variables to the view
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     * 
     * This method is used to create (my) route
     */
    public function create(User $user)
    {
        $myRouteCreate = Cache::remember('myRouteCreate', 60, function () {//Right now we are doing caching for 60 seconds
            $ticket_types = TicketType::get();//get all ticket types
            $islands = Island::get();//get all islands
            foreach ($islands as $key => $island) {
                $islandNames[] = $island->atoll.".".$island->name;
            }
            return compact('islands','ticket_types','islandNames');//returning it as variables
        });
        $ticket_types = $myRouteCreate["ticket_types"];//accessing the variable and initializing 
        $islandNames = $myRouteCreate["islandNames"];

        return view('admin.user.profile.my-route.create',compact('ticket_types','islandNames', 'user'));//sending the variables to the view
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * 
     * This method is what happens when we submit in create page
     */
    public function store(Request $request, StoreMyRouteRequest $requestchange, User $user)
    {
        DB::beginTransaction();//start transaction, This is to have control over rollbacks and commits
        
        /**
         * Using storeMyRoute request form to validate the data. In the request form it will 
         * prepare the data before it is validated. The data entering can be multiple
         * and it is in a single string with comma that separate them. Eg. male,hulhumale,vilingili
         * We are splitting it and inserting it in an array. After that we merge it to the field.
         * Next, it will validate the field using the rule created inside the request form.
         */
        $requestchange->validated();//if an error occurs it will redirect back to page

        try {
            $listOfOrigins = $requestchange->origin;//Array of Origins array
            $listOfDestinations = $requestchange->destination;//Array of Destinations
            $listOfPrices = $requestchange->price;//Array of Prices
            $listOfTicketTypes = $requestchange->ticket_type_id;//Array of Ticket types

            if(count($listOfOrigins) != count($listOfDestinations) ){//if origin and destination amount is not equal then redirect back with error
                return redirect("/admin/user/{$user->id}/profile/my-route/create")->withErrors(['Array mismatch'=>'Origin and Destination are not same amount']);
            }

            foreach($listOfOrigins as $index => $origin) {//for each origin there will be 3 prices (Adult,Child,Infant) so we are looping through prices too
                $route = new Route;//making a new route
                foreach($listOfPrices as $index2 => $listOfPrice){
                    $route->user_id = $user->id;
                    $route->origin = $origin;//initializing with what user entered
                    $route->destination = $listOfDestinations[$index];//index is acting as a key to identify the origin loop amount
                    $route->route_code = $requestchange->origin[$index] . '_' . $requestchange->destination[$index];//combining 2 strings to make route_code
                    $route->duration = $requestchange->duration;

                    $route->save();//save
                    $route->allTicketTypeOfRoute()->attach($listOfTicketTypes[$index2],array('user_id' => $user->id,'price' => $listOfPrice));
                }
            }

            DB::commit();//This means nothing went wrong so we can commit/save to the database//commit/save to the database
            
            /**
             * For individual user pages we are using user's id to define the cache. If everyone used the same cache then the values will be the same
             * If there is a change then we must forget the cache so that the changes will be seen on these pages.
             */
            Cache::forget('myRouteIndexID'.$user->id);//forget the cache so we see the changes.
            Cache::forget('routeIndex');//forget the cache so we see the changes.

        } catch(Exception $e) {
            DB::rollBack();//Something went wrong so we undo the transaction. It won't be saved to database
            return redirect("/admin/user/{$user->id}/profile/my-route/create")->withErrors([ $e->getMessage() ])->withInput();//if it is an sql error then it will show the entire sql error. If we want we can put custom message
        } catch(Throwable $e) {
            DB::rollBack();//same thing here
            return redirect("/admin/user/{$user->id}/profile/my-route/create")->withErrors([ $e->getMessage() ])->withInput();
        }
        
        return redirect("/admin/user/{$user->id}/profile/my-route/")->with('success', 'Route Created!');//if no errors then redirect back to page with alert
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     * 
     * This method is used to show data. Individual data.
     */
    public function show(User $user, Route $route)
    {
        return view('admin.user.profile.my-route.show', compact('user','route'));//sending the variable to the view
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     * 
     * This method is used to edit (my) route
     */
    public function edit(User $user, Route $route)
    {
        $myRouteEdit = Cache::remember('myRouteEdit', 60, function () {//Right now we are doing caching for 60 seconds
            $ticket_types = TicketType::get();//get all ticket types
            $islands = Island::get();//get all islands
            return compact('ticket_types','islands');//returning it as variables
        });
        $ticket_types = $myRouteEdit["ticket_types"];//accessing the variable and initializing 
        $islands = $myRouteEdit["islands"];
        return view('admin.user.profile.my-route.edit', compact('user','route','ticket_types','islands'));//sending the variables to the view
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
    public function update(Request $request, User $user, Route $route)
    {
        DB::beginTransaction();//start transaction, This is to have control over rollbacks and commits

        $request->validate([//validating the data that is being passed through. If an error occurs it will redirect back to page
            'origin'=>'required|max:255',
            'destination'=>'required|max:255',
            'route_code'=>'max:255',
            'duration'=>'required',
            'ticket_type_id.*'=>'required',
            'price.*'=>'required|integer|max:10000'
        ]);

        try {
            $route->origin = $request->origin;//initialzing with what user entered
            $route->destination = $request->destination;
            $route->route_code = $request->route_code;
            $route->duration = $request->duration;

            $listOfPrices = $request->price;;//Array of Prices
            $listOfTicketTypes = $request->ticket_type_id;//Array of Ticket Types
            foreach($listOfPrices as $index2 => $listOfPrice){
                $route->allTicketTypeOfRoute()->updateExistingPivot($listOfTicketTypes[$index2],array('user_id' => $user->id,'price' => $listOfPrice));
            }
            $route->save();//save

            DB::commit();//This means nothing went wrong so we can commit/save to the database

            /**
             * For individual user pages we are using user's id to define the cache. If everyone used the same cache then the values will be the same
             * If there is a change then we must forget the cache so that the changes will be seen on these pages.
             */
            Cache::forget('myRouteIndexID'.$user->id);//forget the cache so we see the changes.
            Cache::forget('routeIndex');

        } catch(Exception $e) {
            DB::rollBack();//Something went wrong so we undo the transaction. It won't be saved to database
            return redirect("/admin/user/{$user->id}/profile/my-route/{$route->id}/edit")->withErrors([ $e->getMessage() ])->withInput();//if it is an sql error then it will show the entire sql error. If we want we can put custom message
        } catch(Throwable $e) {
            DB::rollBack();//same thing here
            return redirect("/admin/user/{$user->id}/profile/my-route/{$route->id}/edit")->withErrors([ $e->getMessage() ])->withInput();
        }

        return redirect("/admin/user/{$user->id}/profile/my-route/")->with('Edit-success','Route Updated!');//if no errors then redirect back to page with alert
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     * 
     * This method is used for deleting (my) route
     */
    public function destroy(User $user, Route $route)
    {
        DB::beginTransaction();//start transaction, This is to have control over rollbacks and commits
        try {
            $route->delete();//deleting it

            DB::commit();//This means nothing went wrong so we can commit/save to the database

            /**
             * For individual user pages we are using user's id to define the cache. If everyone used the same cache then the values will be the same
             * If there is a change then we must forget the cache so that the changes will be seen on these pages.
             */
            Cache::forget('myRouteIndexID'.$user->id);//forget the cache so we see the changes.
            Cache::forget('routeIndex');
            
        } catch(Exception $e) {
            DB::rollBack();//Something went wrong so we undo the transaction. It won't be saved to database
            return redirect("/admin/user/{$user->id}/profile/my-route/")->withErrors([ $e->getMessage() ])->withInput();//if it is an sql error then it will show the entire sql error. If we want we can put custom message
        } catch(Throwable $e) {
            DB::rollBack();//same thing here
            return redirect("/admin/user/{$user->id}/profile/my-route/")->withErrors([ $e->getMessage() ])->withInput();
        }
        
        return redirect("/admin/user/{$user->id}/profile/my-route/")->with('Delete-success','Route deleted successfully!');//if no errors then redirect back to page with alert
    }
}
