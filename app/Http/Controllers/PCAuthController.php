<?php

namespace App\Http\Controllers;

use App\User;
use Hash;
use Illuminate\Http\Request;

class PCAuthController extends Controller
{
    public function loginRequest(Request $request)
    {
        $username = $request['username'];
        $password = $request['password'];

        if (!User::whereUsername($username)->exists())
        {
            return "***INVALID LOGIN***";
        }

        $user = User::whereUsername($username)->first();

        if (!Hash::check($password,$user->password))
        {
            return "***INVALID LOGIN***";
        }

        // Authenticated!!

        return "
".$user->username."
".$user->name."
".$user->email."
".($user->hasRole('admin')?'Administrators':'');
    }
}
