<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\ResponseResource;
use Illuminate\Support\Facades\Response;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->get();

        return new ResponseResource(true, 'List of users', $users, [
            'total_user' => $users->count()
        ]);
    }
}
