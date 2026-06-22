<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

function json_body(): array {
    $raw = file_get_contents('php://input');
    $data = json_decode($raw ?: '{}', true);
    return is_array($data) ? $data : [];
}

function respond($payload, int $status = 200): void {
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

function require_admin_token(): void {
    $headers = function_exists('getallheaders') ? getallheaders() : [];
    $auth = $headers['Authorization'] ?? $headers['authorization'] ?? '';
    $token = preg_replace('/^Bearer\s+/i', '', $auth);

    if (empty($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        respond(['error' => 'Unauthorized'], 401);
    }

    if (empty($_SESSION['admin_api_token']) || !hash_equals((string) $_SESSION['admin_api_token'], (string) $token)) {
        respond(['error' => 'Invalid token'], 401);
    }
}

function table_columns(PDO $db, string $table): array {
    $stmt = $db->query("SHOW COLUMNS FROM `$table`");
    return array_map(fn($row) => $row['Field'], $stmt->fetchAll(PDO::FETCH_ASSOC));
}

function pick_payload(array $payload, array $allowed): array {
    $result = [];
    foreach ($allowed as $field) {
        if (array_key_exists($field, $payload)) {
            $result[$field] = is_bool($payload[$field]) ? (int) $payload[$field] : $payload[$field];
        }
    }
    return $result;
}

function upsert_single_row(PDO $db, string $table, array $payload): void {
    $columns = array_values(array_filter(table_columns($db, $table), fn($c) => !in_array($c, ['id', 'created_at', 'updated_at'], true)));
    $data = pick_payload($payload, $columns);

    $id = (int) ($db->query("SELECT id FROM `$table` ORDER BY id ASC LIMIT 1")->fetchColumn() ?: 0);
    if ($id > 0) {
        if (!$data) return;
        $sets = implode(', ', array_map(fn($key) => "`$key` = ?", array_keys($data)));
        $stmt = $db->prepare("UPDATE `$table` SET $sets WHERE id = ?");
        $stmt->execute([...array_values($data), $id]);
        return;
    }

    if (!$data) return;
    $fields = implode(', ', array_map(fn($key) => "`$key`", array_keys($data)));
    $marks = implode(', ', array_fill(0, count($data), '?'));
    $stmt = $db->prepare("INSERT INTO `$table` ($fields) VALUES ($marks)");
    $stmt->execute(array_values($data));
}

require_admin_token();

$request = trim((string) ($_GET['request'] ?? ''), '/');
$method = $_SERVER['REQUEST_METHOD'];
$parts = $request === '' ? [] : explode('/', $request);

try {
    if ($request === 'admin/link-hub/settings') {
        if ($method === 'GET') {
            $row = $db->query("SELECT * FROM link_hub_settings ORDER BY id ASC LIMIT 1")->fetch(PDO::FETCH_ASSOC) ?: [];
            respond($row);
        }
        if ($method === 'PUT' || $method === 'POST') {
            upsert_single_row($db, 'link_hub_settings', json_body());
            respond(['success' => true]);
        }
    }

    if ($request === 'admin/link-hub/seo') {
        if ($method === 'GET') {
            $row = $db->query("SELECT * FROM link_hub_seo ORDER BY id ASC LIMIT 1")->fetch(PDO::FETCH_ASSOC) ?: [];
            respond($row);
        }
        if ($method === 'PUT' || $method === 'POST') {
            upsert_single_row($db, 'link_hub_seo', json_body());
            respond(['success' => true]);
        }
    }

    if (($parts[0] ?? '') === 'admin' && ($parts[1] ?? '') === 'link-hub') {
        $resource = $parts[2] ?? '';
        $id = isset($parts[3]) && is_numeric($parts[3]) ? (int) $parts[3] : null;
        $action = $parts[4] ?? '';

        $tableMap = [
            'links' => 'link_hub_links',
            'social-links' => 'link_hub_social_links',
        ];
        if (!isset($tableMap[$resource])) {
            respond(['error' => 'Not found'], 404);
        }

        $table = $tableMap[$resource];
        $columns = table_columns($db, $table);
        $allowed = array_values(array_filter($columns, fn($c) => !in_array($c, ['id', 'created_at', 'updated_at', 'click_count'], true)));

        if ($method === 'GET') {
            $rows = $db->query("SELECT * FROM `$table` ORDER BY sort_order ASC, id ASC")->fetchAll(PDO::FETCH_ASSOC);
            respond($rows);
        }

        if ($method === 'POST') {
            $data = pick_payload(json_body(), $allowed);
            if (!$data) respond(['error' => 'Empty payload'], 400);
            $fields = implode(', ', array_map(fn($key) => "`$key`", array_keys($data)));
            $marks = implode(', ', array_fill(0, count($data), '?'));
            $stmt = $db->prepare("INSERT INTO `$table` ($fields) VALUES ($marks)");
            $stmt->execute(array_values($data));
            respond(['success' => true, 'id' => (int) $db->lastInsertId()]);
        }

        if (($method === 'PUT' || $method === 'PATCH') && $id && $action === 'toggle') {
            $field = (string) (json_body()['field'] ?? '');
            if (!in_array($field, ['is_active', 'is_featured'], true) || !in_array($field, $columns, true)) {
                respond(['error' => 'Invalid field'], 400);
            }
            $stmt = $db->prepare("UPDATE `$table` SET `$field` = IF(`$field` = 1, 0, 1) WHERE id = ?");
            $stmt->execute([$id]);
            respond(['success' => true]);
        }

        if (($method === 'PUT' || $method === 'PATCH') && !$id && $action === '') {
            $orders = json_body()['orders'] ?? [];
            if (!is_array($orders)) respond(['error' => 'Invalid orders'], 400);
            $stmt = $db->prepare("UPDATE `$table` SET sort_order = ? WHERE id = ?");
            foreach ($orders as $order) {
                $stmt->execute([(int) ($order['sort_order'] ?? 0), (int) ($order['id'] ?? 0)]);
            }
            respond(['success' => true]);
        }

        if (($method === 'PUT' || $method === 'PATCH') && $id) {
            $data = pick_payload(json_body(), $allowed);
            if (!$data) respond(['error' => 'Empty payload'], 400);
            $sets = implode(', ', array_map(fn($key) => "`$key` = ?", array_keys($data)));
            $stmt = $db->prepare("UPDATE `$table` SET $sets WHERE id = ?");
            $stmt->execute([...array_values($data), $id]);
            respond(['success' => true]);
        }

        if ($method === 'DELETE' && $id) {
            $stmt = $db->prepare("DELETE FROM `$table` WHERE id = ?");
            $stmt->execute([$id]);
            respond(['success' => true]);
        }
    }

    respond(['error' => 'Not found'], 404);
} catch (Throwable $e) {
    respond(['error' => 'Server error', 'details' => $e->getMessage()], 500);
}
