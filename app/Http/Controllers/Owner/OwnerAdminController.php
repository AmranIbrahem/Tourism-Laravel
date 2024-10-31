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
            return Response::UserNotFound();
        }

        if ($user->Role === 'admin') {
            return Response::Message400("User is already an admin");
        }

        $user->Role = 'admin';
        $user->save();

        return Response::Message200("User promoted to admin successfully!");
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// Delete Admin :
    public function removeAdmin( $email_user)
    {
        $user = User::where('email', $email_user)->first();

        if (!$user) {
            return Response::UserNotFound("User not found!");
        }

        if ($user->Role !== 'admin') {
            return Response::Message400("User is not an admin");
        }

        $user->Role = 'user';
        $user->save();
        return Response::Message200("Admin rights removed successfully!");
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


