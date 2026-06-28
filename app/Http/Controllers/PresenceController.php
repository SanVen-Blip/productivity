<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PresenceController extends Controller
{
    /**
     * Heartbeat — user pings this every 2s while editor is open.
     * Returns: online users, who is typing, latest content (if changed by another user).
     */
    public function heartbeat(Request $request, string $slug)
    {
        $document = Document::where('slug', $slug)->firstOrFail();
        $user = Auth::user();

        // Verify access
        if ($document->user_id !== $user->id && $document->team_id) {
            $team = $document->team;
            if (!$team || !$team->hasMember($user)) {
                abort(403);
            }
        } elseif ($document->user_id !== $user->id && !$document->team_id) {
            abort(403);
        }

        // Update presence
        DB::table('document_presence')->updateOrInsert(
            ['document_id' => $document->id, 'user_id' => $user->id],
            [
                'is_typing'    => $request->boolean('is_typing'),
                'last_seen_at' => now(),
            ]
        );

        // Clean up stale presence (>15s no heartbeat = offline)
        DB::table('document_presence')
            ->where('document_id', $document->id)
            ->where('last_seen_at', '<', now()->subSeconds(15))
            ->delete();

        // Get online users
        $onlineUsers = DB::table('document_presence')
            ->join('users', 'users.id', '=', 'document_presence.user_id')
            ->where('document_presence.document_id', $document->id)
            ->select('users.id', 'users.name', 'users.email', 'document_presence.is_typing', 'document_presence.last_seen_at')
            ->get()
            ->map(fn($u) => [
                'id'        => $u->id,
                'name'      => $u->name,
                'initial'   => strtoupper(substr($u->name, 0, 1)),
                'is_typing' => (bool) $u->is_typing,
                'is_self'   => $u->id === $user->id,
            ]);

        // Check if document was updated by someone else
        $clientVersion = $request->input('last_saved_at');
        $needSync = false;
        $syncContent = null;
        $syncTitle = null;

        if ($clientVersion && $document->last_saved_at) {
            $clientTime = strtotime($clientVersion);
            $serverTime = $document->last_saved_at->timestamp;

            // If server version is newer, sync content to this client
            if ($serverTime > $clientTime) {
                $needSync = true;
                $syncContent = $document->content;
                $syncTitle = $document->title;
            }
        } elseif (!$clientVersion && $document->last_saved_at) {
            // Client has never saved but doc has been saved by someone else
            $needSync = true;
            $syncContent = $document->content;
            $syncTitle = $document->title;
        }

        return response()->json([
            'online_users' => $onlineUsers,
            'sync'         => $needSync ? [
                'title'         => $syncTitle,
                'content'       => $syncContent,
                'last_saved_at' => $document->last_saved_at->toIso8601String(),
            ] : null,
        ]);
    }

    /**
     * User leaves the document — remove presence.
     */
    public function leave(string $slug)
    {
        $document = Document::where('slug', $slug)->firstOrFail();

        DB::table('document_presence')
            ->where('document_id', $document->id)
            ->where('user_id', Auth::id())
            ->delete();

        return response()->json(['left' => true]);
    }
}
