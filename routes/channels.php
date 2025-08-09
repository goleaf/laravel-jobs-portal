<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Auth;

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

Broadcast::channel('App.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Candidate-specific job application updates
Broadcast::channel('job-application.{candidateId}', function ($user, $candidateId) {
    return (int) $user->id === (int) $candidateId;
});

// Employer-specific job applications for their company
Broadcast::channel('job-applications.{companyId}', function ($user, $companyId) {
    return (int) optional($user->company)->id === (int) $companyId;
});
