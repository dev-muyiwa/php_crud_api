<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    /**
     * Returns a User model based on the id in the route.
     *
     * @param User $user
     * @return JsonResponse
     */
    public function getUser(User $user): JsonResponse
    {
//        $user->posts();
//        $user->comments();
        return response()->json($user, 200);
    }
}
