<?php

namespace App\Http\Controllers;

use App\Models\Route;
use App\Models\TicketType;
use App\Models\User;
use App\Models\Vessel;
use App\Models\Island;
use Illuminate\Http\Request;
use App\Http\Requests\StoreRouteRequest;
use Illuminate\Support\Facades\DB;
use Exception;
use Throwable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;

class RouteController extends Controller
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
        $routeIndex = Cache::remember('routeIndex', 60, function(){//Right now we are doing caching for 60 seconds
            $vessels = Vessel::get();//get all vessels
            $routes = Route::with('allTicketTypeOfRoute')->get();//get route's with ticket_type relationship
            $users = User::get();//get all users
            return compact('routes','vessels','users');//returning it as variables
        });
        $vessels = $routeIndex["vessels"];//accessing the variable and initializing 
        $routes = $routeIndex["routes"];
        $users = $routeIndex["users"];
        return view('admin.route.index', compact('routes','vessels','users'));//sending the variables to the view
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     * 
     * This method is used to create route
     */
    public function create(Request $request,)
    {
        $routeCreate = Cache::remember('routeCreate', 60, function () {//Right now we are doing caching for 60 seconds
            //$ticket_types = TicketType::get();//get all ticket types
            $islands = Island::get();//get all islands
            foreach ($islands as $key => $island) {
                $islandNames[] = $island->atoll.".".$island->name;
            }
            $users = User::get();//get all users
            $customTicket = User::where('id',Auth::id())->with('ticket_types')->first();
            return compact('users','islandNames','customTicket');//returning it as variables
        });

        //$ticket_types = $routeCreate["ticket_types"];
        //$islands = $routeCreate["islands"];
        $users = $routeCreate["users"];//accessing the variable and initializing 
        $islandNames = $routeCreate["islandNames"];
        $customTicket = $routeCreate["customTicket"];

        return view('admin.route.create',compact('users','islandNames','customTicket'));//sending the variables to the view
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * 
     * This method is what happens when we submit in create page
     */
    public function store(Request $request, StoreRouteRequest $requestchange)
    {
        
        DB::beginTransaction();//start transaction, This is to have control over rollbacks and commits

        /**
         * Using storeRoute request form to validate the data. In the request form it will 
         * prepare the data before it is validated. The data entering can be multiple
         * and it is in a single string with comma that separate them. Eg. male,hulhumale,vilingili
         * We are splitting it and inserting it in an array. After that we merge it to the field.
         * Next, it will validate the field using the rule created inside the request form.
         */
        $requestchange->validated();//if an error occurs it will redirect back to page
        try {
            $listOfOrigins = $requestchange->origin;//Array of Origins
            $listOfDestinations = $requestchange->destination;//Array of Destinations
            $listOfPrices = $requestchange->price;//Array of Prices
            //$listOfTicketTypes = $requestchange->ticket_type_id;//Array of Ticket Types
            $listOfCustomTicketPrice = $requestchange->custom_ticket_price;
            $listOfCustomTicketID = $requestchange->custom_ticket_id;
            $listOfDepartureTime = $requestchange->departure_time;
            $listOfCustomTicketSize = $requestchange->customTicketCount;
            
            //if origin and destination amount is not equal then redirect back with error
            if(count($listOfOrigins) != count($listOfDestinations) ){
                return redirect('/admin/route/create')->withErrors(['Array mismatch'=>'Origin and Destination are not same amount.']);
            }
            //if first origin is repeated in another origin field then give error
            for ($i=1; $i < count($listOfOrigins); $i++) {
                if($listOfOrigins[0] == $listOfOrigins[$i]){
                    return redirect('/admin/route/create')->withErrors(['Array mismatch'=>'Same origin and cannot be placed again.']);
                }
            }
            //cleaning the number of custom tickets from each route and making an array out of it
            $listOfCustomTicketSize = str_replace(array('[', ']'), '', $listOfCustomTicketSize);
            $listOfCustomTicketSize = explode(',', $listOfCustomTicketSize);
            
            //Split custom id to separate arrays. Those arrays will be used to check for duplicates
            $counter = 0;
            for ($i=0; $i < count($listOfCustomTicketSize); $i++) {
                $listOfCustomTicketID2 = [];
                for ($x=0; $x < $listOfCustomTicketSize[$i]; $x++) { 
                    $listOfCustomTicketID2[] = $listOfCustomTicketID[$counter];
                    $counter = $counter + 1;
                }
                $listOfCustomTicketIDModified[] = $listOfCustomTicketID2;
            }
            
            //Checking for duplicate custom ticket. If count is not equal to unique values then give error
            for ($i=0; $i < count($listOfCustomTicketIDModified); $i++) { 
                $checkDuplicateCustomTicket = count($listOfCustomTicketIDModified[$i]) !== count(array_unique($listOfCustomTicketIDModified[$i]));
                if($checkDuplicateCustomTicket){
                    return redirect('/admin/route/create')->withErrors(['Duplicate custom ticket'=>'Duplicate custom ticket in a route. Same ticket cannot be placed again.']);
                }
            }
            
            //get price list and put it in an array of 3 values, Eg: 0: adult, child, infant, 1: adult, child, infant
            foreach ($listOfOrigins as $key => $value) {
                $listOfPricesModified[] = ['Adult' => $listOfPrices[$key], 'Child' => $listOfPrices[$key+1], 'Infant' => $listOfPrices[$key+2]];
                $key = $key + 3;
            }
            
            //get custom ticket's name
            if($listOfCustomTicketID != null){
                foreach ($listOfCustomTicketID as $value) {
                    $listOfCustomTicketName[] = TicketType::where('id',$value)->first()->name;
                }
                //Put custom ticket name and price together. Eg: customTicket1 => 50, customTicket2 => 40
                $counter = 0;
                for ($i=0; $i < count($listOfCustomTicketSize); $i++) { 
                    $listOfCustomTicketNameAndPrice = [];
                    for ($x=0; $x < $listOfCustomTicketSize[$i] ; $x++) { 
                        $listOfCustomTicketNameAndPrice[$listOfCustomTicketName[$counter]] = $listOfCustomTicketPrice[$counter];
                        $counter = $counter + 1;
                    }
                    $listOfCustomTicketNameAndPriceModified[] = $listOfCustomTicketNameAndPrice;
                }
                
                $route = new Route;
                $route->user_id = $requestchange->user_id;
                $route->route_name = $requestchange->route_name;
                foreach ($listOfOrigins as $key => $value) {
                    $data[] = [
                        'Origin' => $listOfOrigins[$key],
                        'Destination' => $listOfDestinations[$key],
                        'Departure_time' => $listOfDepartureTime[$key],
                        'Price_list' => $listOfPricesModified[$key],
                        'Custom_ticket'=> $listOfCustomTicketNameAndPriceModified[$key],
                    ];
                }
                $route->data = $data;
                $route->save();
                DB::commit();//This means nothing went wrong so we can commit/save to the database//commit/save to the database

            } else {
                $route = new Route;
                $route->user_id = $requestchange->user_id;
                $route->route_name = $requestchange->route_name;
                foreach ($listOfOrigins as $key => $value) {
                    $data[] = [
                        'Origin' => $listOfOrigins[$key],
                        'Destination' => $listOfDestinations[$key],
                        'Departure_time' => $listOfDepartureTime[$key],
                        'Price_list' => $listOfPricesModified[$key],
                        'Custom_ticket'=> null,
                    ];
                }
                $route->data = $data;
                $route->save();
                DB::commit();//This means nothing went wrong so we can commit/save to the database//commit/save to the database
            }
            
            
            
            
            //splitting the array of custom ticket name based on the number of custom ticket of a route
            
            // for ($i=0; $i < count($listOfCustomTicketSize); $i++) { 
            //     if($i == 0 ){
            //         $listOfCustomTicketNameAndPriceModified[] = array_slice($listOfCustomTicketNameAndPrice, 0, $listOfCustomTicketSize[$i]);
            //     }else{
            //         $listOfCustomTicketNameAndPriceModified[] = array_slice($listOfCustomTicketNameAndPrice, $listOfCustomTicketSize[$i-1], $listOfCustomTicketSize[$i]);
            //     }
            // }
            
            //splitting the array of custom ticket name based on the number of custom ticket of a route
            // for ($i=0; $i < count($listOfCustomTicketSize); $i++) { 
            //     if($i == 0 ){
            //         $listOfCustomTicketPriceModified[] = array_slice($listOfCustomTicketPrice, 0, $listOfCustomTicketSize[$i]);
            //     }else{
            //         $listOfCustomTicketPriceModified[] = array_slice($listOfCustomTicketPrice, $listOfCustomTicketSize[$i-1], $listOfCustomTicketSize[$i]);
            //     }
            // }
            
            

            /* 
                //$template = json_encode(['User_id'=>$requestchange->user_id,'Route_name'=>$requestchange->route_name]);
                //dd($template);
                //$output = array();
                //foreach ($listOfCustomTicketID as $keyTwo => $customTicket) {
                    //$customTicketArr[] = json_decode([$listOfCustomTicketName[$keyTwo] => $listOfCustomTicketPrice[$keyTwo]]);
                //    $customTicketArr[] = [$listOfCustomTicketName[$keyTwo] => $listOfCustomTicketPrice[$keyTwo]];
                    //array_push($output, array($listOfCustomTicketName[$keyTwo], $listOfCustomTicketPrice[$keyTwo]));

                //}
                
                //$counter = 0;
                //foreach ($listOfOrigins as $keyOne => $origin) {
                    
                //    $prices = ['Adult'=> $listOfPrices[$counter], 'Child'=> $listOfPrices[$counter+1], 'Infant'=> $listOfPrices[$counter+2], 'Custom_ticket' => $customTicketArr];
                //    $routeTemplate[] = ['Origin'=> $listOfOrigins[$keyOne], 'Destination' => $listOfDestinations[$keyOne], 'Departure_time' => $listOfDepartureTime[$keyOne], 'Price_list'=>$prices];
                    
                //    $counter = $counter + 3;
                //}
                
                //foreach ($routeTemplate as $key => $value) {
                //    $route->data = json_encode([$routeTemplate[$key]]);
                //}
                
                //dd("works");
                

                // foreach($listOfOrigins as $index => $origin) {//for each origin there will be 3 prices (Adult,Child,Infant) so we are looping through prices too
                //     $route = new Route;//making a new route
                //     foreach($listOfPrices as $index2 => $listOfPrice){
                        
                //         $route->user_id = $requestchange->user_id;//initializing with what user entered
                //         $route->origin = $origin;
                //         $route->destination = $listOfDestinations[$index];//index is acting as a key to identify the origin loop amount
                //         $route->route_code = 'U' . $route->user_id . '_' . $requestchange->origin[$index] . '_' . $requestchange->destination[$index];//combining 4 strings to make route_code
                //         $route->duration = $requestchange->duration;
                //         //$route->ticket_type_id = $listOfTicketTypes[$index2];//ticket type is also 3, index2 is acting as key to identify the price loop amount
                //         //$route->price = $listOfPrice;//the name says listOfPrice but it is actually 1 element
                //         $route->save();//save
                //         //attaching to route ticket table with additional fields since our table requires them now. Usually it would be just ticket id
                //         $route->allTicketTypeOfRoute()->attach($listOfTicketTypes[$index2],array('user_id' => $requestchange->user_id,'price' => $listOfPrice));
                        
                //     }
                // }
                //dd($route);
                //$listOfPermissionsID = TicketType::whereIn('id',$listOfTicketTypes)->pluck('id')->toArray();//Array of permission ID
                //dd($listOfPermissionsID);
                //$route->allTicketTypeOfRoute()->attach("1");
            */
            
           

            /**
             * We are forgetting all these caches because they are getting route for their pages.
             * If there is a change then we must forget the cache so that the new route will be seen on these pages.
             */
            Cache::forget('routeIndex');//when there is a change remove the cache.
            
            Cache::forget('myScheduleCreate');
            Cache::forget('myScheduleEdit');

            Cache::forget('scheduleCreate');
            Cache::forget('scheduleEdit');

        } catch(Exception $e) {
            DB::rollBack();//Something went wrong so we undo the transaction. It won't be saved to database
            return redirect("/admin/route/create")->withErrors([ $e->getMessage() ])->withInput();//if it is an sql error then it will show the entire sql error. If we want we can put custom message
        } catch(Throwable $e) {
            DB::rollBack();//same thing here
            return redirect("/admin/route/create")->withErrors([ $e->getMessage() ])->withInput();
        }
        
        return redirect('/admin/route/')->with('success', 'Route Created!');//if no errors then redirect back to page with alert
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Route  $route
     * @return \Illuminate\Http\Response
     * 
     * This method is used to show data. Individual data.
     */
    public function show(Route $route)
    {
        return view('admin.route.show', compact('route'));//sending the variable to the view
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Route  $route
     * @return \Illuminate\Http\Response
     * 
     * This method is used to edit route
     */
    public function edit(Route $route)
    {
        $routeEdit = Cache::remember('routeEdit', 60, function () {//Right now we are doing caching for 60 seconds
            //$ticket_types = TicketType::get();//get all ticket types
            //$ticket_types = $ticket_types->splice(3);//except the first 3 which is adult,child,infant
            $customTicket = User::where('id',Auth::id())->with('ticket_types')->first();
            $islands = Island::get();//get all islands
            foreach ($islands as $key => $island) {
                $islandNames[] = $island->atoll.".".$island->name;
            }
            $users = User::get();//get all users
            //$customTicket = TicketType::get();//get all ticketType
            return compact('users','customTicket','islandNames');//returning it as variables
        });

        $customTicket = $routeEdit["customTicket"];//accessing the variable and initializing 
        //$islands = $routeEdit["islands"];
        $users = $routeEdit["users"];
        $islandNames = $routeEdit["islandNames"];
        //$customTicket = $routeEdit["customTicket"];
        // $testThis = $route->data[0]['Custom_ticket'];
        // foreach ($route->data[0]['Custom_ticket'] as $key => $value) {
        //     $valueArray[] = $value;
        // }
        
        //dd($valueArray);
        return view('admin.route.edit', compact('route','customTicket','users','islandNames'));//sending the variables to the view
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Route  $route
     * @return \Illuminate\Http\Response
     * 
     * This method is what happens when we submit in edit page
     */
    public function update(Request $request, Route $route)
    {

        DB::beginTransaction();//start transaction, This is to have control over rollbacks and commits

        $request->validate([//validating the data that is being passed through. If an error occurs it will redirect back to page
            'user_id'=>'required',
            'origin.*'=>'required|max:255',
            'destination.*'=>'required|max:255',
            'route_name'=>'required|max:255',
            'ticket_type_id.*'=>'required',
            'price.*'=>'required|integer|max:10000',
            'custom_ticket_id.*'=>'required',
            'custom_ticket_price.*'=> 'required|integer|max:10000',
        ]);
        
        try {
            $listOfOrigins = $request->origin;//Array of Origins
            $listOfDestinations = $request->destination;//Array of Destinations
            $listOfPrices = $request->price;//Array of Prices
            //$listOfTicketTypes = $request->ticket_type_id;//Array of Ticket Types
            $listOfCustomTicketPrice = $request->custom_ticket_price;
            $listOfCustomTicketID = $request->custom_ticket_id;
            $listOfDepartureTime = $request->departure_time;
            $listOfCustomTicketSize = $request->customTicketCount;
            
            //if origin and destination amount is not equal then redirect back with error
            if(count($listOfOrigins) != count($listOfDestinations) ){
                return redirect('/admin/route/create')->withErrors(['Array mismatch'=>'Origin and Destination are not same amount.']);
            }
            //if first origin is repeated in another origin field then give error
            for ($i=1; $i < count($listOfOrigins); $i++) {
                if($listOfOrigins[0] == $listOfOrigins[$i]){
                    return redirect('/admin/route/create')->withErrors(['Array mismatch'=>'Same origin and cannot be placed again.']);
                }
            }
            //cleaning the number of custom tickets from each route and making an array out of it
            $listOfCustomTicketSize = str_replace(array('[', ']'), '', $listOfCustomTicketSize);
            $listOfCustomTicketSize = explode(',', $listOfCustomTicketSize);
            
            //Split custom id to separate arrays. Those arrays will be used to check for duplicates
            $counter = 0;
            for ($i=0; $i < count($listOfCustomTicketSize); $i++) {
                $listOfCustomTicketID2 = [];
                for ($x=0; $x < $listOfCustomTicketSize[$i]; $x++) { 
                    $listOfCustomTicketID2[] = $listOfCustomTicketID[$counter];
                    $counter = $counter + 1;
                }
                $listOfCustomTicketIDModified[] = $listOfCustomTicketID2;
            }
            
            //Checking for duplicate custom ticket. If count is not equal to unique values then give error
            for ($i=0; $i < count($listOfCustomTicketIDModified); $i++) { 
                $checkDuplicateCustomTicket = count($listOfCustomTicketIDModified[$i]) !== count(array_unique($listOfCustomTicketIDModified[$i]));
                if($checkDuplicateCustomTicket){
                    return redirect('/admin/route/create')->withErrors(['Duplicate custom ticket'=>'Duplicate custom ticket in a route. Same ticket cannot be placed again.']);
                }
            }
            
            //get price list and put it in an array of 3 values, Eg: 0: adult, child, infant, 1: adult, child, infant
            foreach ($listOfOrigins as $key => $value) {
                $listOfPricesModified[] = ['Adult' => $listOfPrices[$key], 'Child' => $listOfPrices[$key+1], 'Infant' => $listOfPrices[$key+2]];
                $key = $key + 3;
            }
            
            //get custom ticket's name
            if($listOfCustomTicketID != null){
                foreach ($listOfCustomTicketID as $value) {
                    $listOfCustomTicketName[] = TicketType::where('id',$value)->first()->name;
                }
                //Put custom ticket name and price together. Eg: customTicket1 => 50, customTicket2 => 40
                $counter = 0;
                for ($i=0; $i < count($listOfCustomTicketSize); $i++) { 
                    $listOfCustomTicketNameAndPrice = [];
                    for ($x=0; $x < $listOfCustomTicketSize[$i] ; $x++) { 
                        $listOfCustomTicketNameAndPrice[$listOfCustomTicketName[$counter]] = $listOfCustomTicketPrice[$counter];
                        $counter = $counter + 1;
                    }
                    $listOfCustomTicketNameAndPriceModified[] = $listOfCustomTicketNameAndPrice;
                }
                
                $route->user_id = $request->user_id;
                $route->route_name = $request->route_name;
                foreach ($listOfOrigins as $key => $value) {
                    $data[] = [
                        'Origin' => $listOfOrigins[$key],
                        'Destination' => $listOfDestinations[$key],
                        'Departure_time' => $listOfDepartureTime[$key],
                        'Price_list' => $listOfPricesModified[$key],
                        'Custom_ticket'=> $listOfCustomTicketNameAndPriceModified[$key],
                    ];
                }
                $route->data = $data;
                $route->save();
                DB::commit();//This means nothing went wrong so we can commit/save to the database//commit/save to the database
                
            } else {
                $route->user_id = $request->user_id;
                $route->route_name = $request->route_name;
                foreach ($listOfOrigins as $key => $value) {
                    $data[] = [
                        'Origin' => $listOfOrigins[$key],
                        'Destination' => $listOfDestinations[$key],
                        'Departure_time' => $listOfDepartureTime[$key],
                        'Price_list' => $listOfPricesModified[$key],
                        'Custom_ticket'=> null,
                    ];
                }
                $route->data = $data;
                $route->save();
                DB::commit();//This means nothing went wrong so we can commit/save to the database//commit/save to the database
            }
            /**
             * We are forgetting all these caches because they are getting route for their pages.
             * If there is a change then we must forget the cache so that the new route will be seen on these pages.
             */
            Cache::forget('routeIndex');//when there is a change remove the cache.

            Cache::forget('myScheduleCreate');
            Cache::forget('myScheduleEdit');

            Cache::forget('scheduleCreate');
            Cache::forget('scheduleEdit');

        } catch(Exception $e) {
            DB::rollBack();//Something went wrong so we undo the transaction. It won't be saved to database
            return redirect("/admin/route/{$route->id}/edit")->withErrors([ $e->getMessage() ])->withInput();//if it is an sql error then it will show the entire sql error. If we want we can put custom message
        } catch(Throwable $e) {
            DB::rollBack();//same thing here
            return redirect("/admin/route/{$route->id}/edit")->withErrors([ $e->getMessage() ])->withInput();
        }

        return redirect('/admin/route/')->with('Edit-success','Route Updated!');//if no errors then redirect back to page with alert
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Route  $route
     * @return \Illuminate\Http\Response
     * 
     * This method is used for deleting route
     */
    public function destroy(Route $route)
    {
        DB::beginTransaction();//start transaction, This is to have control over rollbacks and commits
        try {
            $route->delete();//deleting it

            DB::commit();//This means nothing went wrong so we can commit/save to the database

            /**
             * We are forgetting all these caches because they are getting route for their pages.
             * If there is a change then we must forget the cache so that the new route will be seen on these pages.
             */
            Cache::forget('routeIndex');//when there is a change remove the cache.

            Cache::forget('myScheduleCreate');
            Cache::forget('myScheduleEdit');

            Cache::forget('scheduleCreate');
            Cache::forget('scheduleEdit');

        } catch(Exception $e) {
            DB::rollBack();//Something went wrong so we undo the transaction. It won't be saved to database
            return redirect("/admin/route/")->withErrors([ $e->getMessage() ])->withInput();//if it is an sql error then it will show the entire sql error. If we want we can put custom message
        } catch(Throwable $e) {
            DB::rollBack();//same thing here
            return redirect("/admin/route/")->withErrors([ $e->getMessage() ])->withInput();
        }
        
        return redirect('/admin/route/')->with('Delete-success','Route deleted successfully!');//if no errors then redirect back to page with alert
    }
}
