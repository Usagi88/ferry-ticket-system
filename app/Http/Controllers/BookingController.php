<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\TicketType;
use App\Models\BookingStatus;
use App\Models\Schedule;
use App\Models\Route;
use App\Models\User;
use App\Models\Vessel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
use Throwable;
use Illuminate\Support\Facades\Cache;


class BookingController extends Controller
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
        $bookings = Cache::remember('bookingIndex', 60, function(){//Right now we are doing caching for 60 seconds
            //get all bookings with vessel,schedule,ticket_type,booking_status,schedule's route relationship
            return Booking::with('vessel','schedule','ticket_type','booking_status','schedule.route.allTicketTypeOfRoute')->get();
        });
        return view('admin.booking.index', compact('bookings'));//sending the variable to view
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     * 
     * This method is used to create a booking
     */
    public function create()
    {
        $bookingCreate = Cache::remember('bookingCreate', 60, function () {//Right now we are doing caching for 60 seconds
            $users = User::get();//get all users
            $ticket_types = TicketType::get();//get all ticket types
            $schedules = Schedule::with('route')->get();//get all schedules with route
            return compact('users','schedules','ticket_types');//returning it as variable
            
        });

        $users = $bookingCreate["users"];//accessing the variable and initializing 
        $ticket_types = $bookingCreate["ticket_types"];
        $schedules = $bookingCreate["schedules"];
        return view('admin.booking.create',compact('users','schedules','ticket_types'));//sending the variables to the view
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
            'schedule_id'=>'required',
            'ticket_type_id'=>'required',
            'ticket_quantity'=>'required'
        ]);

        try {
            $schedule_id = Schedule::where('id',$request->schedule_id)->with('route.allTicketTypeOfRoute')->first();
            
            //getting that schedule's route's price
            $price = $schedule_id->route->allTicketTypeOfRoute->where('id',$request->ticket_type_id)->first()->pivot->price;
            
            if($schedule_id->available_seats - $request->ticket_quantity >= 0 ){
                $schedule_id->available_seats = $schedule_id->available_seats - $request->ticket_quantity;//reduce the amount of seats available
                $schedule_id->save();//save
            }else{
                return redirect('/admin/booking/create')->withErrors(['Invalid amount'=>'Not enough seat available.']);
            }
            

            $booking = new Booking;//making a new booking
            $booking->user_id = $request->user_id;//initializing it with what user entered
            $booking->vessel_id = $schedule_id->vessel_id;
            $booking->schedule_id = $request->schedule_id;
            $booking->ticket_type_id = $request->ticket_type_id;
            $booking->ticket_quantity = $request->ticket_quantity;
            $booking->total = $request->ticket_quantity * $price;//calculating price
            $booking->save();//save
            
            
            DB::commit();//This means nothing went wrong so we can commit/save to the database

            Cache::forget('bookingIndex');//forget the cache so we see the changes.

            Cache::forget('scheduleIndex');//forget the cache so we see the changes.

        } catch(Exception $e) {
            DB::rollBack();//Something went wrong so we undo the transaction. It won't be saved to database
            return redirect('/admin/booking/create')->withErrors([ $e->getMessage() ])->withInput();//if it is an sql error then it will show the entire sql error. If we want we can put custom message
        } catch(Throwable $e) {
            DB::rollBack();//same thing here
            return redirect('/admin/booking/create')->withErrors([ $e->getMessage() ])->withInput();
        }

        return redirect('/admin/booking/')->with('success', 'Booking Created!');//if no errors then redirect back to page with alert
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Booking  $booking
     * @return \Illuminate\Http\Response
     * 
     * This method is used to show data. Individual data.
     */
    public function show(Booking $booking)
    {
        return view('admin.booking.show',compact('booking'));//sending the variable to the view
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Booking  $booking
     * @return \Illuminate\Http\Response
     * 
     * This method is used to edit a booking
     */
    public function edit(Booking $booking)
    {
        $bookingEdit = Cache::remember('bookingEdit', 60, function () {//Right now we are doing caching for 60 seconds
            $users = User::get();//get all users
            $ticket_types = TicketType::get();//get all ticket types
            $booking_statuses = BookingStatus::get();//get all booking statuses
            $schedules = Schedule::with('route')->get();//get all schedules with route relationship
            return compact('users','schedules','ticket_types','booking_statuses');//returning it as variable
        });

        $users = $bookingEdit["users"];//accessing the variable and initializing 
        $ticket_types = $bookingEdit["ticket_types"];
        $booking_statuses = $bookingEdit["booking_statuses"];
        $schedules = $bookingEdit["schedules"];

        return view('admin.booking.edit',compact('booking','users','schedules','ticket_types','booking_statuses'));//sending the variables to the view
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Booking  $booking
     * @return \Illuminate\Http\Response
     * 
     * This method is what happens when we submit in edit page
     */
    public function update(Request $request, Booking $booking)
    {
        DB::beginTransaction();//start transaction, This is to have control over rollbacks and commits

        $request->validate([//validating the data that is being passed through. If an error occurs it will redirect back to page
            'user_id'=>'required',
            'schedule_id'=>'required',
            'ticket_type_id'=>'required',
            'ticket_quantity'=>'required',
            'booking_status'=>'required'
        ]);

        try {
            $schedule_id = Schedule::find($request->schedule_id);//find schedule

            $price = $schedule_id->route->allTicketTypeOfRoute->where('id',$request->ticket_type_id)->first()->pivot->price;//getting that schedule's route's price
            
            if($schedule_id->available_seats + $booking->ticket_quantity - $request->ticket_quantity >= 0 ){
                $schedule_id->available_seats = $schedule_id->available_seats + $booking->ticket_quantity - $request->ticket_quantity;//Get previous ticket qty and then reduce the amount of seats available. 
                $schedule_id->save();//save
            }else{
                return redirect("/admin/booking/{$booking->id}/edit")->withErrors(['Invalid amount'=>'Not enough seat available.']);
            }

            $booking->user_id = $request->user_id;//initialzing with what user entered
            $booking->vessel_id = $schedule_id->vessel_id;
            $booking->ticket_type_id = $request->ticket_type_id;
            $booking->ticket_quantity = $request->ticket_quantity;
            $booking->total = $request->ticket_quantity * $price;//calculating price
            $booking->booking_status_id = $request->booking_status;
            $booking->save();//save

            DB::commit();//This means nothing went wrong so we can commit/save to the database

            Cache::forget('bookingIndex');//forget the cache so we see the changes.

            Cache::forget('scheduleIndex');//forget the cache so we see the changes.

        } catch(Exception $e) {
            DB::rollBack();//Something went wrong so we undo the transaction. It won't be saved to database
            return redirect("/admin/booking/{$booking->id}/edit")->withErrors([ $e->getMessage() ])->withInput();//if it is an sql error then it will show the entire sql error. If we want we can put custom message
        } catch(Throwable $e) {
            DB::rollBack();//same thing here
            return redirect("/admin/booking/{$booking->id}/edit")->withErrors([ $e->getMessage() ])->withInput();
        }

        return redirect('/admin/booking/')->with('Edit-success','Booking Updated!');//if no errors then redirect back to page with alert
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Booking  $booking
     * @return \Illuminate\Http\Response
     * 
     * This method is used for deleting a booking
     */
    public function destroy(Booking $booking)
    {
        DB::beginTransaction();//start transaction, This is to have control over rollbacks and commits
        try {
            $schedule_id = Schedule::find($booking->schedule_id);//find schedule
            $schedule_id->available_seats = $schedule_id->available_seats + $booking->ticket_quantity;//if the booking is cancelled/deleted then give back the seat available
            $schedule_id->save();

            $booking->delete();//deleting it

            DB::commit();//This means nothing went wrong so we can commit/save to the database

            Cache::forget('bookingIndex');//forget the cache so we see the changes.

            Cache::forget('scheduleIndex');//forget the cache so we see the changes.

        } catch(Exception $e) {
            DB::rollBack();//Something went wrong so we undo the transaction. It won't be saved to database
            return redirect("/admin/booking/")->withErrors([ $e->getMessage() ])->withInput();//if it is an sql error then it will show the entire sql error. If we want we can put custom message
        } catch(Throwable $e) {
            DB::rollBack();//same thing here
            return redirect("/admin/booking/")->withErrors([ $e->getMessage() ])->withInput();
        }
        
        return redirect('/admin/booking/')->with('Delete-success','Booking deleted successfully!');//if no errors then redirect back to page with alert
    }
}
