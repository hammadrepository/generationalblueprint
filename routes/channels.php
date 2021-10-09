<?php

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

use App\Group;
use App\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('users.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
Broadcast::channel('groups', function ($user) {

    return true;
});
Broadcast::channel('user.ban.{userto}', function ($user,User $userto) {
    return true;
});
Broadcast::channel('groups.{group}', function ($user,Group $group) {
    return $group->hasUser($user->id);
});
//Broadcast::channel('App.User.{id}', function ($user, $id) {
//    return (int) $user->id === (int) $id;
//});
