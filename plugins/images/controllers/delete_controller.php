<?php

if (user_can('delete_image')) {
    $record_id = $req->post('image_id') ?: $id;

    if (csrf_verify($req->post('_token')) && !empty($record_id)) {
        $row = $images->find($record_id);

        if ($row) {
            if ($images->delete($record_id)) {
                $req->delete_file($row->path);
                message("Image deleted successfully!", "success");
            } else {
                message("Failed to delete image.", "fail");
            }
        } else {
            message("Image not found.", "fail");
        }
    } else {
        message("Invalid request.", "fail");
    }

    redirect($admin_route . '/' . $plugin_route);
}
