<?php

namespace App\Http\Controllers;

use Spatie\Activitylog\Models\Activity;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ActivityLogController extends Controller
{
    public function index()
    {
        return Inertia::render('ActivityLog/Index', [
            'historia' => Activity::with(['causer', 'subject'])
                ->latest()
                ->paginate(20)
                ->withQueryString()
                ->through(fn ($activity) => [
                    'id' => $activity->id,
                    'description' => $activity->description,
                    'subject_type' => str_replace('App\\Models\\', '', $activity->subject_type),
                    'subject_id' => $activity->subject_id,
                    'causer' => $activity->causer ? $activity->causer->first_name . ' ' . $activity->causer->last_name : 'System',
                    'properties' => $activity->properties,
                    'created_at' => $activity->created_at->format('Y-m-d H:i:s'),
                    'link' => $this->getLink($activity),
                ])
        ]);
    }

    private function getLink($activity)
    {
        $type = strtolower(str_replace('App\\Models\\', '', $activity->subject_type));

        // Mapowanie typów modeli na ścieżki URL
        $map = [
            'client' => 'clients',
            'zapytania' => 'zapytania',
            'oferta' => 'oferta',
        ];

        $route = $map[$type] ?? null;

        if ($route && $activity->subject_id) {
            return "/{$route}/{$activity->subject_id}/edit";
        }

        return null;
    }
}
