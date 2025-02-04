<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Fetch profile of user/business
     */
    function getUser() {
        $user = auth()->user();
        $user['avatar'] = $user->logo;
        unset($user->logo);

        return response()->json([
            'status' => 'success',
            'message' => 'Profile fetched successfully.',
            'data' => $user
        ]);
    }
}
