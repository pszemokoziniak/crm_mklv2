<?php

namespace App\Http\Controllers;

use App\Http\Requests\EditRequest;
use App\Http\Requests\EmailStoreRequest;
use App\Models\Email;
use App\Models\EmailsType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class EmailController extends Controller
{
    public function index()
    {
        return Inertia::render('Email/Index', [
            'email' => Email::select('emails.id','first_name', 'last_name', 'name', 'users.email')->join('users', 'users.id', 'emails.user_id')
                ->join('emails_type', 'emails_type.id', 'emails.type_id')
                ->get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Email/Create', [
            'users' => User::select('id', 'first_name', 'last_name')->get(),
            'emailsType' => EmailsType::get(),
        ]);
    }

    public function store(EmailStoreRequest $request)
    {
        if ($this->checkExists($request)) {
            return Redirect::back()->with('error', 'Taki rekord już istnieje..');
        };

        Email::create($request->all());

        return Redirect::route('email')->with('success', 'Email dodany.');
    }

    public function edit(Email $email)
    {
        return Inertia::render('Email/Edit', [
            'email' => [
                'id' => $email->id,
                'user_id' => $email->user_id,
                'type_id' => $email->type_id,
            ],
            'users' => User::get(),
            'emailsType' => EmailsType::get(),
        ]);
    }

    public function update(EmailStoreRequest $request)
    {
        if ($this->checkExists($request)) {
            return Redirect::back()->with('error', 'Taki rekord już istnieje..');
        };

        Email::find($request->id)->update(
            ['user_id' => $request->user_id, 'type_id' => $request->type_id]
        );

        return Redirect::route('email')->with('success', 'Poprawiono.');
    }

    public function destroy(Email $email)
    {
        $email->delete();

        return Redirect::route('email')->with('success', 'Usunięto.');
    }
    public function checkExists($request)
    {
        $email = Email::where('user_id', $request->user_id)
            ->where('type_id', $request->type_id)
            ->get();
        return $email->count() > 0 ? true : false;
    }
}
