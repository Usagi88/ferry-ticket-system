<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)//... token to denote that the function accepts a variable number of arguments. The arguments will be passed into the given variable as an array
    {
        foreach ($roles as $role){//check if user has role
            if(auth()->user()->hasRole($role)){
                return $next($request);
            }
        }
        //$url = $request->url();
        //$containsAgentIsland = Str::contains($url, 'my-agent-island');
        //if($containsAgentIsland && auth()->user()->hasRole('merchant') ){
        //    return redirect('/admin/dashboard')->with('no-auth','User is not authorized to access page!');
        //}
        //$containsAssignVessel = Str::contains($url, 'my-assign-vessel');
        //if($containsAssignVessel && auth()->user()->hasRole('agent') ){
        //    return redirect('/admin/dashboard')->with('no-auth','User is not authorized to access page!');
        //}

        
        //May have to use switch case if we are going to allow users to access a page using permission.
        //we will need to specify each url.

        if ($request->route('user')) {//if user found
            $user = $request->route()->parameter('user');//get user
            if (Auth::check() && Auth::id() != $user->id) {//if user is logged in and if logged in id is not equal to user id
                return redirect('/admin/dashboard')->with('no-auth','User is not authorized to access page!');
            }else{//else run the request
                return $next($request);
            }
        }
        return redirect('/admin/dashboard')->with('no-auth','User is not authorized to access page!');
    }
}
