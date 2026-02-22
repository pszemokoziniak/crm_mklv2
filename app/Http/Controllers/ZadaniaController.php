<?php

namespace App\Http\Controllers;

use App\Http\Requests\ZadaniaStoreRequest;
use App\Models\User;
use App\Models\Zadania;
use App\Models\ZadaniaStage;
use App\Models\ZadaniaMilestone;
use Illuminate\Support\Facades\Request;
use App\Traits\StoreActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class ZadaniaController extends Controller
{
    use StoreActivityLog;

    public function index()
    {
        return Inertia::render('Zadania/Index', [
            'filters' => Request::all('search', 'trashed'),
            'zadanias' => Zadania::with(['user', 'responsiblePerson'])
                ->filter(Request::only('search', 'trashed'))
                ->paginate(10)
                ->withQueryString()
                ->through(fn ($zadania) => [
                    'id' => $zadania->id,
                    'responsible_person_id' => $zadania->responsiblePerson,
                    'subject' => $zadania->subject,
                    'description' => $zadania->description,
                    'deadline' => $zadania->deadline,
                    'user' => $zadania->user,
                    'deleted_at' => $zadania->deleted_at,
                    'created_at' => $zadania->created_at->format('Y-m-d H:i')
                ])
        ]);
    }

    public function create()
    {
        return Inertia::render('Zadania/Create', [
            'users' => User::all(['id', 'first_name', 'last_name']),
        ]);
    }

    public function store(ZadaniaStoreRequest $request)
    {
        $zadania = Zadania::create($request->validated());

        if ($request->has('milestones')) {
            foreach ($request->input('milestones') as $milestoneData) {
                if (!empty($milestoneData['name'])) {
                    // Wyciągamy tylko pola należące do modelu Milestone, bez tablicy stages
                    $milestone = $zadania->milestones()->create([
                        'name' => $milestoneData['name'],
                        'deadline' => $milestoneData['deadline'] ?? null,
                        'completed_at' => $milestoneData['completed_at'] ?? null,
                    ]);

                    if (isset($milestoneData['stages'])) {
                        foreach ($milestoneData['stages'] as $stageData) {
                            if (!empty($stageData['name'])) {
                                $milestone->stages()->create([
                                    'zadania_id' => $zadania->id,
                                    'name' => $stageData['name'],
                                    'status' => $stageData['status'] ?? 'pending',
                                    'order' => $stageData['order'] ?? 0,
                                ]);
                            }
                        }
                    }
                }
            }
        }

        return Redirect::route('zadania')->with('success', 'Zadanie utworzone.');
    }

    public function edit(Zadania $zadania)
    {
        return Inertia::render('Zadania/Edit', [
            'zadanie' => [
                'id' => $zadania->id,
                'responsible_person_id' => $zadania->responsible_person_id,
                'subject' => $zadania->subject,
                'description' => $zadania->description,
                'deadline' => $zadania->deadline,
                'user_id' => $zadania->user_id,
                'deleted_at' => $zadania->deleted_at,
                'assignments' => $zadania->assignments()->with(['assignedUser', 'assigner'])->get(),
                'milestones' => $zadania->milestones()->with('stages')->get(),
            ],
            'users' => User::all(['id', 'first_name', 'last_name']),
        ]);
    }

    public function update(Zadania $zadania, ZadaniaStoreRequest $request)
    {
        $zadania->update($request->validated());

        if ($request->has('milestones')) {
            $zadania->milestones()->each(function($milestone) {
                $milestone->stages()->delete();
                $milestone->delete();
            });

            foreach ($request->input('milestones') as $milestoneData) {
                if (!empty($milestoneData['name'])) {
                    // Jawne przekazanie pól, aby uniknąć błędu "Array to string conversion" (przez tablicę 'stages')
                    $milestone = $zadania->milestones()->create([
                        'name' => $milestoneData['name'],
                        'deadline' => $milestoneData['deadline'] ?? null,
                        'completed_at' => $milestoneData['completed_at'] ?? null,
                    ]);

                    if (isset($milestoneData['stages'])) {
                        foreach ($milestoneData['stages'] as $stageData) {
                            if (!empty($stageData['name'])) {
                                $milestone->stages()->create([
                                    'zadania_id' => $zadania->id,
                                    'name' => $stageData['name'],
                                    'status' => $stageData['status'] ?? 'pending',
                                    'order' => $stageData['order'] ?? 0,
                                ]);
                            }
                        }
                    }
                }
            }
        }

        return Redirect::route('zadania')->with('success', 'Zadanie zaktualizowane.');
    }

    public function destroy(Zadania $zadania)
    {
        $zadania->delete();
        return Redirect::back()->with('success', 'Zadanie zarchiwizowane.');
    }

    public function restore(Zadania $zadania)
    {
        $zadania->restore();
        return Redirect::back()->with('success', 'Zadanie przywrócone');
    }
}
