<?php

header('Content-Type: application/json');

if (!user_can('view_images')) {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden']);
    exit;
}

$rows = $images->query("SELECT id, path, original_name, filename FROM images ORDER BY id DESC LIMIT 200");

$result = [];
foreach ($rows as $row) {
    $result[] = [
        'id'   => $row->id,
        'url'  => ROOT . '/' . $row->path,
        'name' => $row->original_name ?: $row->filename,
    ];
}

echo json_encode($result);
exit;
