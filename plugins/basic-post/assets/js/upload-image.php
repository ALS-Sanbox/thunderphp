<?php
$uploadDir = plugin_path('uploads/');
$uploadUrl = plugin_http_path('uploads/');

header('Content-Type: application/json');

if (!isset($_FILES['files'])) {
    echo json_encode(['data' => []]);
    exit;
}

$uploadedFiles = $_FILES['files'];
$results = [];

foreach ($uploadedFiles['tmp_name'] as $index => $tmpPath) {
    if ($tmpPath && is_uploaded_file($tmpPath)) {
        $originalName = basename($uploadedFiles['name'][$index]);
        $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9\._-]/', '_', $originalName);
        $targetPath = $uploadDir . $filename;

        if (move_uploaded_file($tmpPath, $targetPath)) {
            $results[] = [
                'src' => $uploadUrl . $filename,
                'name' => $originalName,
            ];
        }
    }
}

echo json_encode(['data' => $results]);