<?php

if (user_can('create_coloring_books')) {
    $postdata = $req->post();

    if (csrf_verify($postdata['_token'] ?? null)) {
        $books = new \ColoringBook\ColoringBooks;

        $title = trim((string) ($postdata['title'] ?? ''));
        $slugInput = trim((string) ($postdata['slug'] ?? ''));
        $slug = $slugInput !== '' ? $books->makeSlug($slugInput) : $books->makeSlug($title);

        $data = [
            'title'        => $title,
            'slug'         => $slug,
            'description'  => trim((string) ($postdata['description'] ?? '')) ?: null,
            'cover_image'  => null,
            'status'       => in_array($postdata['status'] ?? '', ['draft', 'published'], true) ? $postdata['status'] : 'draft',
            'sort_order'   => (int) ($postdata['sort_order'] ?? 0),
            'date_created' => date('Y-m-d H:i:s'),
        ];

        // Cover image: a plain file upload on the same form, the same
        // pattern the Settings plugin uses for the site logo - there's no
        // separate reusable media-picker component in this codebase to
        // reuse instead. Uses the existing general-purpose raster uploader
        // (MIME-derived extension, never the client filename) since a
        // cover is a normal raster image, not something that needs the
        // SVG-specific sanitizer below.
        $coverWarning = '';
        if (!empty($_FILES['cover_image']['tmp_name'])) {
            $bookDir = rtrim(get_value()['storage_dir'], '/') . '/' . $slug;
            $result = $req->upload_files('cover_image', $bookDir);
            if (is_string($result)) {
                $data['cover_image'] = $result;
            } else {
                // message() overwrites its single session slot on every
                // call, so this is folded into whichever message below
                // actually gets sent rather than sent on its own here -
                // otherwise the success message right after would erase it
                // silently.
                $coverWarning = ' (cover image failed: ' . implode(' ', $result) . ')';
            }
        }

        if ($books->validate_insert($data)) {
            if ($books->create($data)) {
                message('Coloring book created successfully!' . $coverWarning, $coverWarning ? 'info' : 'success');
                redirect($admin_route . '/' . $plugin_route . '/pages/' . $books->insert_id);
            } else {
                message('Failed to create the coloring book.', 'fail');
            }
        } else {
            message(implode(' ', $books->errors), 'fail');
        }
    } else {
        message('Form expired! Please refresh the page.', 'fail');
    }
}
