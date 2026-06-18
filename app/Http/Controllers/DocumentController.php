<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DocumentController extends Controller
{
    public function index()
    {
        $documents = Auth::user()
            ->documents()
            ->latest()
            ->get();

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
            'last_saved_at' => $document->last_saved_at->diffForHumans(),
        ]);
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
}
