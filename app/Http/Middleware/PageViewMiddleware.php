<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PageViewMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        //May have to use switch case if we are going to allow users to access a page using permission.
        //we will need to specify each url.
        foreach ($roles as $role){//check if user has role
            if(!auth()->user()->hasRole($role)){//if user doesn't have role
                return $next($request);
            }else{//if he has role then redirect
                return redirect('/admin/dashboard')->with('no-auth','User is not authorized to access page!');
            }
            
        }
    }
}
