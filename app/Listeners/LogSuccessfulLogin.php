<?php

namespace App\Listeners;
use Log;
use Auth;
use Illuminate\Auth\Events\Login;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\User;
class LogSuccessfulLogin
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  Login  $event
     * @return void
     */
    public function handle(Login $event)
    {
       $user = Auth::guard('web')->user();
       $event->user->last_login =  date('Y-m-d H:i:s');
       Log::info($user);
       $event->user->save();
       Log::info($user);
        
    }
}
