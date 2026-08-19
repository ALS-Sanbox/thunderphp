<?php

if (user_can('edit_coloring_books')) {
    $id = (int) (URL(3) ?? 0);
    $postdata = $req->post();

    if (csrf_verify($postdata['_token'] ?? null)) {
        $books = new \ColoringBook\ColoringBooks;
        $existing = $books->find($id);

        if (!$existing) {
            message('That coloring book was not found.', 'fail');
            redirect($admin_route . '/' . $plugin_route);
        }

        $slugInput = trim((string) ($postdata['slug'] ?? ''));
        $data = [
            'title'        => trim((string) ($postdata['title'] ?? '')),
            'slug'         => $slugInput !== '' ? $slugInput : $existing->slug,
            'description'  => trim((string) ($postdata['description'] ?? '')) ?: null,
            'status'       => in_array($postdata['status'] ?? '', ['draft', 'published'], true) ? $postdata['status'] : 'draft',
            'sort_order'   => (int) ($postdata['sort_order'] ?? 0),
            'date_updated' => date('Y-m-d H:i:s'),
        ];

        // Same file-upload cover pattern as add_book_controller.php - only
        // touched if a new file was actually chosen, otherwise the
        // existing cover_image stays as-is (update() only writes columns
        // present in $data, so simply omitting the key here is enough).
        $coverWarning = '';
        if (!empty($_FILES['cover_image']['tmp_name'])) {
            $bookDir = rtrim(get_value()['storage_dir'], '/') . '/' . $data['slug'];
            $result = $req->upload_files('cover_image', $bookDir);
            if (is_string($result)) {
                if (!empty($existing->cover_image)) {
                    $req->delete_file($existing->cover_image);
                }
                $data['cover_image'] = $result;
            } else {
                $coverWarning = ' (cover image failed: ' . implode(' ', $result) . ')';
            }
        }

        if ($books->validate_update($data, $id)) {
            if ($books->update($id, $data)) {
                message('Coloring book updated successfully!' . $coverWarning, $coverWarning ? 'info' : 'success');
            } else {
                message('No changes were saved.' . $coverWarning, $coverWarning ? 'info' : 'info');
            }
            redirect($admin_route . '/' . $plugin_route);
        } else {
            message(implode(' ', $books->errors), 'fail');
        }
    } else {
        message('Form expired! Please refresh the page.', 'fail');
    }
}
