<?php
$uploadDir = plugin_path('uploads/');
$uploadUrl = plugin_http_path('uploads/'); // Converts to proper HTTP URL

if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$response = ['data' => []];

// Match 'file' from JS: formData.append('file', file)
if (!empty($_FILES['file']['name'])) {
    $file = $_FILES['file'];
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    if (!in_array(strtolower($ext), $allowed)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid file type']);
        exit;
    }

    $filename = uniqid('img_', true) . '.' . $ext;
    $targetPath = $uploadDir . $filename;

    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        $response['data'][] = $uploadUrl . $filename;
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to upload']);
        exit;
    }
}

header('Content-Type: application/json');
echo json_encode($response);
