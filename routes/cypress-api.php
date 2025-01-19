<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::delete('/delete-user', function(Request $request) {
    $user = \App\Models\User::firstWhere('email', $request->input('email'));

    $user->delete();

    return response()->json([
        'message' => 'User deleted'
    ]);
});
