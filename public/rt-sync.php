<?php
/**
 * Ultra-lightweight real-time sync endpoint.
 * NO Laravel framework boot = ~5ms per request instead of 500ms.
 * 
 * Reads/writes a simple JSON file for document state.
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-CSRF-TOKEN');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

// Parse request
$input = json_decode(file_get_contents('php://input'), true) ?: [];
$action = $input['action'] ?? '';
$slug   = $input['slug'] ?? '';
$userId = $input['user_id'] ?? 0;
$userName = $input['user_name'] ?? 'Unknown';

if (!$slug) { echo json_encode(['error' => 'no slug']); exit; }

// Storage directory
$dir = __DIR__ . '/../storage/app/rt-sync';
if (!is_dir($dir)) mkdir($dir, 0777, true);

$file = $dir . '/' . preg_replace('/[^a-z0-9\-]/', '', $slug) . '.json';

// ── PUSH: user sends their content ────────────────────────────────
if ($action === 'push') {
    $data = [
        'content'   => $input['content'] ?? '',
        'title'     => $input['title'] ?? '',
        'user_id'   => $userId,
        'user_name' => $userName,
        'timestamp' => microtime(true),
    ];
    file_put_contents($file, json_encode($data), LOCK_EX);
    
    // Update presence
    $presenceFile = $dir . '/' . preg_replace('/[^a-z0-9\-]/', '', $slug) . '_presence.json';
    $presence = file_exists($presenceFile) ? json_decode(file_get_contents($presenceFile), true) : [];
    $presence[$userId] = [
        'id'        => $userId,
        'name'      => $userName,
        'initial'   => strtoupper(substr($userName, 0, 1)),
        'is_typing' => true,
        'is_self'   => false,
        'timestamp' => microtime(true),
    ];
    file_put_contents($presenceFile, json_encode($presence), LOCK_EX);
    
    echo json_encode(['pushed' => true]);
    exit;
}

// ── PULL: user checks for updates ─────────────────────────────────
if ($action === 'pull') {
    $since = (float)($input['since'] ?? 0);
    
    $hasUpdate = false;
    $content = null;
    $title = null;
    $fromUser = null;
    $timestamp = $since;
    
    if (file_exists($file)) {
        $data = json_decode(file_get_contents($file), true);
        if ($data && $data['timestamp'] > $since && $data['user_id'] != $userId) {
            $hasUpdate = true;
            $content   = $data['content'];
            $title     = $data['title'];
            $fromUser  = $data['user_name'];
            $timestamp = $data['timestamp'];
        } elseif ($data) {
            $timestamp = $data['timestamp'];
        }
    }
    
    // Get presence (filter stale > 8s)
    $presenceFile = $dir . '/' . preg_replace('/[^a-z0-9\-]/', '', $slug) . '_presence.json';
    $presence = file_exists($presenceFile) ? json_decode(file_get_contents($presenceFile), true) : [];
    $now = microtime(true);
    $onlineUsers = [];
    foreach ($presence as $uid => $p) {
        if (($now - $p['timestamp']) < 8) {
            $p['is_self'] = ($uid == $userId);
            $p['is_typing'] = ($now - $p['timestamp']) < 3;
            $onlineUsers[] = $p;
        }
    }
    
    // Update own presence (mark as viewing)
    $presence[$userId] = [
        'id'        => $userId,
        'name'      => $userName,
        'initial'   => strtoupper(substr($userName, 0, 1)),
        'is_typing' => false,
        'is_self'   => false,
        'timestamp' => $now,
    ];
    file_put_contents($presenceFile, json_encode($presence), LOCK_EX);
    
    echo json_encode([
        'has_update'   => $hasUpdate,
        'content'      => $content,
        'title'        => $title,
        'from_user'    => $fromUser,
        'timestamp'    => $timestamp,
        'online_users' => $onlineUsers,
    ]);
    exit;
}

// ── LEAVE ─────────────────────────────────────────────────────────
if ($action === 'leave') {
    $presenceFile = $dir . '/' . preg_replace('/[^a-z0-9\-]/', '', $slug) . '_presence.json';
    if (file_exists($presenceFile)) {
        $presence = json_decode(file_get_contents($presenceFile), true) ?: [];
        unset($presence[$userId]);
        file_put_contents($presenceFile, json_encode($presence), LOCK_EX);
    }
    echo json_encode(['left' => true]);
    exit;
}

echo json_encode(['error' => 'unknown action']);
