<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $query = Auth::user()->documents()->latest();

        // Search / filter
        if ($search = $request->query('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $documents = $query->get();

        return view('dashboard', compact('documents'));
    }

    public function store(Request $request)
    {
        $document = Auth::user()->documents()->create([
            'title'   => 'Untitled Document',
            'content' => '',
            'status'  => 'draft',
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
}
