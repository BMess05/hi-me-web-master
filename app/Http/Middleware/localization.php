<?php

namespace App\Http\Middleware;

use Closure;
use App\User;
class localization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $input = $request->all();
        if(empty($input)) {
            $input = json_decode(file_get_contents('php://input'), true);
        }
        $userData = User::where('id', $input['user_id'])->first();
        if($userData){
            app()->setLocale($userData->language);
        }else{
            app()->setLocale('en');
        }
        
        return $next($request);
    }
}
