<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    
    public function readNotification(Request $request)
    {
        if($request->ajax()){//if ajax request
            $notfication = auth()->user()->unreadNotifications->where('id',$request->notifID);//get notification
            $notfication->markAsRead();//mark it as read
            return response()->json([//just a way to show that it worked or not
                'success'=>true
            ]);
        }
        return response()->json([
            'success'=>false
        ]);
    }

    public function MarkAllNotification()
    {
        auth()->user()->unreadNotifications->markAsRead();//mark all as read
        return response()->json([//just a way to show that it worked or not
            'success'=>true
        ]);
        
        return response()->json([
            'success'=>false
        ]);
        }
}
