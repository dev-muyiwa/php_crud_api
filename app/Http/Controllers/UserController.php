<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Returns the currently authenticated user
     *
     * @return JsonResponse
     */
    public function getUser(): JsonResponse
    {
        $user = Auth::user();
        return response()->json($user, 200);
    }

    public function updateUserCredentials(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $user->update($request->all());
        return redirect()->route("user-profile");
    }
}
