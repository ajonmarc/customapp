<?php

namespace App\Http\Responses;

use Illuminate\Http\Request;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = $request->user();
        $roleName = $user?->role?->name;

        $redirectTo = match ($roleName) {
            'Superadmin' => route('superadmin.dashboard'),
            'Admin' => route('admin.dashboard'),
            'User' => route('user.dashboard'),
        };

        return $request->wantsJson()
            ? response()->json(['two_factor' => false])
            : redirect()->intended($redirectTo);
    }
}