<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('private.chat' . $this->userID . '-' . $this->trainer_id,
    function ($user, $userID, $trainerID) {
    return (int) $user->id === (int) $userID;
});

Broadcast::channel('private.chat.Doctor' . $this->userID . '-' . $this->doctor_id,
    function ($user, $userID, $doctorID) {
        return (int) $user->id === (int) $userID;
    });
