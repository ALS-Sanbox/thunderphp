<?php

if (user_can('add_image')) {
    if (csrf_verify($req->post('_token'))) {
        $filedata = $req->files('images');
        $uploadErrors = [];
        $uploadedCount = 0;

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
                    $uploadedCount++;
                } else {
                    $uploadErrors[] = "Failed to save record for $originalName.";
                }
            }
        } else {
            $uploadErrors[] = "No files were selected.";
        }

        if ($uploadedCount > 0 && empty($uploadErrors)) {
            message("$uploadedCount image(s) uploaded successfully!", "success");
        } elseif ($uploadedCount > 0) {
            message("$uploadedCount image(s) uploaded, but some failed: " . implode(' ', $uploadErrors), "info");
        } else {
            message("Upload failed: " . implode(' ', $uploadErrors), "fail");
        }
    } else {
        message("Form expired! Please refresh", "fail");
    }

    redirect($admin_route . '/' . $plugin_route);
}
