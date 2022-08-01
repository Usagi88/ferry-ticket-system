<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
use Throwable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
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
        /*$profiles = Cache::remember('profileIndexID'.$user->id, 60, function() use($user){//Right now we are doing caching for 60 seconds
            return Profile::where('user_id',$user->id)->with('user')->get();//get user's profile with user relationship
        });*/
        return view('admin.user.profile.index', compact('user'));//sending the variables to the view
    }

    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Profile  $profile
     * @return \Illuminate\Http\Response
     * 
     * This method is used to edit profile
     */
    public function edit(User $user)
    {
        return view('admin.user.profile.edit', compact('user'));//sending the variable to the view
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Profile  $profile
     * @return \Illuminate\Http\Response
     * 
     * This method is what happens when we submit in edit page
     */
    public function update(Request $request, User $user)
    {
        DB::beginTransaction();//start transaction, This is to have control over rollbacks and commits
        
        $request->validate([//validating the data that is being passed through. If an error occurs it will redirect back to page
            'title'=>'max:255',
            'description'=>'max:255'
        ]);
        
        try {
            $user->profile->user_id = $user->id;
            $user->profile->title = $request->title;//initialzing with what user entered
            $user->profile->description = $request->description;
            $user->profile->save();//save

            DB::commit();//This means nothing went wrong so we can commit/save to the database
            /**
             * For individual user pages we are using user's id to define the cache. If everyone used the same cache then the values will be the same
             * If there is a change then we must forget the cache so that the changes will be seen on these pages.
             */
            Cache::forget('profileIndexID'.$user->id);//forget the cache so we see the changes.

        } catch(Exception $e) {
            DB::rollBack();//Something went wrong so we undo the transaction. It won't be saved to database
            return redirect("/admin/user/{$user->id}/profile/{$user->id}/edit")->withErrors([ $e->getMessage() ])->withInput();//if it is an sql error then it will show the entire sql error. If we want we can put custom message
        } catch(Throwable $e) {
            DB::rollBack();//same thing here
            return redirect("/admin/user/{$user->id}/profile/{$user->id}/edit")->withErrors([ $e->getMessage() ])->withInput();
        }

        return redirect("/admin/user/{$user->id}/profile")->with('Edit-success','Profile Updated!');//if no errors then redirect back to page with alert
        
    }

}
