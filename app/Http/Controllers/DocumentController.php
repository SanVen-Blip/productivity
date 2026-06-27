<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $user  = Auth::user();
        $query = $user->documents();

        // Search
        if ($search = $request->query('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Filter by folder
        if ($folder = $request->query('folder')) {
            $query->where('folder', $folder);
        }

        // Filter starred
        if ($request->query('starred')) {
            $query->where('starred', true);
        }

        // Sort
        $sort = $request->query('sort', 'updated_at');
        $dir  = $request->query('dir', 'desc');
        $allowedSorts = ['updated_at', 'created_at', 'title'];
        $sort = in_array($sort, $allowedSorts) ? $sort : 'updated_at';
        $dir  = $dir === 'asc' ? 'asc' : 'desc';

        // Starred always on top (unless filtering starred)
        if (!$request->query('starred')) {
            $query->orderBy('starred', 'desc');
        }
        $query->orderBy($sort, $dir);

        $documents = $query->get();

        // Folders list for sidebar
        $folders = $user->documents()
            ->whereNotNull('folder')
            ->distinct()
            ->pluck('folder')
            ->sort()
            ->values();

        // Stats
        $stats = [
            'total'   => $user->documents()->count(),
            'starred' => $user->documents()->where('starred', true)->count(),
            'folders' => $folders->count(),
        ];

        return view('dashboard', compact('documents', 'folders', 'stats'));
    }

    public function store(Request $request)
    {
        $document = Auth::user()->documents()->create([
            'title'   => 'Untitled Document',
            'content' => '',
            'status'  => 'draft',
            'folder'  => $request->query('folder'),
        ]);

        return redirect()->route('documents.edit', $document->slug);
    }

    public function edit(string $slug)
    {
        $document = Auth::user()
            ->documents()
            ->where('slug', $slug)
            ->firstOrFail();

        return view('editor', compact('document'));
    }

    public function update(Request $request, string $slug)
    {
        $document = Auth::user()
            ->documents()
            ->where('slug', $slug)
            ->firstOrFail();

        $validated = $request->validate([
            'title'   => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
        ]);

        $document->update([
            'title'         => $validated['title'],
            'content'       => $validated['content'],
            'last_saved_at' => now(),
        ]);

        // Save version snapshot
        $document->saveVersion();

        return response()->json([
            'saved'         => true,
            'last_saved_at' => $document->fresh()->last_saved_at->diffForHumans(),
            'title'         => $document->title,
        ]);
    }

    public function rename(Request $request, string $slug)
    {
        $document = Auth::user()
            ->documents()
            ->where('slug', $slug)
            ->firstOrFail();

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
        ]);

        $document->update(['title' => $validated['title']]);

        return response()->json(['title' => $document->title]);
    }

    public function toggleStar(string $slug)
    {
        $document = Auth::user()
            ->documents()
            ->where('slug', $slug)
            ->firstOrFail();

        $document->update(['starred' => !$document->starred]);

        return response()->json(['starred' => $document->starred]);
    }

    public function moveFolder(Request $request, string $slug)
    {
        $document = Auth::user()
            ->documents()
            ->where('slug', $slug)
            ->firstOrFail();

        $validated = $request->validate([
            'folder' => ['nullable', 'string', 'max:100'],
        ]);

        $document->update(['folder' => $validated['folder'] ?: null]);

        return response()->json(['folder' => $document->folder]);
    }

    public function duplicate(string $slug)
    {
        $original = Auth::user()
            ->documents()
            ->where('slug', $slug)
            ->firstOrFail();

        $copy = Auth::user()->documents()->create([
            'title'   => $original->title . ' (Copy)',
            'content' => $original->content,
            'status'  => 'draft',
            'folder'  => $original->folder,
        ]);

        return redirect()->route('documents.edit', $copy->slug)
            ->with('success', 'Document duplicated.');
    }

    public function destroy(string $slug)
    {
        $document = Auth::user()
            ->documents()
            ->where('slug', $slug)
            ->firstOrFail();

        $document->delete();

        return redirect()->route('dashboard')->with('success', 'Document deleted.');
    }

    public function export(string $slug)
    {
        $document = Auth::user()
            ->documents()
            ->where('slug', $slug)
            ->firstOrFail();

        return view('export', compact('document'));
    }

    // ── Version history ────────────────────────────────────────────
    public function versions(string $slug)
    {
        $document = Auth::user()
            ->documents()
            ->where('slug', $slug)
            ->firstOrFail();

        $versions = $document->versions()
            ->select('id', 'version_number', 'title', 'created_at')
            ->get()
            ->map(fn ($v) => [
                'id'             => $v->id,
                'version_number' => $v->version_number,
                'title'          => $v->title,
                'created_at'     => $v->created_at->diffForHumans(),
                'created_at_full'=> $v->created_at->format('M j, Y g:i A'),
            ]);

        return response()->json($versions);
    }

    public function restoreVersion(string $slug, int $versionId)
    {
        $document = Auth::user()
            ->documents()
            ->where('slug', $slug)
            ->firstOrFail();

        $version = $document->versions()->findOrFail($versionId);

        // Save current as a version before overwriting
        $document->saveVersion();

        $document->update([
            'title'         => $version->title,
            'content'       => $version->content,
            'last_saved_at' => now(),
        ]);

        return response()->json([
            'restored' => true,
            'title'    => $document->title,
            'content'  => $document->content,
        ]);
    }

    // ── Image upload ───────────────────────────────────────────────
    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => ['required', 'image', 'max:5120'], // 5MB
        ]);

        $path = $request->file('image')->store('editor-images', 'public');
        $url  = asset('storage/' . $path);

        return response()->json(['url' => $url]);
    }

    // ── Public share ───────────────────────────────────────────────
    public function togglePublic(string $slug)
    {
        $document = Auth::user()
            ->documents()
            ->where('slug', $slug)
            ->firstOrFail();

        if ($document->is_public) {
            $document->update(['is_public' => false, 'share_token' => null]);
            return response()->json(['is_public' => false, 'share_url' => null]);
        }

        $token = \Illuminate\Support\Str::random(32);
        $document->update(['is_public' => true, 'share_token' => $token]);

        return response()->json([
            'is_public' => true,
            'share_url' => route('documents.public', $token),
        ]);
    }

    public function publicView(string $token)
    {
        $document = Document::where('share_token', $token)
            ->where('is_public', true)
            ->firstOrFail();

        return view('public-doc', compact('document'));
    }

    // ── Tags ───────────────────────────────────────────────────────
    public function updateTags(Request $request, string $slug)
    {
        $document = Auth::user()
            ->documents()
            ->where('slug', $slug)
            ->firstOrFail();

        $validated = $request->validate([
            'tags'   => ['nullable', 'array', 'max:10'],
            'tags.*' => ['string', 'max:30'],
        ]);

        $document->update(['tags' => $validated['tags'] ?? []]);

        return response()->json(['tags' => $document->tags]);
    }

    // ── Templates ─────────────────────────────────────────────────
    public function storeFromTemplate(Request $request)
    {
        $template = $request->input('template', 'blank');

        $templates = [
            'blank' => ['title' => 'Untitled Document', 'content' => ''],
            'meeting' => [
                'title'   => 'Meeting Notes',
                'content' => '<h1>Meeting Notes</h1><p><strong>Date:</strong> ' . now()->format('F j, Y') . '</p><p><strong>Attendees:</strong> </p><p><strong>Agenda:</strong></p><ul><li>Item 1</li><li>Item 2</li></ul><h2>Discussion</h2><p></p><h2>Action Items</h2><ul><li>[ ] Task 1 — Owner</li><li>[ ] Task 2 — Owner</li></ul><h2>Next Meeting</h2><p></p>',
            ],
            'todo' => [
                'title'   => 'To-Do List',
                'content' => '<h1>To-Do List</h1><p><strong>Date:</strong> ' . now()->format('F j, Y') . '</p><h2>🔴 High Priority</h2><ul><li>[ ] Task</li></ul><h2>🟡 Medium Priority</h2><ul><li>[ ] Task</li></ul><h2>🟢 Low Priority</h2><ul><li>[ ] Task</li></ul>',
            ],
            'project' => [
                'title'   => 'Project Brief',
                'content' => '<h1>Project Brief</h1><h2>Overview</h2><p>Describe the project in 2-3 sentences.</p><h2>Goals</h2><ul><li>Goal 1</li><li>Goal 2</li></ul><h2>Scope</h2><p>What is in scope? What is out of scope?</p><h2>Timeline</h2><p><strong>Start:</strong> &nbsp; &nbsp;<strong>End:</strong></p><h2>Team</h2><ul><li>Role — Name</li></ul><h2>Success Metrics</h2><ul><li>Metric 1</li></ul>',
            ],
            'notes' => [
                'title'   => 'Quick Notes',
                'content' => '<h1>Quick Notes</h1><p>' . now()->format('F j, Y') . '</p><p></p>',
            ],
        ];

        $t = $templates[$template] ?? $templates['blank'];

        $document = Auth::user()->documents()->create([
            'title'   => $t['title'],
            'content' => $t['content'],
            'status'  => 'draft',
            'folder'  => $request->query('folder'),
        ]);

        return redirect()->route('documents.edit', $document->slug);
    }
}
