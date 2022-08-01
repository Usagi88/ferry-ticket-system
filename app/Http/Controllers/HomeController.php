<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FcmToken;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Exception;
use Throwable;
use App\Models\User;
use App\Models\Vessel;
use App\Models\Route;
use App\Models\Schedule;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth','verified']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $dashboardIndex = Cache::remember('dashboardIndex', 60, function(){//Right now we are doing caching for 60 seconds
            //$profile =  Profile::where('user_id',$user->id)->with('user')->get();//get user's profile with user relationship
            
            //line chart data
            $userCount = User::select('id')->get()->count();//getting count
            $vesselCount = Vessel::select('id')->get()->count();
            $routeCount = Route::select('id')->get()->count();
            $scheduleCount = Schedule::select('id')->get()->count();
            $bookingCount = Booking::select('id')->get()->count();

            //pie chart 1 data
            $admin = User::whereHas('roles', function($q){//getting all users with role 
                $q->where('name', 'admin');
            })->get()->count();

            $staff = User::whereHas('roles', function($q){
                $q->where('name', 'staff');
            })->get()->count();

            $merchant = User::whereHas('roles', function($q){
                $q->where('name', 'merchant');
            })->get()->count();

            $agent = User::whereHas('roles', function($q){
                $q->where('name', 'agent');
            })->get()->count();

            //In case we need user data. Not using it at the moment
            $user = User::where('id', Auth::id())->first();
            
            //pie chart 2 data
            $ferry = Vessel::whereHas('vessel_type', function($q){//getting all users with role 
                $q->where('name', 'Ferry');
            })->get()->count();
            $speedBoat = Vessel::whereHas('vessel_type', function($q){//getting all users with role 
                $q->where('name', 'Speed boat');
            })->get()->count();

            return compact('userCount','vesselCount','routeCount','scheduleCount','bookingCount','admin','staff','merchant','agent', 'user','ferry','speedBoat');
        });
        //line chart data
        $userCount = $dashboardIndex['userCount'];
        $vesselCount = $dashboardIndex['vesselCount'];
        $routeCount = $dashboardIndex['routeCount'];
        $scheduleCount = $dashboardIndex['scheduleCount'];
        $bookingCount = $dashboardIndex['bookingCount'];

        //user data (not using)
        $user = $dashboardIndex['user'];

        //pie chart data 1
        $admin = $dashboardIndex['admin'];
        $staff = $dashboardIndex['staff'];
        $merchant = $dashboardIndex['merchant'];
        $agent = $dashboardIndex['agent'];

        //pie chart data 2
        $ferry = $dashboardIndex['ferry'];
        $speedBoat = $dashboardIndex['speedBoat'];
        
        return view('admin.dashboard', compact('userCount','vesselCount','routeCount','scheduleCount','bookingCount','admin','staff','merchant','agent', 'user','ferry','speedBoat'));
    }
    //Later use task scheduler in linux to delete token every 5min. Cronjob not available in windows
    public function updateToken(Request $request){
        
        DB::beginTransaction();//start transaction, This is to have control over rollbacks and commits
        
        try{
            //$request->user()->fcmTokens->attach($request->token);
            Cache::remember('myFCMtoken'.$request->user()->id, 300, function() use($request){//Right now we are doing caching for (5min)300 seconds
                $exists = false;
                foreach ($request->user()->fcmTokens as $fcmToken) {//get users tokens
                    
                    if($fcmToken->fcm_token == $request->token){//if it exists set boolean to true
                        $exists = true;
                    }
                }
                if($exists){//if exists then return json saying it exists
                    DB::commit();//This means nothing went wrong so we can commit/save to the database//commit/save to the database
                    return response()->json([
                        'Token Exists'=>true
                    ]);
                }else{//if it doesn't exist then create a token and add to table
                    $fcmtoken = new FcmToken();
                    $fcmtoken->fcm_token = $request->token;
                    $fcmtoken->save();
                    $request->user()->fcmTokens()->attach($fcmtoken->id);//then we attach it to pivot table
                    DB::commit();//This means nothing went wrong so we can commit/save to the database//commit/save to the database
                    return response()->json([
                        'success'=>true
                    ]);
                }
            });
        }catch(\Exception $e){
            DB::rollBack();//Something went wrong so we undo the transaction. It won't be saved to database
            //report($e);
            return response()->json([
                'success'=>false
            ],500);
        }catch(Throwable $e) {
            DB::rollBack();
            //report($e);
            return response()->json([
                'success'=>false
            ],500);
        }
    }
}
