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
}
