<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Exception;
use Throwable;
use Illuminate\Support\Facades\Cache;


class UserController extends Controller
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
        $userIndex = Cache::remember('userIndex', 60, function(){//Right now we are doing caching for 60 seconds
            $users = User::with('roles','permissions')->orderBy('id','desc')->get();

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

            return compact('users','admin','staff','merchant','agent');
        });
        $users = $userIndex['users'];
        $admin = $userIndex['admin'];
        $staff = $userIndex['staff'];
        $merchant = $userIndex['merchant'];
        $agent = $userIndex['agent'];
        return view('admin.user.index', compact('users','admin','staff','merchant','agent'));//sending the variable to view
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     * 
     * This method is used to create user
     */
    public function create(Request $request)
    {
        if($request->ajax()){//if it is a ajax request
            $roles = Role::where('id',$request->role_id)->first();//find role
            $permissions = $roles->permissions;//get it's permissions
            return $permissions;//return them
        }
        $roles = Cache::remember('userCreate', 60, function(){//Right now we are doing caching for 60 seconds
            return Role::get();//get all roles
        });
        return view('admin.user.create', compact('roles'));//sending the variable to view
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
            'username'=>'required|unique:users,username|max:255',
            'first_name'=>'required|max:255',
            'last_name'=>'required|max:255',
            'email'=>'required|unique:users,email|max:255',
            'password'=>'required|between:4,255|confirmed',//for now we are doing minimum 4 length pass
            'password_confirmation'=>'required'
        ]);

        try {
            $user = new User;//making a new user
            $user->first_name = $request->first_name;//initializing with what user entered
            $user->last_name = $request->last_name;
            $user->username = $request->username;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->save();//save
            //Previously made a profile when user created from model. But it was intefering with validation. Username already taken. Because it was making before validation.
            Profile::create(['user_id' => $user->id, 'title' => $user->username, 'description' => 'No description']);

            if($request->role != null){//if role is selected in create/if role isn't null
                $user->roles()->attach($request->role);//then attach role using the roles relationship. It will save to role users table
                $user->save();//save
            }

            if($request->permissions != null){//if permision is selected in create/if permission isn't null
                $user->permissions()->attach($request->permissions);//then attach permissions using the permissions relationship. It will save to permission users table
                $user->save();//save
            }

            DB::commit();//This means nothing went wrong so we can commit/save to the database//commit/save to the database

            /**
             * We are forgetting all these caches because they are getting user for their pages.
             * If there is a change then we must forget the cache so that the new user will be seen on these pages.
             */
            Cache::forget('userIndex');//when there is a change remove the cache.

            Cache::forget('bookingCreate');
            Cache::forget('bookingEdit');

            Cache::forget('routeCreate');
            Cache::forget('routeEdit');

            Cache::forget('scheduleCreate');
            Cache::forget('scheduleEdit');

            Cache::forget('vesselCreate');
            Cache::forget('vesselEdit');

            Cache::forget('agentIslandCreate');
            Cache::forget('agentIslandEdit');

            Cache::forget('assignToVesselCreate');
            Cache::forget('assignToVesselEdit');

            Cache::forget('myAgentIslandCreate');
            Cache::forget('myAgentIslandEdit');

            Cache::forget('myAssignToVesselCreate');
            Cache::forget('myAssignToVesselEdit');

            Cache::forget('myAssignedVesselCreate');
            Cache::forget('myAssignedVesselEdit');

        } catch(Exception $e) {
            DB::rollBack();//Something went wrong so we undo the transaction. It won't be saved to database
            return redirect('/admin/user/create')->withErrors([ $e->getMessage() ])->withInput();//if it is an sql error then it will show the entire sql error. If we want we can put custom message
        } catch(Throwable $e) {
            DB::rollBack();//same thing here
            return redirect('/admin/user/create')->withErrors([ $e->getMessage() ])->withInput();
        }

        return redirect('/admin/user/')->with('success', 'User Created!');//if no errors then redirect back to page with alert
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * 
     * This method is used to show data. Individual data.
     */
    public function show(User $user)
    {
        return view('admin.user.show', compact('user'));//sending the variable to the view
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * 
     * This method is used to edit user
     */
    public function edit(User $user)
    {
        $roles = Cache::remember('userEdit', 60, function(){//Right now we are doing caching for 60 seconds
            return Role::get();//get all users
        });
        $userRole = $user->roles->first();//get all user's role
        if($userRole != null){//if it's not null
            $rolePermissions = $userRole->permissions;//get all permission role has
        }else{
            $rolePermissions = null;
        }
        $userPermissions = $user->permissions;//get all user's permission
        return view('admin.user.edit', compact('user','roles','userRole','rolePermissions','userPermissions'));//sending the variables to the view
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * 
     * This method is what happens when we submit in edit page
     */
    public function update(Request $request, User $user)
    {
        DB::beginTransaction();//start transaction, This is to have control over rollbacks and commits
        
        $request->validate([//validating the data that is being passed through. If an error occurs it will redirect back to page
            'username'=>'required|max:255|unique:users,username,'.$user->id,
            'first_name'=>'required|max:255',
            'last_name'=>'required|max:255',
            'email'=>'required|max:255|unique:users,email,'.$user->id,
            'password'=>'nullable|between:4,255|confirmed'//for now we are doing minimum 4 length pass
        ]);

        try {
            $user->first_name = $request->first_name;//initialzing with what user entered
            $user->last_name = $request->last_name;
            $user->username = $request->username;
            $user->email = $request->email;
            if($request->password != null){ //if password is not entered then don't hash the password
                $user->password = Hash::make($request->password);
            }
            $user->save();//save

            /**
             * We are detaching and deleting it because we are initializing it again with the changes
             */
            $user->roles()->detach();
            $user->permissions()->detach();

            if($request->role != null){//if role is not null
                $user->roles()->attach($request->role);//then attach role using the roles relationship. It will save to role user table
                $user->save();//save
            }

            if($request->permissions != null){//if permission is not null            
                $user->permissions()->attach($request->permissions);//then attach permission using the permission relationship. It will save to permission user table
                $user->save();
            }

            DB::commit();//This means nothing went wrong so we can commit/save to the database

            /**
             * We are forgetting all these caches because they are getting user for their pages.
             * If there is a change then we must forget the cache so that the new user will be seen on these pages.
             */
            Cache::forget('userIndex');//when there is a change remove the cache.
            
            Cache::forget('bookingCreate');
            Cache::forget('bookingEdit');

            Cache::forget('routeCreate');
            Cache::forget('routeEdit');

            Cache::forget('scheduleCreate');
            Cache::forget('scheduleEdit');
            
            Cache::forget('vesselCreate');
            Cache::forget('vesselEdit');

            Cache::forget('agentIslandCreate');
            Cache::forget('agentIslandEdit');

            Cache::forget('assignToVesselCreate');
            Cache::forget('assignToVesselEdit');

            Cache::forget('myAgentIslandCreate');
            Cache::forget('myAgentIslandEdit');

            Cache::forget('myAssignToVesselCreate');
            Cache::forget('myAssignToVesselEdit');

            Cache::forget('myAssignedVesselCreate');
            Cache::forget('myAssignedVesselEdit');

        } catch(Exception $e) {
            DB::rollBack();//Something went wrong so we undo the transaction. It won't be saved to database
            return redirect("/admin/user/{$user->id}/edit")->withErrors([ $e->getMessage() ])->withInput();//if it is an sql error then it will show the entire sql error. If we want we can put custom message
        } catch(Throwable $e) {
            DB::rollBack();
            return redirect("/admin/user/{$user->id}/edit")->withErrors([ $e->getMessage() ])->withInput();
        }
        
        return redirect('/admin/user/')->with('Edit-success','User Updated!');//if no errors then redirect back to page with alert
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * 
     * This method is used for deleting user
     */
    public function destroy(User $user)
    {
        DB::beginTransaction();//start transaction, This is to have control over rollbacks and commits
        
        try {
            $user->roles()->detach();//delete relation in pivot table. role user table
            $user->permissions()->detach();//delete relation in pivot table. permission user table
            $user->delete();//delete user

            DB::commit();//This means nothing went wrong so we can commit/save to the database

            /**
             * We are forgetting all these caches because they are getting user for their pages.
             * If there is a change then we must forget the cache so that the new user will be seen on these pages.
             */
            Cache::forget('userIndex');//when there is a change remove the cache.

            Cache::forget('bookingCreate');
            Cache::forget('bookingEdit');

            Cache::forget('routeCreate');
            Cache::forget('routeEdit');

            Cache::forget('scheduleCreate');
            Cache::forget('scheduleEdit');
            
            Cache::forget('vesselCreate');
            Cache::forget('vesselEdit');

            Cache::forget('assignToVesselCreate');
            Cache::forget('assignToVesselEdit');

            Cache::forget('myAgentIslandCreate');
            Cache::forget('myAgentIslandEdit');

            Cache::forget('myAssignToVesselCreate');
            Cache::forget('myAssignToVesselEdit');

            Cache::forget('myAssignedVesselCreate');
            Cache::forget('myAssignedVesselEdit');

        } catch(Exception $e) {
            DB::rollBack();//Something went wrong so we undo the transaction. It won't be saved to database
            return redirect("/admin/user/")->withErrors([ $e->getMessage() ])->withInput();//if it is an sql error then it will show the entire sql error. If we want we can put custom message
        } catch(Throwable $e) {
            DB::rollBack();//same thing here
            return redirect("/admin/user/")->withErrors([ $e->getMessage() ])->withInput();
        }
        
        return redirect('/admin/user/')->with('Delete-success','User deleted successfully!');//if no errors then redirect back to page with alert
    }
}
