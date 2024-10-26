<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\addAdminRequest;
use App\Http\Responses\Response;
use App\Models\User\User;

class OwnerAdminController extends Controller
{
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// Add Admin :
    public function addAdmin(addAdminRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return Response::Message("User not found!", 404);
        }

        if ($user->Role === 'admin') {
            return Response::Message("User is already an admin", 400);
        }

        $user->Role = 'admin';
        $user->save();

        return Response::Message("User promoted to admin successfully!", 200);
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// Delete Admin :
    public function removeAdmin( $email_user)
    {
        $user = User::where('email', $email_user)->first();

        if (!$user) {
            return Response::Message("User not found!", 404);
        }

        if ($user->Role !== 'admin') {
            return Response::Message("User is not an admin", 400);
        }

        $user->Role = 'user';
        $user->save();
        return Response::Message("Admin rights removed successfully!", 200);
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// Show All Admin :
    public function getAllAdmins()
    {
        $admins = User::where('Role', 'admin')->get();
        $adminCount = $admins->count();

        return response()->json([
            'admin_count' => $adminCount,
            'admins' => $admins
        ], 200);
    }


}


