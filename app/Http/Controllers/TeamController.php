<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeamController extends Controller
{
    // ── List user's teams ──────────────────────────────────────────
    public function index()
    {
        $teams = Auth::user()->teams()->withCount('members', 'documents')->get();
        return view('teams.index', compact('teams'));
    }

    // ── Create team ────────────────────────────────────────────────
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
        ]);

        $team = Team::create([
            'name'     => $validated['name'],
            'owner_id' => Auth::id(),
        ]);

        // Add owner as member with 'owner' role
        $team->members()->attach(Auth::id(), ['role' => 'owner']);

        return redirect()->route('teams.show', $team->slug)
            ->with('success', 'Team "' . $team->name . '" created!');
    }

    // ── Team dashboard ─────────────────────────────────────────────
    public function show(Request $request, string $slug)
    {
        $team = Team::where('slug', $slug)->firstOrFail();

        // Verify membership
        if (!$team->hasMember(Auth::user())) {
            abort(403, 'You are not a member of this team.');
        }

        $query = $team->documents()->latest();

        if ($search = $request->query('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $documents = $query->get();
        $members   = $team->members()->get();
        $role      = $team->getMemberRole(Auth::user());

        return view('teams.show', compact('team', 'documents', 'members', 'role'));
    }

    // ── Team settings ──────────────────────────────────────────────
    public function settings(string $slug)
    {
        $team = Team::where('slug', $slug)->firstOrFail();

        if (!$team->isOwner(Auth::user())) {
            abort(403, 'Only the team owner can manage settings.');
        }

        $members = $team->members()->get();

        return view('teams.settings', compact('team', 'members'));
    }

    // ── Update team name ───────────────────────────────────────────
    public function update(Request $request, string $slug)
    {
        $team = Team::where('slug', $slug)->firstOrFail();

        if (!$team->isOwner(Auth::user())) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
        ]);

        $team->update(['name' => $validated['name']]);

        return back()->with('success', 'Team name updated.');
    }

    // ── Invite member ──────────────────────────────────────────────
    public function invite(Request $request, string $slug)
    {
        $team = Team::where('slug', $slug)->firstOrFail();

        if (!$team->isOwner(Auth::user())) {
            abort(403);
        }

        $validated = $request->validate([
            'email' => ['required', 'email'],
            'role'  => ['required', 'in:editor,viewer'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user) {
            return back()->withErrors(['email' => 'No user found with that email. They must register first.']);
        }

        if ($team->hasMember($user)) {
            return back()->withErrors(['email' => 'This user is already a team member.']);
        }

        $team->members()->attach($user->id, ['role' => $validated['role']]);

        return back()->with('success', $user->name . ' has been added as ' . $validated['role'] . '.');
    }

    // ── Change member role ─────────────────────────────────────────
    public function updateRole(Request $request, string $slug, int $userId)
    {
        $team = Team::where('slug', $slug)->firstOrFail();

        if (!$team->isOwner(Auth::user())) {
            abort(403);
        }

        if ($userId === Auth::id()) {
            return back()->withErrors(['role' => 'You cannot change your own role.']);
        }

        $validated = $request->validate([
            'role' => ['required', 'in:editor,viewer'],
        ]);

        $team->members()->updateExistingPivot($userId, ['role' => $validated['role']]);

        return back()->with('success', 'Member role updated.');
    }

    // ── Remove member ──────────────────────────────────────────────
    public function removeMember(string $slug, int $userId)
    {
        $team = Team::where('slug', $slug)->firstOrFail();

        if (!$team->isOwner(Auth::user())) {
            abort(403);
        }

        if ($userId === Auth::id()) {
            return back()->withErrors(['member' => 'You cannot remove yourself. Transfer ownership first.']);
        }

        $team->members()->detach($userId);

        return back()->with('success', 'Member removed from team.');
    }

    // ── Leave team ─────────────────────────────────────────────────
    public function leave(string $slug)
    {
        $team = Team::where('slug', $slug)->firstOrFail();

        if ($team->isOwner(Auth::user())) {
            return back()->withErrors(['leave' => 'Owners cannot leave. Delete the team or transfer ownership.']);
        }

        $team->members()->detach(Auth::id());

        return redirect()->route('teams.index')->with('success', 'You left "' . $team->name . '".');
    }

    // ── Delete team ────────────────────────────────────────────────
    public function destroy(string $slug)
    {
        $team = Team::where('slug', $slug)->firstOrFail();

        if (!$team->isOwner(Auth::user())) {
            abort(403);
        }

        // Remove team_id from documents (move them to personal)
        $team->documents()->update(['team_id' => null]);
        $team->delete();

        return redirect()->route('teams.index')->with('success', 'Team deleted. Documents moved to personal workspace.');
    }

    // ── Create team document ───────────────────────────────────────
    public function createDocument(Request $request, string $slug)
    {
        $team = Team::where('slug', $slug)->firstOrFail();

        if (!$team->canEdit(Auth::user())) {
            abort(403, 'You do not have edit access in this team.');
        }

        $document = Auth::user()->documents()->create([
            'team_id' => $team->id,
            'title'   => 'Untitled Document',
            'content' => '',
            'status'  => 'draft',
        ]);

        return redirect()->route('documents.edit', $document->slug);
    }

    // ── Move existing doc to team ──────────────────────────────────
    public function assignDocument(Request $request, string $slug)
    {
        $team = Team::where('slug', $slug)->firstOrFail();

        if (!$team->canEdit(Auth::user())) {
            abort(403);
        }

        $validated = $request->validate([
            'document_slug' => ['required', 'string'],
        ]);

        $doc = Auth::user()->documents()
            ->where('slug', $validated['document_slug'])
            ->firstOrFail();

        $doc->update(['team_id' => $team->id]);

        return response()->json(['moved' => true, 'team' => $team->name]);
    }
}
