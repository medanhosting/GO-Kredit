<?php

namespace Thunderlabid\Auths\Listeners;

use Thunderlabid\Auths\Events\UserCreated;
use Hash;

class EncryptPassword
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
     * Handle event
     * @param  UserCreated $event [description]
     * @return [type]             [description]
     */
    public function handle($event)
    {
        $user = $event->data;

        if (Hash::needsRehash($user->password)) 
        {
            $user->password = Hash::make($user->password); 
        }
    }
}