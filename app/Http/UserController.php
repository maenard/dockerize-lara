<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Handles getting logged in user
     *
     * @http POST
     * @middleware auth:sanctum
     *
     * @param $request
     */
    public function index(Request $request)
    {
        return $request->user();
    }
}

