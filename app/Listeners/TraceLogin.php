<?php

namespace App\Listeners;

use App\ActionType;
use App\Models\Tracing;
use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class TraceLogin
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
     * @param  \Illuminate\Auth\Events\Login  $event
     * @return void
     */
    public function handle(Login $event)
    {
        Tracing::create([
            'at' => now(),
            'user_id' => $event->user->id,
            'action_type' => ActionType::Login,
        ]);
    }
}
