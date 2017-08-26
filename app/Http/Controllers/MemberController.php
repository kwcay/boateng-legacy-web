<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MemberController extends Controller
{
    /**
     * @return Illuminate\Http\Response
     */
    public function index()
    {
        return view('member.index')->withMember($this->request->user());
    }

    /**
     * @return Illuminate\Http\Response
     */
    public function settings()
    {
        return view('member.settings.index', [
            'member' => $this->request->user()
        ]);
    }

    /**
     *
     */
    protected function boot()
    {
        $this->middleware('auth');
    }
}
