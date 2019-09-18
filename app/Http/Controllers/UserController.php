<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Response;

class UserController extends Controller
{
    private $usersPerPage = 10;

    /**
     * Display list of users.
     *
     * @return Response
     */
    public function index()
    {
        $users = User::latest()->paginate($this->usersPerPage);
        return view('users.index', compact('users'))
            ->with('i', (request()->input('page', 1) - 1) * $this->usersPerPage);
    }

    /**
     * Display the specified user.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        return view('users.profile', ['user' => User::findOrFail($id)]);
    }
}
