<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use App\Http\Requests\StoreRoleRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use Throwable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
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
        $rolesIndex = Cache::remember('roleIndex', 60, function(){//Right now we are doing caching for 60 seconds
            $roles = Role::orderBy('id','desc')->get();//get all roles

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

            return compact('roles','admin','staff','merchant','agent');
        });
        $roles = $rolesIndex['roles'];
        $admin = $rolesIndex['admin'];
        $staff = $rolesIndex['staff'];
        $merchant = $rolesIndex['merchant'];
        $agent = $rolesIndex['agent'];
        return view('admin.role.index', compact('roles','admin','staff','merchant','agent'));//sending the variable to the view
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     * 
     * This method is used to create role
     */
    public function create()
    {
        $roleCreate = Cache::remember('roleCreate', 60, function(){//Right now we are doing caching for 60 seconds
            $permissions = Permission::get();
            $permissionNames = Permission::get()->pluck('name');
            return compact('permissions','permissionNames');
        });
        $permissions = $roleCreate['permissions'];
        $permissionNames = $roleCreate['permissionNames'];
        return view('admin.role.create',compact('permissions','permissionNames'));//sending the variable to the view
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * 
     * This method is what happens when we submit in create page
     */
    public function store(Request $request, StoreRoleRequest $requestchange)
    {
        DB::beginTransaction();//start transaction, This is to have control over rollbacks and commits
        /**
         * Using storeRole request form to validate the data. In the request form it will 
         * prepare the data before it is validated. The data entering can be multiple
         * and it is in a single string with comma that separate them. Eg. create-booking,edit-booking,delete-booking
         * We are splitting it and inserting it in an array. After that we merge it to the field.
         * Next, it will validate the field using the rule created inside the request form.
         */
        $requestchange->validated();//if an error occurs it will redirect back to page

        try {
            $role = new Role;//making a new role
            $role->name = $requestchange->role_name;//initializing with what user entered
            $role->slug = $requestchange->role_slug;
            $role-> save();//save

            $listOfPermissions = $requestchange->permission; //Array of permission names
            $listOfPermissionsID = Permission::whereIn('name',$listOfPermissions)->pluck('id')->toArray();//Array of permission ID
            if ($listOfPermissions[0]=="") {//If it is empty then save. This way there can be roles with no permissions
                $role->save();//save
            }else{
                $role->permissions()->attach($listOfPermissionsID);//we attach permission ids using the permission relationship. It will save to role_permission table
                $role->save();//save
            }

            DB::commit();//This means nothing went wrong so we can commit/save to the database//commit/save to the database

            /**
             * We are forgetting all these caches because they are getting role for their pages.
             * If there is a change then we must forget the cache so that the new role will be seen on these pages.
             */
            Cache::forget('roleIndex');//when there is a change remove the cache.

            Cache::forget('userCreate');
            Cache::forget('userEdit');

        } catch(Exception $e) {
            DB::rollBack();//Something went wrong so we undo the transaction. It won't be saved to database
            return redirect("/admin/role/create")->withErrors([ $e->getMessage() ])->withInput();//if it is an sql error then it will show the entire sql error. If we want we can put custom message
        } catch(Throwable $e) {
            DB::rollBack();//same thing here
            return redirect("/admin/role/create")->withErrors([ $e->getMessage() ])->withInput();
        }
        
        return redirect('/admin/role/')->with('success', 'Role Created!');//if no errors then redirect back to page with alert
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     * 
     * This method is used to show data. Individual data.
     */
    public function show(Role $role)
    {
        return view('admin.role.show', compact('role'));//sending the variable to the view
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     * 
     * This method is used to edit role
     */
    public function edit(Role $role)
    {
        $roleEdit = Cache::remember('roleEdit', 60, function(){//Right now we are doing caching for 60 seconds
            $permissions =  Permission::get();//get all permissions
            $permissionNames = Permission::get()->pluck('name');
            return compact('permissions','permissionNames');
        });
        $permissions = $roleEdit['permissions'];
        $permissionNames = $roleEdit['permissionNames'];
        return view('admin.role.edit', compact('role','permissions','permissionNames'));//sending the variables to the view
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     * 
     * This method is what happens when we submit in edit page
     */
    public function update(Role $role, StoreRoleRequest $requestchange)
    {
        DB::beginTransaction();//start transaction, This is to have control over rollbacks and commits
        /**
         * Using storeRole request form to validate the data. In the request form it will 
         * prepare the data before it is validated. The data entering can be multiple
         * and it is in a single string with comma that separate them. Eg. create-booking,edit-booking,delete-booking
         * We are splitting it and inserting it in an array. After that we merge it to the field.
         * Next, it will validate the field using the rule created inside the request form.
         */
        $requestchange->validated();//if an error occurs it will redirect back to page
        
        try {
            $role->name = $requestchange->role_name;
            $role->slug = $requestchange->role_slug;
            $role->save();
            /**
             * We are detaching and deleting it because we are initializing it again with the changes
             */
            $role->permissions()->detach();//deleting the relation that exists
            $role->permissions()->delete();//deleting permission

            $listOfPermissions = $requestchange->permission; //Array of permission names
            $listOfPermissionsID = Permission::whereIn('name',$listOfPermissions)->pluck('id')->toArray();//Array of permission ids

            if ($listOfPermissions[0]=="") {//If it is empty then save. This way there can be roles with no permissions
                $role->save();//save
            }else{
                $role->permissions()->attach($listOfPermissionsID);//we attach permission ids using the permission relationship. It will save to role_permission table
                $role->save();//save
            }

            DB::commit();//This means nothing went wrong so we can commit/save to the database//commit/save to the database
            
            /**
             * We are forgetting all these caches because they are getting role for their pages.
             * If there is a change then we must forget the cache so that the new role will be seen on these pages.
             */
            Cache::forget('roleIndex');//when there is a change remove the cache.

            Cache::forget('userCreate');
            Cache::forget('userEdit');

        } catch(Exception $e) {
            DB::rollBack();//Something went wrong so we undo the transaction. It won't be saved to database
            return redirect("/admin/role/{$role->id}/edit")->withErrors([ $e->getMessage() ])->withInput();//if it is an sql error then it will show the entire sql error. If we want we can put custom message
        } catch(Throwable $e) {
            DB::rollBack();//same thing here
            return redirect("/admin/role/{$role->id}/edit")->withErrors([ $e->getMessage() ])->withInput();
        }
        
        return redirect('/admin/role')->with('Edit-success','Role Updated!');;//if no errors then redirect back to page with alert
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     * 
     * This method is used for deleting role
     */
    public function destroy(Role $role)
    {
        DB::beginTransaction();//start transaction, This is to have control over rollbacks and commits

        try {
            $role->delete();//deleting it

            DB::commit();//This means nothing went wrong so we can commit/save to the database

            /**
             * We are forgetting all these caches because they are getting role for their pages.
             * If there is a change then we must forget the cache so that the new role will be seen on these pages.
             */
            Cache::forget('roleIndex');//when there is a change remove the cache.

            Cache::forget('userCreate');
            Cache::forget('userEdit');
            
        } catch(Exception $e) {
            DB::rollBack();//Something went wrong so we undo the transaction. It won't be saved to database
            return redirect("/admin/role/")->withErrors([ $e->getMessage() ])->withInput();//if it is an sql error then it will show the entire sql error. If we want we can put custom message
        } catch(Throwable $e) {
            DB::rollBack();//same thing here
            return redirect("/admin/role/")->withErrors([ $e->getMessage() ])->withInput();
        }
        
        return redirect('/admin/role')->with('Delete-success','Role deleted successfully!');//if no errors then redirect back to page with alert
    }
}
