<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\ZgloszeniePriority;
use App\Enums\ZgloszenieStatus;
use App\Http\Requests\StoreZgloszenieRequest;
use App\Models\Note;
use App\Models\User;
use App\Models\Zgloszenie;
use App\Models\ZgloszenieFile;
use App\Notifications\NoteMentionNotification;
use App\Notifications\ZgloszenieAssignedNotification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ZgloszeniaController extends Controller
{
    public function index(): Response
    {
        $filters = Request::all('search', 'status', 'priority', 'assignee', 'trashed', 'view');
        $view = ($filters['view'] ?? null) === 'lista' ? 'lista' : 'kanban';

        $query = Zgloszenie::query()
            ->with(['reporter:id,first_name,last_name', 'assignee:id,first_name,last_name'])
            ->withCount(['notes', 'screenshots'])
            ->filter($filters)
            ->orderByRaw("CASE priority WHEN 'wysoki' THEN 1 WHEN 'normalny' THEN 2 ELSE 3 END")
            ->orderByRaw('deadline IS NULL, deadline')
            ->orderByDesc('id');

        return Inertia::render('Zgloszenia/Index', [
            'filters' => array_merge($filters, ['view' => $view]),
            'statuses' => ZgloszenieStatus::options(),
            'priorities' => ZgloszeniePriority::options(),
            'users' => $this->assignableUsers(),
            'columns' => $view === 'kanban' ? $this->kanbanColumns($query) : null,
            'zgloszenia' => $view === 'lista'
                ? $query->paginate(20)->withQueryString()->through(fn (Zgloszenie $zgloszenie) => $this->card($zgloszenie))
                : null,
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Zgloszenie::class);

        return Inertia::render('Zgloszenia/Create', [
            'statuses' => ZgloszenieStatus::options(),
            'priorities' => ZgloszeniePriority::options(),
            'users' => $this->assignableUsers(),
        ]);
    }

    public function store(StoreZgloszenieRequest $request): RedirectResponse
    {
        $this->authorize('create', Zgloszenie::class);

        $zgloszenie = Zgloszenie::create($request->safe()->except('screenshots') + [
            'reporter_id' => Auth::id(),
        ]);

        $this->storeUploadedFiles($request->file('screenshots'), $zgloszenie);

        if ($zgloszenie->assignee_id && (int) $zgloszenie->assignee_id !== (int) Auth::id()) {
            $this->notifyAssignee($zgloszenie);
        }

        return Redirect::route('zgloszenia.show', $zgloszenie)->with('success', 'Zgłoszenie dodane.');
    }

    public function show(Zgloszenie $zgloszenie): Response
    {
        $this->authorize('view', $zgloszenie);

        $zgloszenie->load([
            'reporter:id,first_name,last_name',
            'assignee:id,first_name,last_name',
            'screenshots',
            'notes.author:id,first_name,last_name',
            'notes.files',
        ]);

        $user = Auth::user();

        return Inertia::render('Zgloszenia/Show', [
            'zgloszenie' => $this->card($zgloszenie) + [
                'description' => $zgloszenie->description,
                'created_at' => $zgloszenie->created_at?->format('d.m.Y H:i'),
                'updated_at' => $zgloszenie->updated_at?->format('d.m.Y H:i'),
                'screenshots' => $zgloszenie->screenshots->map(fn (ZgloszenieFile $file) => $this->file($file)),
            ],
            'notes' => $zgloszenie->notes->map(fn (Note $note) => $this->note($note, $user)),
            'statuses' => ZgloszenieStatus::options(),
            'priorities' => ZgloszeniePriority::options(),
            'mentionableUsers' => $this->mentionableUsers(),
            'can' => [
                'update' => $user->can('update', $zgloszenie),
                'updateStatus' => $user->can('updateStatus', $zgloszenie),
                'comment' => $user->can('comment', $zgloszenie),
                'delete' => $user->can('delete', $zgloszenie),
            ],
        ]);
    }

    public function edit(Zgloszenie $zgloszenie): Response
    {
        $this->authorize('update', $zgloszenie);

        $zgloszenie->load('screenshots');

        return Inertia::render('Zgloszenia/Edit', [
            'zgloszenie' => [
                'id' => $zgloszenie->id,
                'title' => $zgloszenie->title,
                'description' => $zgloszenie->description,
                'url' => $zgloszenie->url,
                'status' => $zgloszenie->status,
                'priority' => $zgloszenie->priority,
                'assignee_id' => $zgloszenie->assignee_id,
                'deadline' => $zgloszenie->deadline?->format('Y-m-d'),
                'deleted_at' => $zgloszenie->deleted_at?->format('Y-m-d'),
                'screenshots' => $zgloszenie->screenshots->map(fn (ZgloszenieFile $file) => $this->file($file)),
            ],
            'statuses' => ZgloszenieStatus::options(),
            'priorities' => ZgloszeniePriority::options(),
            'users' => $this->assignableUsers(),
        ]);
    }

    public function update(StoreZgloszenieRequest $request, Zgloszenie $zgloszenie): RedirectResponse
    {
        $this->authorize('update', $zgloszenie);

        $previousStatus = $zgloszenie->status;
        $previousAssignee = (int) $zgloszenie->assignee_id;

        $zgloszenie->update($request->safe()->except('screenshots'));

        $this->storeUploadedFiles($request->file('screenshots'), $zgloszenie);

        if ($zgloszenie->status !== $previousStatus) {
            $this->logStatusChange($zgloszenie, $previousStatus);
        }

        if ($zgloszenie->assignee_id && (int) $zgloszenie->assignee_id !== $previousAssignee
            && (int) $zgloszenie->assignee_id !== (int) Auth::id()) {
            $this->notifyAssignee($zgloszenie);
        }

        return Redirect::route('zgloszenia.show', $zgloszenie)->with('success', 'Zgłoszenie zaktualizowane.');
    }

    /** Zmiana statusu bez wchodzenia w formularz (dropdown i drag&drop na kanbanie). */
    public function updateStatus(Zgloszenie $zgloszenie): RedirectResponse
    {
        $this->authorize('updateStatus', $zgloszenie);

        $data = Validator::make(Request::only('status'), [
            'status' => ['required', Rule::in(ZgloszenieStatus::values())],
        ])->validate();

        if ($data['status'] === $zgloszenie->status) {
            return Redirect::back();
        }

        $previousStatus = $zgloszenie->status;
        $zgloszenie->update(['status' => $data['status']]);
        $this->logStatusChange($zgloszenie, $previousStatus);

        return Redirect::back()->with('success', 'Status zmieniony na: '.$zgloszenie->statusLabel());
    }

    public function destroy(Zgloszenie $zgloszenie): RedirectResponse
    {
        $this->authorize('delete', $zgloszenie);

        $zgloszenie->delete();

        return Redirect::route('zgloszenia.index')->with('success', 'Zgłoszenie zarchiwizowane.');
    }

    public function restore(Zgloszenie $zgloszenie): RedirectResponse
    {
        $this->authorize('restore', $zgloszenie);

        $zgloszenie->restore();

        return Redirect::back()->with('success', 'Zgłoszenie przywrócone.');
    }

    /** Dodanie print screenów do istniejącego zgłoszenia. */
    public function storeFiles(Zgloszenie $zgloszenie): RedirectResponse
    {
        $this->authorize('update', $zgloszenie);

        Validator::make(Request::only('screenshots'), [
            'screenshots' => 'required|array|max:10',
            'screenshots.*' => 'required|file|max:10240|mimes:jpg,jpeg,png,gif,webp,pdf',
        ])->validate();

        $this->storeUploadedFiles(Request::file('screenshots'), $zgloszenie);

        return Redirect::back()->with('success', 'Dodano załącznik.');
    }

    public function showFile(Zgloszenie $zgloszenie, ZgloszenieFile $file): BinaryFileResponse
    {
        $this->authorize('view', $zgloszenie);

        if ((int) $file->zgloszenie_id !== (int) $zgloszenie->id) {
            abort(404);
        }

        $absolutePath = storage_path('app/'.$file->path);

        if (! is_file($absolutePath)) {
            abort(404);
        }

        // Obrazy pokazujemy w przeglądarce, resztę oddajemy do pobrania.
        return $file->is_image
            ? response()->file($absolutePath)
            : response()->download($absolutePath, $file->original_name);
    }

    public function destroyFile(Zgloszenie $zgloszenie, ZgloszenieFile $file): RedirectResponse
    {
        $this->authorize('update', $zgloszenie);

        if ((int) $file->zgloszenie_id !== (int) $zgloszenie->id) {
            abort(404);
        }

        $this->deleteFile($file);

        return Redirect::back()->with('success', 'Załącznik usunięty.');
    }

    /** Dodanie komentarza w dyskusji pod zgłoszeniem. */
    public function storeComment(Zgloszenie $zgloszenie): RedirectResponse
    {
        $this->authorize('comment', $zgloszenie);

        $data = Validator::make(Request::all(), [
            'body' => 'required|string|max:10000',
            'files' => 'nullable|array|max:10',
            'files.*' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,gif,webp,pdf',
        ], [
            'body.required' => 'Treść komentarza jest wymagana.',
            'files.*.max' => 'Plik jest zbyt duży (max 10 MB).',
            'files.*.mimes' => 'Dozwolone formaty: jpg, png, gif, webp, pdf.',
        ])->validate();

        $note = $zgloszenie->notes()->create([
            'user_id' => Auth::id(),
            'body' => $data['body'],
            'system' => false,
        ]);

        $this->storeUploadedFiles(Request::file('files'), $zgloszenie, $note->id);

        $this->notifyMentioned($note);

        return Redirect::back()->with('success', 'Komentarz dodany.');
    }

    public function updateComment(Note $note): RedirectResponse
    {
        if ($note->system || (int) $note->user_id !== (int) Auth::id()) {
            return Redirect::back()->with('error', 'Możesz edytować tylko swoje komentarze.');
        }

        $data = Validator::make(Request::only('body'), [
            'body' => 'required|string|max:10000',
        ])->validate();

        $previousBody = $note->body;
        $note->update(['body' => $data['body']]);

        // Powiadamiamy tylko nowo wywołane osoby — bez dublowania.
        $this->notifyMentioned($note, $previousBody);

        return Redirect::back()->with('success', 'Komentarz poprawiony.');
    }

    public function destroyComment(Note $note): RedirectResponse
    {
        $user = Auth::user();

        if ($note->system || ((int) $note->user_id !== (int) $user->id && ! $this->isOffice($user))) {
            return Redirect::back()->with('error', 'Możesz usunąć tylko swoje komentarze.');
        }

        foreach ($note->files as $file) {
            $this->deleteFile($file);
        }

        $note->delete();

        return Redirect::back()->with('success', 'Komentarz usunięty.');
    }

    /**
     * Kolumny kanbana — wszystkie widoczne zgłoszenia pogrupowane po statusie.
     *
     * @return array<int, array<string, mixed>>
     */
    private function kanbanColumns(Builder $query): array
    {
        $items = $query->get();

        return array_map(function (ZgloszenieStatus $status) use ($items) {
            $inColumn = $items->where('status', $status->value)->values();

            return [
                'value' => $status->value,
                'label' => $status->label(),
                'count' => $inColumn->count(),
                'items' => $inColumn->map(fn (Zgloszenie $zgloszenie) => $this->card($zgloszenie)),
            ];
        }, ZgloszenieStatus::ordered());
    }

    /**
     * @return array<string, mixed>
     */
    private function card(Zgloszenie $zgloszenie): array
    {
        return [
            'id' => $zgloszenie->id,
            'title' => $zgloszenie->title,
            'url' => $zgloszenie->url,
            'status' => $zgloszenie->status,
            'status_label' => $zgloszenie->statusLabel(),
            'priority' => $zgloszenie->priority,
            'deadline' => $zgloszenie->deadline?->format('Y-m-d'),
            'deleted_at' => $zgloszenie->deleted_at?->format('Y-m-d'),
            'notes_count' => $zgloszenie->notes_count ?? $zgloszenie->notes()->count(),
            'screenshots_count' => $zgloszenie->screenshots_count ?? $zgloszenie->screenshots()->count(),
            'reporter' => $this->person($zgloszenie->reporter),
            'assignee' => $this->person($zgloszenie->assignee),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function note(Note $note, User $user): array
    {
        return [
            'id' => $note->id,
            'body' => $note->body,
            'system' => (bool) $note->system,
            'author' => $this->person($note->author),
            'created_at' => $note->created_at?->format('d.m.Y H:i'),
            'updated_at' => $note->updated_at?->format('d.m.Y H:i'),
            'edited' => $note->updated_at?->ne($note->created_at) ?? false,
            'files' => $note->files->map(fn (ZgloszenieFile $file) => $this->file($file)),
            'can_edit' => ! $note->system && (int) $note->user_id === (int) $user->id,
            'can_delete' => ! $note->system && ((int) $note->user_id === (int) $user->id || $this->isOffice($user)),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function file(ZgloszenieFile $file): array
    {
        return [
            'id' => $file->id,
            'name' => $file->original_name,
            'is_image' => $file->is_image,
            'size' => $file->size,
            'url' => route('zgloszenia.files.show', ['zgloszenie' => $file->zgloszenie_id, 'file' => $file->id]),
        ];
    }

    /**
     * @return array{id: int, name: string}|null
     */
    private function person(?User $user): ?array
    {
        return $user ? ['id' => $user->id, 'name' => trim($user->first_name.' '.$user->last_name)] : null;
    }

    /**
     * Lista osób do przypisania (select). @return Collection<int, array{id:int, name:string}>
     */
    private function assignableUsers(): Collection
    {
        return User::where('active', true)
            ->orderByName()
            ->get(['id', 'first_name', 'last_name'])
            ->map(fn (User $user) => ['id' => $user->id, 'name' => trim($user->first_name.' '.$user->last_name)])
            ->values();
    }

    /**
     * Lista osób do wywołania przez @ (dropdown). @return Collection<int, array{id:int, label:string}>
     */
    private function mentionableUsers(): Collection
    {
        return User::where('active', true)
            ->orderByName()
            ->get(['id', 'first_name', 'last_name'])
            ->map(fn (User $user) => ['id' => $user->id, 'label' => trim($user->first_name.' '.$user->last_name)])
            ->values();
    }

    /**
     * Zapis przesłanych plików na dysku + rekordy ZgloszenieFile.
     *
     * @param  array<int, UploadedFile>|UploadedFile|null  $files
     */
    private function storeUploadedFiles($files, Zgloszenie $zgloszenie, ?int $noteId = null): void
    {
        if (empty($files)) {
            return;
        }

        foreach (is_array($files) ? $files : [$files] as $file) {
            if (! $file instanceof UploadedFile) {
                continue;
            }

            try {
                $originalName = $file->getClientOriginalName();
                $mime = $file->getClientMimeType();
                $size = $file->getSize();
                $extension = strtolower($file->getClientOriginalExtension() ?: (string) $file->guessExtension() ?: 'bin');

                $dir = 'zgloszenia/'.$zgloszenie->id;
                $filename = Str::random(40).'.'.$extension;
                $file->storeAs($dir, $filename);

                ZgloszenieFile::create([
                    'zgloszenie_id' => $zgloszenie->id,
                    'note_id' => $noteId,
                    'path' => $dir.'/'.$filename,
                    'original_name' => $originalName ?: $filename,
                    'mime' => $mime,
                    'size' => $size,
                    'uploaded_by' => Auth::id(),
                ]);
            } catch (\Throwable $e) {
                Log::error('Nie udało się zapisać załącznika zgłoszenia: '.$e->getMessage(), [
                    'zgloszenie_id' => $zgloszenie->id,
                ]);
            }
        }
    }

    private function deleteFile(ZgloszenieFile $file): void
    {
        try {
            Storage::disk('local')->delete($file->path);
        } catch (\Throwable $e) {
            Log::warning('Nie udało się usunąć pliku zgłoszenia: '.$e->getMessage(), ['file_id' => $file->id]);
        }

        $file->delete();
    }

    /** Zmiana statusu zostaje w wątku jako wpis systemowy — widać kto i kiedy. */
    private function logStatusChange(Zgloszenie $zgloszenie, ?string $previousStatus): void
    {
        $from = ZgloszenieStatus::tryFrom((string) $previousStatus)?->label() ?? $previousStatus;

        $zgloszenie->notes()->create([
            'user_id' => Auth::id(),
            'body' => 'Zmiana statusu: '.$from.' → '.$zgloszenie->statusLabel(),
            'system' => true,
        ]);
    }

    private function notifyAssignee(Zgloszenie $zgloszenie): void
    {
        try {
            $zgloszenie->assignee?->notify(new ZgloszenieAssignedNotification($zgloszenie, Auth::user()));
        } catch (\Throwable $e) {
            Log::warning('Nie udało się powiadomić o przypisaniu zgłoszenia: '.$e->getMessage(), [
                'zgloszenie_id' => $zgloszenie->id,
            ]);
        }
    }

    /**
     * Powiadamia osoby wywołane w komentarzu. Przy edycji podaj $previousBody —
     * powiadomimy tylko nowo wywołanych.
     */
    private function notifyMentioned(Note $note, ?string $previousBody = null): void
    {
        $ids = $this->extractMentionedIds($note->body);

        if ($previousBody !== null) {
            $ids = array_values(array_diff($ids, $this->extractMentionedIds($previousBody)));
        }

        $ids = array_values(array_filter($ids, fn (int $id) => $id !== (int) Auth::id()));

        if ($ids === []) {
            return;
        }

        try {
            $recipients = User::whereIn('id', $ids)->where('active', true)->get();

            if ($recipients->isEmpty()) {
                return;
            }

            Notification::send($recipients, new NoteMentionNotification($note, Auth::user()));
        } catch (\Throwable $e) {
            Log::warning('Nie udało się wysłać powiadomienia o wzmiance: '.$e->getMessage(), [
                'note_id' => $note->id,
            ]);
        }
    }

    /**
     * Wyciąga id użytkowników wywołanych w treści. Format: @[Imię Nazwisko](user:ID).
     *
     * @return int[]
     */
    private function extractMentionedIds(string $body): array
    {
        $ids = [];

        if (preg_match_all('/@\[[^\]]+\]\(user:(\d+)\)/u', $body, $matches)) {
            foreach ($matches[1] as $id) {
                $ids[] = (int) $id;
            }
        }

        return array_values(array_unique($ids));
    }

    private function isOffice(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'Administrator']);
    }
}
