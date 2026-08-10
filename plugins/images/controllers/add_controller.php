<?php

if (user_can('add_image')) {
    $isAjax = $req->post('ajax') == '1';

    if (csrf_verify($req->post('_token'))) {
        $filedata = $req->files('images');
        $uploadErrors = [];
        $uploadedImages = [];

        if (!empty($filedata['name']) && is_array($filedata['name'])) {
            foreach ($filedata['name'] as $index => $originalName) {
                if (empty($originalName)) continue;

                $_FILES['images'] = [
                    'name'     => $originalName,
                    'tmp_name' => $filedata['tmp_name'][$index],
                    'size'     => $filedata['size'][$index],
                    'type'     => $filedata['type'][$index],
                    'error'    => $filedata['error'][$index],
                ];

                $result = $req->upload_files('images');

                if (is_array($result)) {
                    $uploadErrors = array_merge($uploadErrors, $result);
                    continue;
                }

                $saved = $images->insert([
                    'filename'      => basename($result),
                    'path'          => $result,
                    'original_name' => $originalName,
                    'size'          => (int) $filedata['size'][$index],
                    'user_id'       => $user_id,
                    'date_created'  => date('Y-m-d H:i:s'),
                ]);

                if ($saved) {
                    $uploadedImages[] = [
                        'id'   => $images->insert_id,
                        'url'  => ROOT . '/' . $result,
                        'name' => $originalName,
                    ];
                } else {
                    $uploadErrors[] = "Failed to save record for $originalName.";
                }
            }
        } else {
            $uploadErrors[] = "No files were selected.";
        }

        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => !empty($uploadedImages),
                'images'  => $uploadedImages,
                'errors'  => $uploadErrors,
            ]);
            exit;
        }

        $uploadedCount = count($uploadedImages);

        if ($uploadedCount > 0 && empty($uploadErrors)) {
            message("$uploadedCount image(s) uploaded successfully!", "success");
        } elseif ($uploadedCount > 0) {
            message("$uploadedCount image(s) uploaded, but some failed: " . implode(' ', $uploadErrors), "info");
        } else {
            message("Upload failed: " . implode(' ', $uploadErrors), "fail");
        }
    } else {
        if ($isAjax) {
            header('Content-Type: application/json');
            http_response_code(419);
            echo json_encode(['success' => false, 'images' => [], 'errors' => ['Form expired! Please refresh the page.']]);
            exit;
        }
        message("Form expired! Please refresh", "fail");
    }

    redirect($admin_route . '/' . $plugin_route);
}
