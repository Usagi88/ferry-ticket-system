<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\VesselController;
use App\Http\Controllers\TicketTypeController;
use App\Http\Controllers\VesselTypeController;
use App\Http\Controllers\IslandController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\RouteController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MyVesselController;
use App\Http\Controllers\MyBookingController;
use App\Http\Controllers\MyScheduleController;
use App\Http\Controllers\MyRouteController;
use App\Http\Controllers\AssignToVesselController;
use App\Http\Controllers\MyAssignToVesselController;
use App\Http\Controllers\AgentIslandController;
use App\Http\Controllers\MyAgentIslandController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\MyAssignedVesselController;
use App\Http\Controllers\MyTicketTypeController;
use App\Http\Controllers\ScheduleFCFastEventController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Merchant Registration
Route::get('/merchant-register', [RegisterController::class, 'showMerchantRegistrationForm'])->name('merchant-register');
Route::post('/merchant-register', [RegisterController::class, 'register']);

//Agent Registration
Route::get('/agent-register', [RegisterController::class, 'showAgentRegistrationForm'])->name('agent-register');
Route::post('/agent-register', [RegisterController::class, 'register']);

//contains login, registration, password reset
Auth::routes();

//verify email
//contains email/verify, email/verify/{id}/{hash}, email/resend
Auth::routes(['verify' => true]);

Route::get('/admin/dashboard', [HomeController::class, 'index'])->name('dashboard');

//Notification
Route::patch('/fcm-token', [HomeController::class, 'updateToken'])->name('fcmToken');
//Route::post('/send-notification',[HomeController::class,'notification'])->name('notification');

//User.
//If we use resource method we can remove all these since it will have same path. But to show routes I have done this.
Route::group(['middleware' => ['role:admin,staff']], function() {
    Route::get('/admin/user/', [UserController::class, 'index'])->name('user.index')->middleware('role:admin,staff');
    Route::get('/admin/user/create', [UserController::class, 'create'])->name('user.create')->middleware('role:admin,staff');
    Route::post('/admin/user/', [UserController::class, 'store'])->name('user.store')->middleware('role:admin,staff');
    Route::get('/admin/user/{user}', [UserController::class, 'show'])->name('user.show')->middleware('role:admin,staff');
    Route::get('/admin/user/{user}/edit', [UserController::class, 'edit'])->name('user.edit')->middleware('role:admin,staff');
    Route::patch('/admin/user/{user}', [UserController::class, 'update'])->name('user.update')->middleware('role:admin,staff');
    Route::post('/admin/user/{user}', [UserController::class, 'destroy'])->name('user.delete')->middleware('role:admin,staff');
});

//Role
Route::group(['middleware' => ['role:admin,staff']], function() {
    Route::get('/admin/role/', [RoleController::class, 'index'])->name('role.index')->middleware('can:isAdmin');
    Route::get('/admin/role/create', [RoleController::class, 'create'])->name('role.create')->middleware('can:isAdmin');
    Route::post('/admin/role/', [RoleController::class, 'store'])->name('role.store')->middleware('can:isAdmin');
    Route::get('/admin/role/{role}', [RoleController::class, 'show'])->name('role.show')->middleware('can:isAdmin');
    Route::get('/admin/role/{role}/edit', [RoleController::class, 'edit'])->name('role.edit')->middleware('can:isAdmin');
    Route::patch('/admin/role/{role}', [RoleController::class, 'update'])->name('role.update')->middleware('can:isAdmin');
    Route::post('/admin/role/{role}', [RoleController::class, 'destroy'])->name('role.delete')->middleware('can:isAdmin');
});

//Assign Vessel
Route::group(['middleware' => ['role:admin,staff']], function() {
    Route::get('/admin/vessel/assign-vessel/', [AssignToVesselController::class, 'index'])->name('assignToVessel.index');
    Route::get('/admin/vessel/assign-vessel/create', [AssignToVesselController::class, 'create'])->name('assignToVessel.create');
    Route::post('/admin/vessel/assign-vessel/', [AssignToVesselController::class, 'store'])->name('assignToVessel.store');
    Route::get('/admin/vessel/assign-vessel/{assign}', [AssignToVesselController::class, 'show'])->name('assignToVessel.show');
    Route::get('/admin/vessel/assign-vessel/{assign}/edit', [AssignToVesselController::class, 'edit'])->name('assignToVessel.edit');
    Route::patch('/admin/vessel/assign-vessel/{assign}', [AssignToVesselController::class, 'update'])->name('assignToVessel.update');
    Route::post('/admin/vessel/assign-vessel/{assign}', [AssignToVesselController::class, 'destroy'])->name('assignToVessel.delete');
});

//Vessel
Route::group(['middleware' => ['role:admin,staff']], function() {
    Route::get('/admin/vessel/', [VesselController::class, 'index'])->name('vessel.index');
    Route::get('/admin/vessel/create', [VesselController::class, 'create'])->name('vessel.create');
    Route::post('/admin/vessel/', [VesselController::class, 'store'])->name('vessel.store');
    Route::get('/admin/vessel/{vessel}', [VesselController::class, 'show'])->name('vessel.show');
    Route::get('/admin/vessel/{vessel}/edit', [VesselController::class, 'edit'])->name('vessel.edit');
    Route::patch('/admin/vessel/{vessel}', [VesselController::class, 'update'])->name('vessel.update');
    Route::post('/admin/vessel/{vessel}', [VesselController::class, 'destroy'])->name('vessel.delete');
});


//Ticket Type
Route::group(['middleware' => ['role:admin,staff']], function() {
    Route::get('/admin/ticket-type/', [TicketTypeController::class, 'index'])->name('ticket-type.index');
    Route::get('/admin/ticket-type/create', [TicketTypeController::class, 'create'])->name('ticket-type.create');
    Route::post('/admin/ticket-type/', [TicketTypeController::class, 'store'])->name('ticket-type.store');
    Route::get('/admin/ticket-type/{ticket_type}', [TicketTypeController::class, 'show'])->name('ticket-type.show');
    Route::get('/admin/ticket-type/{ticket_type}/edit', [TicketTypeController::class, 'edit'])->name('ticket-type.edit');
    Route::patch('/admin/ticket-type/{ticket_type}', [TicketTypeController::class, 'update'])->name('ticket-type.update');
    Route::post('/admin/ticket-type/{ticket_type}', [TicketTypeController::class, 'destroy'])->name('ticket-type.delete');
});

//Vessel Type
Route::group(['middleware' => ['role:admin,staff']], function() {
    Route::get('/admin/vessel-type/', [VesselTypeController::class, 'index'])->name('vessel-type.index');
    Route::get('/admin/vessel-type/create', [VesselTypeController::class, 'create'])->name('vessel-type.create');
    Route::post('/admin/vessel-type/', [VesselTypeController::class, 'store'])->name('vessel-type.store');
    Route::get('/admin/vessel-type/{vessel_type}', [VesselTypeController::class, 'show'])->name('vessel-type.show');
    Route::get('/admin/vessel-type/{vessel_type}/edit', [VesselTypeController::class, 'edit'])->name('vessel-type.edit');
    Route::patch('/admin/vessel-type/{vessel_type}', [VesselTypeController::class, 'update'])->name('vessel-type.update');
    Route::post('/admin/vessel-type/{vessel_type}', [VesselTypeController::class, 'destroy'])->name('vessel-type.delete');
});

//Route
Route::group(['middleware' => ['role:admin,staff']], function() {
    Route::get('/admin/route/', [RouteController::class, 'index'])->name('route.index');
    Route::get('/admin/route/create', [RouteController::class, 'create'])->name('route.create');
    Route::post('/admin/route/', [RouteController::class, 'store'])->name('route.store');
    Route::get('/admin/route/{route}', [RouteController::class, 'show'])->name('route.show');
    Route::get('/admin/route/{route}/edit', [RouteController::class, 'edit'])->name('route.edit');
    Route::patch('/admin/route/{route}', [RouteController::class, 'update'])->name('route.update');
    Route::post('/admin/route/{route}', [RouteController::class, 'destroy'])->name('route.delete');
});

//Agent Island
Route::group(['middleware' => ['role:admin,staff']], function() {
    Route::get('/admin/island/agent-island/', [AgentIslandController::class, 'index'])->name('agentIsland.index');
    Route::get('/admin/island/agent-island/create', [AgentIslandController::class, 'create'])->name('agentIsland.create');
    Route::post('/admin/island/agent-island/', [AgentIslandController::class, 'store'])->name('agentIsland.store');
    Route::get('/admin/island/agent-island/{agent}', [AgentIslandController::class, 'show'])->name('agentIsland.show');
    Route::get('/admin/island/agent-island/{agent}/edit', [AgentIslandController::class, 'edit'])->name('agentIsland.edit');
    Route::patch('/admin/island/agent-island/{agent}', [AgentIslandController::class, 'update'])->name('agentIsland.update');
    Route::post('/admin/island/agent-island/{agent}', [AgentIslandController::class, 'destroy'])->name('agentIsland.delete');
});

//Island
Route::group(['middleware' => ['role:admin,staff']], function() {
    Route::get('/admin/island/', [IslandController::class, 'index'])->name('island.index');
    Route::get('/admin/island/create', [IslandController::class, 'create'])->name('island.create');
    Route::post('/admin/island/', [IslandController::class, 'store'])->name('island.store');
    Route::get('/admin/island/{island}', [IslandController::class, 'show'])->name('island.show');
    Route::get('/admin/island/{island}/edit', [IslandController::class, 'edit'])->name('island.edit');
    Route::patch('/admin/island/{island}', [IslandController::class, 'update'])->name('island.update');
    Route::post('/admin/island/{island}', [IslandController::class, 'destroy'])->name('island.delete');
});

//Schedule
Route::group(['middleware' => ['role:admin,staff']], function() {
    Route::get('/admin/schedule/', [ScheduleController::class, 'index'])->name('schedule.index');
    Route::get('/admin/schedule/create', [ScheduleController::class, 'create'])->name('schedule.create');
    Route::post('/admin/schedule/', [ScheduleController::class, 'store'])->name('schedule.store');
    Route::get('/admin/schedule/{schedule}', [ScheduleController::class, 'show'])->name('schedule.show');
    Route::get('/admin/schedule/{schedule}/edit', [ScheduleController::class, 'edit'])->name('schedule.edit');
    Route::patch('/admin/schedule/{schedule}', [ScheduleController::class, 'update'])->name('schedule.update');
    Route::post('/admin/schedule/{schedule}', [ScheduleController::class, 'destroy'])->name('schedule.delete');
    //Schedule Full Calendar
    Route::get('/scheduleFC-load-event/{user}', [ScheduleController::class, 'scheduleFCLoadEvent'])->name('schedule.scheduleFCLoadEvent');
    Route::put('/scheduleFC-update-event', [ScheduleController::class, 'scheduleFCUpdateEvent'])->name('schedule.scheduleFCUpdateEvent');
    Route::post('/scheduleFC-store-event', [ScheduleController::class, 'scheduleFCStoreEvent'])->name('schedule.scheduleFCStoreEvent');
    Route::post('/scheduleFC-delete-event', [ScheduleController::class, 'scheduleFCDeleteEvent'])->name('schedule.scheduleFCDeleteEvent');
    
    //Schedule Full Calendar Fast Event
    Route::post('/scheduleFCFastEvent-delete-event', [ScheduleFCFastEventController::class, 'destroy'])->name('schedule.scheduleFCFastEventDeleteEvent');
});

//Booking
Route::group(['middleware' => ['role:admin,staff']], function() {
    Route::get('/admin/booking/', [BookingController::class, 'index'])->name('booking.index');
    Route::get('/admin/booking/create', [BookingController::class, 'create'])->name('booking.create');
    Route::post('/admin/booking/', [BookingController::class, 'store'])->name('booking.store');
    Route::get('/admin/booking/{booking}', [BookingController::class, 'show'])->name('booking.show');
    Route::get('/admin/booking/{booking}/edit', [BookingController::class, 'edit'])->name('booking.edit');
    Route::patch('/admin/booking/{booking}', [BookingController::class, 'update'])->name('booking.update');
    Route::post('/admin/booking/{booking}', [BookingController::class, 'destroy'])->name('booking.delete');
});


//Profile
Route::group(['middleware' => ['role:admin,staff']], function() {
    Route::get('/admin/user/{user}/profile/', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/admin/user/{user}/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/admin/user/{user}/profile', [ProfileController::class, 'update'])->name('profile.update');
});

//My Vessel
Route::group(['middleware' => ['role:admin,staff']], function() {
    Route::get('/admin/user/{user}/profile/my-vessel/', [MyVesselController::class, 'index'])->name('myVessel.index');
    Route::get('/admin/user/{user}/profile/my-vessel/create', [MyVesselController::class, 'create'])->name('myVessel.create');
    Route::post('/admin/user/{user}/profile/my-vessel/', [MyVesselController::class, 'store'])->name('myVessel.store');
    Route::get('/admin/user/{user}/profile/my-vessel/{vessel}', [MyVesselController::class, 'show'])->name('myVessel.show');
    Route::get('/admin/user/{user}/profile/my-vessel/{vessel}/edit', [MyVesselController::class, 'edit'])->name('myVessel.edit');
    Route::patch('/admin/user/{user}/profile/my-vessel/{vessel}', [MyVesselController::class, 'update'])->name('myVessel.update');
    Route::post('/admin/user/{user}/profile/my-vessel/{vessel}', [MyVesselController::class, 'destroy'])->name('myVessel.delete');
});

//My Booking
Route::group(['middleware' => ['role:admin,staff']], function() {
    Route::get('/admin/user/{user}/profile/my-booking/', [MyBookingController::class, 'index'])->name('myBooking.index');
    Route::get('/admin/user/{user}/profile/my-booking/create', [MyBookingController::class, 'create'])->name('myBooking.create');
    Route::post('/admin/user/{user}/profile/my-booking/', [MyBookingController::class, 'store'])->name('myBooking.store');
    Route::get('/admin/user/{user}/profile/my-booking/{booking}', [MyBookingController::class, 'show'])->name('myBooking.show');
    Route::get('/admin/user/{user}/profile/my-booking/{booking}/edit', [MyBookingController::class, 'edit'])->name('myBooking.edit');
    Route::patch('/admin/user/{user}/profile/my-booking/{booking}', [MyBookingController::class, 'update'])->name('myBooking.update');
    Route::post('/admin/user/{user}/profile/my-booking/{booking}', [MyBookingController::class, 'destroy'])->name('myBooking.delete');
});

//My Schedule
Route::group(['middleware' => ['role:admin,staff']], function() {
    Route::get('/admin/user/{user}/profile/my-schedule/', [MyScheduleController::class, 'index'])->name('mySchedule.index');
    Route::get('/admin/user/{user}/profile/my-schedule/create', [MyScheduleController::class, 'create'])->name('mySchedule.create');
    Route::post('/admin/user/{user}/profile/my-schedule/', [MyScheduleController::class, 'store'])->name('mySchedule.store');
    Route::get('/admin/user/{user}/profile/my-schedule/{schedule}', [MyScheduleController::class, 'show'])->name('mySchedule.show');
    Route::get('/admin/user/{user}/profile/my-schedule/{schedule}/edit', [MyScheduleController::class, 'edit'])->name('mySchedule.edit');
    Route::patch('/admin/user/{user}/profile/my-schedule/{schedule}', [MyScheduleController::class, 'update'])->name('mySchedule.update');
    Route::post('/admin/user/{user}/profile/my-schedule/{schedule}', [MyScheduleController::class, 'destroy'])->name('mySchedule.delete');
});

//My Route
Route::group(['middleware' => ['role:admin,staff']], function() {
    Route::get('/admin/user/{user}/profile/my-route/', [MyRouteController::class, 'index'])->name('myRoute.index');
    Route::get('/admin/user/{user}/profile/my-route/create', [MyRouteController::class, 'create'])->name('myRoute.create');
    Route::post('/admin/user/{user}/profile/my-route/', [MyRouteController::class, 'store'])->name('myRoute.store');
    Route::get('/admin/user/{user}/profile/my-route/{route}', [MyRouteController::class, 'show'])->name('myRoute.show');
    Route::get('/admin/user/{user}/profile/my-route/{route}/edit', [MyRouteController::class, 'edit'])->name('myRoute.edit');
    Route::patch('/admin/user/{user}/profile/my-route/{route}', [MyRouteController::class, 'update'])->name('myRoute.update');
    Route::post('/admin/user/{user}/profile/my-route/{route}', [MyRouteController::class, 'destroy'])->name('myRoute.delete');
});

//My Assign To Vessel
Route::group(['middleware' => ['role:admin,staff']], function() {
    Route::get('/admin/user/{user}/profile/vessel-assigned-to/', [MyAssignToVesselController::class, 'index'])->name('myAssignToVessel.index');
    Route::get('/admin/user/{user}/profile/vessel-assigned-to/create', [MyAssignToVesselController::class, 'create'])->name('myAssignToVessel.create');
    Route::post('/admin/user/{user}/profile/vessel-assigned-to/', [MyAssignToVesselController::class, 'store'])->name('myAssignToVessel.store');
    Route::get('/admin/user/{user}/profile/vessel-assigned-to/{assign}', [MyAssignToVesselController::class, 'show'])->name('myAssignToVessel.show');
    Route::get('/admin/user/{user}/profile/vessel-assigned-to/{assign}/edit', [MyAssignToVesselController::class, 'edit'])->name('myAssignToVessel.edit');
    Route::patch('/admin/user/{user}/profile/vessel-assigned-to/{assign}', [MyAssignToVesselController::class, 'update'])->name('myAssignToVessel.update');
    Route::post('/admin/user/{user}/profile/vessel-assigned-to/{assign}', [MyAssignToVesselController::class, 'destroy'])->name('myAssignToVessel.delete');
});

//My Assigned Vessel
Route::group(['middleware' => ['role:admin,staff']], function() {
    Route::get('/admin/user/{user}/profile/my-assigned-vessel/', [MyAssignedVesselController::class, 'index'])->name('myAssignedVessel.index');
    Route::get('/admin/user/{user}/profile/my-assigned-vessel/create', [MyAssignedVesselController::class, 'create'])->name('myAssignedVessel.create');
    Route::post('/admin/user/{user}/profile/my-assigned-vessel/', [MyAssignedVesselController::class, 'store'])->name('myAssignedVessel.store');
    Route::get('/admin/user/{user}/profile/my-assigned-vessel/{assign}', [MyAssignedVesselController::class, 'show'])->name('myAssignedVessel.show');
    Route::get('/admin/user/{user}/profile/my-assigned-vessel/{assign}/edit', [MyAssignedVesselController::class, 'edit'])->name('myAssignedVessel.edit');
    Route::patch('/admin/user/{user}/profile/my-assigned-vessel/{assign}', [MyAssignedVesselController::class, 'update'])->name('myAssignedVessel.update');
    Route::post('/admin/user/{user}/profile/my-assigned-vessel/{assign}', [MyAssignedVesselController::class, 'destroy'])->name('myAssignedVessel.delete');
});

//My Agent Island
Route::group(['middleware' => ['role:admin,staff']], function() {
    Route::get('/admin/user/{user}/profile/my-agent-island/', [MyAgentIslandController::class, 'index'])->name('myAgentIsland.index');
    Route::get('/admin/user/{user}/profile/my-agent-island/create', [MyAgentIslandController::class, 'create'])->name('myAgentIsland.create');
    Route::post('/admin/user/{user}/profile/my-agent-island/', [MyAgentIslandController::class, 'store'])->name('myAgentIsland.store');
    Route::get('/admin/user/{user}/profile/my-agent-island/{assign}', [MyAgentIslandController::class, 'show'])->name('myAgentIsland.show');
    Route::get('/admin/user/{user}/profile/my-agent-island/{assign}/edit', [MyAgentIslandController::class, 'edit'])->name('myAgentIsland.edit');
    Route::patch('/admin/user/{user}/profile/my-agent-island/{assign}', [MyAgentIslandController::class, 'update'])->name('myAgentIsland.update');
    Route::post('/admin/user/{user}/profile/my-agent-island/{assign}', [MyAgentIslandController::class, 'destroy'])->name('myAgentIsland.delete');
});

//Notification controller. Just to have a place to store the methods instead of making it in web.php
Route::get('/notification/read/', [NotificationController::class, 'readNotification'])->name('notification.read');
Route::get('/notification/markAllRead/', [NotificationController::class, 'MarkAllNotification'])->name('notification.markAllRead');


//My Ticket Type
Route::group(['middleware' => ['role:admin,staff']], function() {
    Route::get('/admin/user/{user}/profile/my-ticket-type/', [MyTicketTypeController::class, 'index'])->name('myTicketType.index');
    Route::get('/admin/user/{user}/profile/my-ticket-type/create', [MyTicketTypeController::class, 'create'])->name('myTicketType.create');
    Route::post('/admin/user/{user}/profile/my-ticket-type/', [MyTicketTypeController::class, 'store'])->name('myTicketType.store');
    Route::get('/admin/user/{user}/profile/my-ticket-type/{ticket_type}', [MyTicketTypeController::class, 'show'])->name('myTicketType.show');
    Route::get('/admin/user/{user}/profile/my-ticket-type/{ticket_type}/edit', [MyTicketTypeController::class, 'edit'])->name('myTicketType.edit');
    Route::patch('/admin/user/{user}/profile/my-ticket-type/{ticket_type}', [MyTicketTypeController::class, 'update'])->name('myTicketType.update');
    Route::post('/admin/user/{user}/profile/my-ticket-type/{ticket_type}', [MyTicketTypeController::class, 'destroy'])->name('myTicketType.delete');
});