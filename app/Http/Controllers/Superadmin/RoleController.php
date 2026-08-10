<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RoleController extends Controller
{
        public function roles(): Response
    {
        return Inertia::render('superadmin/roles/Index');
    }
}
