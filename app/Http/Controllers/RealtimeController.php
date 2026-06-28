<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class RealtimeController extends Controller
{
    /**
     * Push content changes instantly via a shared cache layer.
     * Called every time a user types (debounced 300ms on client).
     */
    public function push(Request $request, string $slug)
    {
        $user = Auth::user();
        $document = Document::where('slug', $slug)->firstOrFail();

        // Store the latest edit in cache (expires in 30s)
        $key = "doc_realtime:{$document->id}";
        Cache::put($key, [
            'content'    => $request->input('content'),
            'title'      => $request->input('title'),
            'user_id'    => $user->id,
            'user_name'  => $user->name,
            'timestamp'  => microtime(true),
        ], 30);

        // Update typing status
        $typingKey = "doc_typing:{$document->id}";
        $typing = Cache::get($typingKey, []);
        $typing[$user->id] = [
            'name'      => $user->name,
            'initial'   => strtoupper(substr($user->name, 0, 1)),
            'is_typing' => true,
            'timestamp' => microtime(true),
        ];
        Cache::put($typingKey, $typing, 30);

        return response()->json(['pushed' => true]);
    }

    /**
     * Poll for latest changes — ultra fast, just reads cache.
     * Called every 300ms from client.
     */
    public function pull(Request $request, string $slug)
    {
        $user = Auth::user();
        $document = Document::where('slug', $slug)->firstOrFail();

        $key = "doc_realtime:{$document->id}";
        $data = Cache::get($key);

        $clientTimestamp = (float) $request->input('since', 0);

        // Check if there's a newer version from another user
        $hasUpdate = false;
        $content = null;
        $title = null;
        $fromUser = null;

        if ($data && $data['user_id'] !== $user->id && $data['timestamp'] > $clientTimestamp) {
            $hasUpdate = true;
            $content = $data['content'];
            $title = $data['title'];
            $fromUser = $data['user_name'];
        }

        // Get typing users (filter stale > 3s)
        $typingKey = "doc_typing:{$document->id}";
        $typing = Cache::get($typingKey, []);
        $now = microtime(true);
        $activeTyping = [];
        foreach ($typing as $uid => $info) {
            if (($now - $info['timestamp']) < 3 && $uid != $user->id) {
                $activeTyping[] = $info;
            }
        }

        // Get online presence
        $presenceKey = "doc_presence:{$document->id}";
        $presence = Cache::get($presenceKey, []);
        $presence[$user->id] = [
            'id'        => $user->id,
            'name'      => $user->name,
            'initial'   => strtoupper(substr($user->name, 0, 1)),
            'timestamp' => $now,
        ];
        // Clean stale (>10s)
        $presence = array_filter($presence, fn($p) => ($now - $p['timestamp']) < 10);
        Cache::put($presenceKey, $presence, 30);

        $onlineUsers = array_values(array_map(function ($p) use ($user, $activeTyping) {
            $isTyping = false;
            foreach ($activeTyping as $t) {
                if ($t['name'] === $p['name']) $isTyping = true;
            }
            return [
                'id'        => $p['id'],
                'name'      => $p['name'],
                'initial'   => $p['initial'],
                'is_typing' => $isTyping,
                'is_self'   => $p['id'] === $user->id,
            ];
        }, $presence));

        return response()->json([
            'has_update'   => $hasUpdate,
            'content'      => $content,
            'title'        => $title,
            'from_user'    => $fromUser,
            'timestamp'    => $data['timestamp'] ?? $clientTimestamp,
            'online_users' => $onlineUsers,
            'typing'       => $activeTyping,
        ]);
    }

    /**
     * User leaves the document.
     */
    public function leave(Request $request, string $slug)
    {
        $user = Auth::user();
        $document = Document::where('slug', $slug)->firstOrFail();

        $presenceKey = "doc_presence:{$document->id}";
        $presence = Cache::get($presenceKey, []);
        unset($presence[$user->id]);
        Cache::put($presenceKey, $presence, 30);

        $typingKey = "doc_typing:{$document->id}";
        $typing = Cache::get($typingKey, []);
        unset($typing[$user->id]);
        Cache::put($typingKey, $typing, 30);

        return response()->json(['left' => true]);
    }
}
