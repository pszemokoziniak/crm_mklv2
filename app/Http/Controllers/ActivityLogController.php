<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ActivityLogController extends Controller
{
    public function index()
    {
        return Inertia::render('ActivityLog/Index', [
            'historia' => ActivityLog::with('client')
                ->with('user')
                ->OrderByCreatedAt()
                ->paginate(10)
                ->withQueryString()
                ->through(fn ($historia) => [
                    'id' => $historia->id,
                    'action' => $historia->action,
                    'link_id' => $historia->link_id ? $historia->link_id : null,
                    'client' => $historia->client ? $historia->client : null,
                    'link_action' => $historia->link_action,
                    'changes' => $historia->changes,
                    'user' => $historia->user ? $historia->user : null,
                    'deleted_at' => $historia->deleted_at,
                    'created_at' => date($historia->created_at)
                ])
        ]);
    }
}
