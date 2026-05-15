<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReminderRuleStoreRequest;
use App\Models\ReminderRule;
use App\Models\User;
use App\Services\ReminderTemplateRenderer;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class ReminderRuleController extends Controller
{
    public function index()
    {
        return Inertia::render('ReminderRule/Index', [
            'rules' => ReminderRule::orderBy('event')->orderBy('days_before')->get()->map(function ($r) {
                return [
                    'id' => $r->id,
                    'name' => $r->name,
                    'event' => $r->event,
                    'event_label' => ReminderRule::EVENTS[$r->event] ?? $r->event,
                    'days_before' => $r->days_before,
                    'recipients' => $r->recipients,
                    'active' => $r->active,
                ];
            }),
            'events' => ReminderRule::EVENTS,
        ]);
    }

    public function create()
    {
        return Inertia::render('ReminderRule/Create', [
            'events' => ReminderRule::EVENTS,
            'users' => User::select('id', 'first_name', 'last_name')->orderBy('last_name')->get(),
            'placeholders' => $this->placeholderMap(),
        ]);
    }

    public function store(ReminderRuleStoreRequest $request)
    {
        ReminderRule::create(array_merge($request->validated(), [
            'active' => (bool) $request->input('active', true),
        ]));

        return Redirect::route('reminder-rules.index')->with('success', 'Regułę dodano.');
    }

    public function edit(ReminderRule $reminderRule)
    {
        return Inertia::render('ReminderRule/Edit', [
            'rule' => [
                'id' => $reminderRule->id,
                'name' => $reminderRule->name,
                'event' => $reminderRule->event,
                'days_before' => $reminderRule->days_before,
                'recipients' => $reminderRule->recipients,
                'subject' => $reminderRule->subject,
                'body' => $reminderRule->body,
                'active' => $reminderRule->active,
            ],
            'events' => ReminderRule::EVENTS,
            'users' => User::select('id', 'first_name', 'last_name')->orderBy('last_name')->get(),
            'placeholders' => $this->placeholderMap(),
        ]);
    }

    public function update(ReminderRuleStoreRequest $request, ReminderRule $reminderRule)
    {
        $reminderRule->update(array_merge($request->validated(), [
            'active' => (bool) $request->input('active', true),
        ]));

        return Redirect::route('reminder-rules.index')->with('success', 'Regułę zapisano.');
    }

    public function destroy(ReminderRule $reminderRule)
    {
        $reminderRule->delete();
        return Redirect::route('reminder-rules.index')->with('success', 'Regułę usunięto.');
    }

    public function toggle(ReminderRule $reminderRule)
    {
        $reminderRule->active = !$reminderRule->active;
        $reminderRule->save();
        return Redirect::back()->with('success', $reminderRule->active ? 'Włączono regułę.' : 'Wyłączono regułę.');
    }

    private function placeholderMap(): array
    {
        $map = [];
        foreach (array_keys(ReminderRule::EVENTS) as $event) {
            $map[$event] = ReminderTemplateRenderer::availablePlaceholders($event);
        }
        return $map;
    }
}
