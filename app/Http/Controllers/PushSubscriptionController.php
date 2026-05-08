<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PushSubscriptionController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'endpoint' => 'required|url',
            'keys.auth' => 'required|string',
            'keys.p256dh' => 'required|string',
        ]);

        Auth::user()->updatePushSubscription(
            $request->input('endpoint'),
            $request->input('keys.p256dh'),
            $request->input('keys.auth'),
            $request->input('content_encoding', 'aesgcm')
        );

        return response()->json(['success' => true]);
    }

    public function destroy(Request $request)
    {
        Auth::user()->deletePushSubscription($request->input('endpoint'));

        return response()->json(['success' => true]);
    }
}
