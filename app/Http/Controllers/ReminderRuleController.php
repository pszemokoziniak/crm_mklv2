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
                    'channels' => $r->channels,
                    'active' => $r->active,
                ];
            }),
            'events' => ReminderRule::EVENTS,
            'channels' => ReminderRule::CHANNELS,
        ]);
    }

    public function create()
    {
        return Inertia::render('ReminderRule/Create', [
            'events' => ReminderRule::EVENTS,
            'channels' => ReminderRule::CHANNELS,
            'immediateEvents' => ReminderRule::IMMEDIATE_EVENTS,
            'users' => User::select('id', 'first_name', 'last_name')->orderBy('last_name')->get(),
            'placeholders' => $this->placeholderMap(),
            'defaultSubject' => 'Przypomnienie: {{projekt_nazwa}} — termin za {{days_until}} dni',
            'defaultBody' => $this->defaultBodyTemplate(),
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
                'channels' => $reminderRule->channels,
                'subject' => $reminderRule->subject,
                'body' => $reminderRule->body,
                'active' => $reminderRule->active,
            ],
            'events' => ReminderRule::EVENTS,
            'channels' => ReminderRule::CHANNELS,
            'immediateEvents' => ReminderRule::IMMEDIATE_EVENTS,
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

    private function defaultBodyTemplate(): string
    {
        return <<<HTML
<h2>Cześć {{opiekun}}!</h2>
<p>Przypominamy, że za <strong>{{days_until}}</strong> dni przypada ważny termin w projekcie <strong>{{projekt_nazwa}}</strong> (klient: <em>{{client_nazwa}}</em>).</p>
<p><strong>Data terminu:</strong> {{data}}</p>
<p>Co warto zrobić:</p>
<ul>
  <li>Sprawdzić aktualny status w CRM</li>
  <li>Skontaktować się z klientem, jeśli potrzeba</li>
  <li>Zaktualizować notatki po kontakcie</li>
</ul>
<p>Pełne szczegóły znajdziesz w rekordzie pod przyciskiem poniżej.</p>
HTML;
    }
}
