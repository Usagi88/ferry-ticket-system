<?php

namespace App\Http\Controllers;

use App\Models\TicketType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
use Throwable;
use Illuminate\Support\Facades\Cache;

class MyTicketTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(User $user)
    {
        $ticketTypeIndex = Cache::remember('myTicketTypeIndexID'.$user->id, 60, function() use($user){//Right now we are doing caching for 60 seconds
            return User::where('id',$user->id)->with('ticket_types')->orderBy('id','desc')->first();//get user's ticket types
        });
        $userT = $ticketTypeIndex;
        return view('admin.user.profile.my-ticket-type.index', compact('userT','user'));//sending the variables to the view
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(User $user)
    {
        return view('admin.user.profile.my-ticket-type.create',compact('user'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, User $user)
    {
        DB::beginTransaction();//start transaction, This is to have control over rollbacks and commits

        $request->validate([//validating the data that is being passed through. If an error occurs it will redirect back to page
            'name'=>'required|unique:ticket_types,name|max:255',
            'description'=>'nullable|max:255'
        ]);

        try {
            //dd($user->id);
            $ticketType = new TicketType;//make new ticket type
            $ticketType->name = $request->name;//initializing with what user entered
            $ticketType->description = $request->description;
            $ticketType->save();//save
           
            $ticketType->users()->attach($user->id);
            $ticketType->save();//save

            DB::commit();//This means nothing went wrong so we can commit/save to the database//commit/save to the database

            /**
             * We are forgetting all these caches because they are getting ticket type for their pages.
             * If there is a change then we must forget the cache so that the new ticket type will be seen on these pages.
             */
            Cache::forget('myTicketTypeIndexID'.$user->id);//when there is a change remove the cache.

            Cache::forget('bookingCreate');
            Cache::forget('bookingEdit');

            Cache::forget('myBookingCreate');
            Cache::forget('myBookingEdit');

            Cache::forget('myRouteCreate');
            Cache::forget('myRouteEdit');

            Cache::forget('routeCreate');
            Cache::forget('routeEdit');

        } catch(Exception $e) {
            DB::rollBack();//Something went wrong so we undo the transaction. It won't be saved to database
            return redirect("/admin/user/{$user->id}/profile/my-ticket-type/create")->withErrors([ $e->getMessage() ])->withInput();//if it is an sql error then it will show the entire sql error. If we want we can put custom message
        } catch(Throwable $e) {
            DB::rollBack();//same thing here
            return redirect("/admin/user/{$user->id}/profile/my-ticket-type/create")->withErrors([ $e->getMessage() ])->withInput();
        }
        
        return redirect("/admin/user/{$user->id}/profile/my-ticket-type/")->with('success','Ticket Type Created!');//if no errors then redirect back to page with alert
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TicketType  $ticketType
     * @return \Illuminate\Http\Response
     */
    public function show(User $user, TicketType $ticketType)
    {
        return view('admin.user.profile.my-ticket-type.show', compact('ticketType','user'));//sending the variable to the view
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TicketType  $ticketType
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user, TicketType $ticketType)
    {
        return view('admin.user.profile.my-ticket-type.edit',compact('ticketType','user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TicketType  $ticketType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user, TicketType $ticketType)
    {
        DB::beginTransaction();//start transaction, This is to have control over rollbacks and commits

        $request->validate([//validating the data that is being passed through. If an error occurs it will redirect back to page
            'name'=>'required|max:255|unique:ticket_types,name,'.$ticketType->id,
            'description'=>'nullable|max:255'
        ]);

        try {
            $ticketType->name = $request->name;//initialzing with what user entered
            $ticketType->description = $request->description;
            $ticketType->save();//save

            DB::commit();//This means nothing went wrong so we can commit/save to the database

            /**
             * We are forgetting all these caches because they are getting ticket type for their pages.
             * If there is a change then we must forget the cache so that the new ticket type will be seen on these pages.
             */
            Cache::forget('myTicketTypeIndexID'.$user->id);//when there is a change remove the cache.

            Cache::forget('bookingCreate');
            Cache::forget('bookingEdit');

            Cache::forget('myBookingCreate');
            Cache::forget('myBookingEdit');

            Cache::forget('myRouteCreate');
            Cache::forget('myRouteEdit');
            
            Cache::forget('routeCreate');
            Cache::forget('routeEdit');

        } catch(Exception $e) {
            DB::rollBack();//Something went wrong so we undo the transaction. It won't be saved to database
            return redirect("/admin/user/{$user->id}/profile/my-ticket-type/edit")->withErrors([ $e->getMessage() ])->withInput();//if it is an sql error then it will show the entire sql error. If we want we can put custom message
        } catch(Throwable $e) {
            DB::rollBack();//same thing here
            return redirect("/admin/user/{$user->id}/profile/my-ticket-type/edit")->withErrors([ $e->getMessage() ])->withInput();
        }
        
        return redirect("/admin/user/{$user->id}/profile/my-ticket-type/")->with('Edit-success','Ticket Type Updated!');//if no errors then redirect back to page with alert
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TicketType  $ticketType
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user, TicketType $ticketType)
    {
        DB::beginTransaction();//start transaction, This is to have control over rollbacks and commits
        try {
            $ticketType->delete();//deleting it

            DB::commit();//This means nothing went wrong so we can commit/save to the database

            /**
             * We are forgetting all these caches because they are getting ticket type for their pages.
             * If there is a change then we must forget the cache so that the new ticket type will be seen on these pages.
             */
            Cache::forget('myTicketTypeIndexID'.$user->id);//when there is a change remove the cache.

            Cache::forget('bookingCreate');
            Cache::forget('bookingEdit');

            Cache::forget('myBookingCreate');
            Cache::forget('myBookingEdit');

            Cache::forget('myRouteCreate');
            Cache::forget('myRouteEdit');
            
            Cache::forget('routeCreate');
            Cache::forget('routeEdit');
            
        } catch(Exception $e) {
            DB::rollBack();//Something went wrong so we undo the transaction. It won't be saved to database
            return redirect("/admin/user/{$user->id}/profile/my-ticket-type/")->withErrors([ $e->getMessage() ])->withInput();//if it is an sql error then it will show the entire sql error. If we want we can put custom message
        } catch(Throwable $e) {
            DB::rollBack();//same thing here
            return redirect("/admin/user/{$user->id}/profile/my-ticket-type/")->withErrors([ $e->getMessage() ])->withInput();
        }
        
        return redirect("/admin/user/{$user->id}/profile/my-ticket-type/")->with('Delete-success','Ticket Type deleted successfully!');//if no errors then redirect back to page with alert
    }
}
