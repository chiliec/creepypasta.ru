<?php

namespace App\Http\Controllers;

use Clarkeash\Doorman\Facades\Doorman;
use Clarkeash\Doorman\Models\Invite;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class InviteController extends Controller
{
    private $invitesPerPage = 10;

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display Invite collection for current user
     *
     * @return Response
     */
    public function index()
    {
        $invites = Invite::latest()->paginate($this->invitesPerPage);
        return view('invites.index', compact('invites'))
            ->with('i', (request()->input('page', 1) - 1) * $this->invitesPerPage);
    }

    /**
     * Create new Invite
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        Doorman::generate()->make();
        return redirect(action('InviteController@index'))->with('status', 'Invite successfully created');
    }
}
