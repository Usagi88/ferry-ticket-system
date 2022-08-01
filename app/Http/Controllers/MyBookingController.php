<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Booking;
use App\Models\TicketType;
use App\Models\BookingStatus;
use App\Models\Schedule;
use App\Models\Route;
use App\Models\Vessel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
use Throwable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class MyBookingController extends Controller
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
        /**
         * Using user id to make the cache unique.
         */
        $bookings = Cache::remember('myBookingIndexID'.$user->id, 60, function() use($user){//Right now we are doing caching for 60 seconds
            //get booking with vessel,schedule,ticket_type,booking_status,schedule's route relationship
            return Booking::where('user_id',$user->id)->with('vessel','schedule','ticket_type','booking_status','schedule.route')->get();
        });
        return view('admin.user.profile.my-booking.index', compact('bookings','user'));//sending the variables to view
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     * 
     * This method is used to create (my) booking
     */
    public function create(User $user)
    {
        $myBookingCreate = Cache::remember('myBookingCreate', 60, function () {//Right now we are doing caching for 60 seconds
            $ticket_types = TicketType::get();//get all ticket types
            $schedules = Schedule::with('route')->get();//get all schedules with route relationship
            return compact('schedules','ticket_types');//returning it as variables
        });

        $ticket_types = $myBookingCreate["ticket_types"];//accessing the variable and initializing 
        $schedules = $myBookingCreate["schedules"];
        return view('admin.user.profile.my-booking.create', compact('user','schedules','ticket_types'));//sending the variables to the view

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
            'schedule_id'=>'required',
            'ticket_type_id'=>'required',
            'ticket_quantity'=>'required'
        ]);
        
        try {
            $schedule_id = Schedule::find($request->schedule_id);//getting schedule based on schedule id
            //$price = $schedule_id->route->price;//getting that schedule's route's price
            $price = $schedule_id->route->allTicketTypeOfRoute->where('id',$request->ticket_type_id)->first()->pivot->price;//getting that schedule's route's price
            
            if($schedule_id->available_seats - $request->ticket_quantity >= 0 ){
                $schedule_id->available_seats = $schedule_id->available_seats - $request->ticket_quantity;//reduce the amount of seats available
                $schedule_id->save();//save
            }else{
                return redirect("/admin/user/{$user->id}/profile/my-booking/create")->withErrors(['Invalid amount'=>'Not enough seat available.']);
            }

            $booking = new Booking;//making a new booking
            $booking->user_id = $user->id;//initializing it with what user entered
            $booking->vessel_id = $schedule_id->vessel_id;
            $booking->schedule_id = $request->schedule_id;
            $booking->ticket_type_id = $request->ticket_type_id;
            $booking->ticket_quantity = $request->ticket_quantity;
            $booking->total = $request->ticket_quantity * $price;
            $booking->save();//save

            DB::commit();//This means nothing went wrong so we can commit/save to the database

            /**
             * For individual user pages we are using user's id to define the cache. If everyone used the same cache then the values will be the same
             * If there is a change then we must forget the cache so that the changes will be seen on these pages.
             */
            Cache::forget('myBookingIndexID'.$user->id);//forget the cache so we see the changes.
            Cache::forget('bookingIndex');

            Cache::forget('myScheduleIndexID'.$user->id);
            Cache::forget('scheduleIndex');


        } catch(Exception $e) {
            DB::rollBack();//Something went wrong so we undo the transaction. It won't be saved to database
            return redirect("/admin/user/{$user->id}/profile/my-booking/create")->withErrors([ $e->getMessage() ])->withInput();//if it is an sql error then it will show the entire sql error. If we want we can put custom message
            
        } catch(Throwable $e) {
            DB::rollBack();//same thing here
            return redirect("/admin/user/{$user->id}/profile/my-booking/create")->withErrors([ $e->getMessage() ])->withInput();
            
        }

        return redirect("/admin/user/{$user->id}/profile/my-booking/")->with('success', 'Booking Created!');//if no errors then redirect back to page with alert
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     * 
     * This method is used to show data. Individual data.
     */
    public function show(User $user, Booking $booking)
    {
        return view('admin.user.profile.my-booking.show', compact('booking'));//sending the variable to the view
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     * 
     * This method is used to edit (my) booking
     */
    public function edit(User $user, Booking $booking)
    {
        $myBookingEdit = Cache::remember('myBookingEdit', 60, function () {//Right now we are doing caching for 60 seconds
            $ticket_types = TicketType::get();//get all ticket types
            $booking_statuses = BookingStatus::get();//get all booking statuses
            $schedules = Schedule::with('route')->get();//get all schedule with route relationship
            return compact('schedules','ticket_types','booking_statuses');//returning it as variables
        });

        $ticket_types = $myBookingEdit["ticket_types"];//accessing the variable and initializing
        $booking_statuses = $myBookingEdit["booking_statuses"];
        $schedules = $myBookingEdit["schedules"];

        return view('admin.user.profile.my-booking.edit', compact('booking','user','ticket_types','booking_statuses','schedules'));//sending the variables to the view
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
    public function update(Request $request, User $user, Booking $booking)
    {
        DB::beginTransaction();//start transaction, This is to have control over rollbacks and commits

        $request->validate([//validating the data that is being passed through. If an error occurs it will redirect back to page
            'schedule_id'=>'required',
            'ticket_type_id'=>'required',
            'ticket_quantity'=>'required',
            'booking_status'=>'required'
        ]);
        
        try {
            
            $schedule_id = Schedule::find($request->schedule_id);//get schedule based on schedule id

            $price = $schedule_id->route->allTicketTypeOfRoute->where('id',$request->ticket_type_id)->first()->pivot->price;//getting that schedule's route's price

            if($schedule_id->available_seats + $booking->ticket_quantity - $request->ticket_quantity >= 0 ){
                $schedule_id->available_seats = $schedule_id->available_seats + $booking->ticket_quantity - $request->ticket_quantity;//Get previous ticket qty and then reduce the amount of seats available. 
                $schedule_id->save();//save
            }else{
                return redirect("/admin/user/{$user->id}/profile/my-booking/{$booking->id}/edit")->withErrors(['Invalid amount'=>'Not enough seat available.']);
            }

            $booking->user_id = $user->id;//initialzing with what user entered
            $booking->vessel_id = $schedule_id->vessel_id;
            $booking->schedule_id = $request->schedule_id;
            $booking->ticket_type_id = $request->ticket_type_id;
            $booking->ticket_quantity = $request->ticket_quantity;
            $booking->total = $request->ticket_quantity * $price;//calculating price
            $booking->booking_status_id = $request->booking_status;
            $booking->save();//save

            DB::commit();//This means nothing went wrong so we can commit/save to the database

            /**
             * For individual user pages we are using user's id to define the cache. If everyone used the same cache then the values will be the same
             * If there is a change then we must forget the cache so that the changes will be seen on these pages.
             */
            Cache::forget('myBookingIndexID'.$user->id);//forget the cache so we see the changes.
            Cache::forget('bookingIndex');

            Cache::forget('myScheduleIndexID'.$user->id);
            Cache::forget('scheduleIndex');

        } catch(Exception $e) {
            DB::rollBack();//Something went wrong so we undo the transaction. It won't be saved to database
            return redirect("/admin/user/{$user->id}/profile/my-booking/{$booking->id}/edit")->withErrors([ $e->getMessage() ])->withInput();//if it is an sql error then it will show the entire sql error. If we want we can put custom message
            
        } catch(Throwable $e) {
            DB::rollBack();//same thing here
            return redirect("/admin/user/{$user->id}/profile/my-booking/{$booking->id}/edit")->withErrors([ $e->getMessage() ])->withInput();
            
        }
        
        return redirect("/admin/user/{$user->id}/profile/my-booking/")->with('Edit-success','Booking Updated!');//if no errors then redirect back to page with alert
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     * 
     * This method is used for deleting (my) booking
     */
    public function destroy(User $user, Booking $booking)
    {
        DB::beginTransaction();//start transaction, This is to have control over rollbacks and commits

        try {
            $schedule_id = Schedule::find($booking->schedule_id);//find schedule
            $schedule_id->available_seats = $schedule_id->available_seats + $booking->ticket_quantity;//if the booking is cancelled/deleted then give back the seat available
            $schedule_id->save();

            $booking->delete();//deleting it

            DB::commit();//This means nothing went wrong so we can commit/save to the database

            /**
             * For individual user pages we are using user's id to define the cache. If everyone used the same cache then the values will be the same
             * If there is a change then we must forget the cache so that the changes will be seen on these pages.
             */
            Cache::forget('myBookingIndexID'.$user->id);//forget the cache so we see the changes.
            Cache::forget('bookingIndex');

            Cache::forget('myScheduleIndexID'.$user->id);
            Cache::forget('scheduleIndex');
            
        } catch(Exception $e) {
            DB::rollBack();//Something went wrong so we undo the transaction. It won't be saved to database
            return redirect("/admin/user/{$user->id}/profile/my-booking/")->withErrors([ $e->getMessage() ])->withInput();//if it is an sql error then it will show the entire sql error. If we want we can put custom message
        } catch(Throwable $e) {
            DB::rollBack();
            return redirect("/admin/user/{$user->id}/profile/my-booking/")->withErrors([ $e->getMessage() ])->withInput();
        }
        
        return redirect("/admin/user/{$user->id}/profile/my-booking/")->with('Delete-success','Booking deleted successfully!');//if no errors then redirect back to page with alert

    }
}
