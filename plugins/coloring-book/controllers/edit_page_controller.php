<?php

if (user_can('manage_coloring_pages')) {
    $bookId = (int) URL(3);
    $pageId = (int) URL(5);
    $postdata = $req->post();

    if (!csrf_verify($postdata['_token'] ?? null)) {
        message('Form expired! Please refresh the page.', 'fail');
        redirect($admin_route . '/' . $plugin_route . '/pages/' . $bookId . '/edit/' . $pageId);
    }

    $books = new \ColoringBook\ColoringBooks;
    $book = $books->find($bookId);
    if (!$book) {
        message('That coloring book was not found.', 'fail');
        redirect($admin_route . '/' . $plugin_route);
    }

    $pages = new \ColoringBook\ColoringBookPages;
    $existing = $pages->find($pageId);
    if (!$existing || (int) $existing->coloring_book_id !== $bookId) {
        message('That page was not found in this coloring book.', 'fail');
        redirect($admin_route . '/' . $plugin_route . '/pages/' . $bookId);
    }

    $slugInput = trim((string) ($postdata['slug'] ?? ''));
    $data = [
        'title'        => trim((string) ($postdata['title'] ?? '')),
        'slug'         => $slugInput !== '' ? $slugInput : $existing->slug,
        'status'       => in_array($postdata['status'] ?? '', ['draft', 'published'], true) ? $postdata['status'] : 'draft',
        'date_updated' => date('Y-m-d H:i:s'),
    ];

    if (!$pages->validate_update($data, $bookId, $pageId)) {
        message(implode(' ', $pages->errors), 'fail');
        redirect($admin_route . '/' . $plugin_route . '/pages/' . $bookId . '/edit/' . $pageId);
    }

    $bookDir = rtrim(get_value()['storage_dir'], '/') . '/' . $book->slug;

    // Replacing the SVG is optional on edit - only touched if a new file
    // was actually selected.
    if (!empty($_FILES['svg_file']['tmp_name']) && is_uploaded_file($_FILES['svg_file']['tmp_name'])) {
        $rawSvg = file_get_contents($_FILES['svg_file']['tmp_name']);
        $sanitizer = new \ColoringBook\SvgSanitizer();
        $sanitizedSvg = $rawSvg !== false ? $sanitizer->sanitize($rawSvg) : null;

        if ($sanitizedSvg === null) {
            $reason = $rawSvg === false ? 'The uploaded file could not be read.' : implode(' ', $sanitizer->errors);
            message('The new SVG could not be used, the existing page picture was kept: ' . $reason, 'fail');
            redirect($admin_route . '/' . $plugin_route . '/pages/' . $bookId . '/edit/' . $pageId);
        }

        if (!is_dir($bookDir)) {
            mkdir($bookDir, 0777, true);
        }

        $newSvgPath = $bookDir . '/' . $data['slug'] . '.svg';
        $counter = 1;
        while (file_exists($newSvgPath) && $newSvgPath !== $existing->svg_path) {
            $newSvgPath = $bookDir . '/' . $data['slug'] . '-' . $counter . '.svg';
            $counter++;
        }

        if (file_put_contents($newSvgPath, $sanitizedSvg) !== false) {
            if (!empty($existing->svg_path) && $existing->svg_path !== $newSvgPath) {
                $req->delete_file($existing->svg_path);
            }
            $data['svg_path'] = $newSvgPath;
        }
    }

    if (!empty($_FILES['thumbnail']['tmp_name'])) {
        $result = $req->upload_files('thumbnail', $bookDir);
        if (is_string($result)) {
            if (!empty($existing->thumbnail_path)) {
                $req->delete_file($existing->thumbnail_path);
            }
            $data['thumbnail_path'] = $result;
        } elseif (is_array($result)) {
            $thumbnailWarning = ' (thumbnail failed: ' . implode(' ', $result) . ')';
        }
    }

    if ($pages->update($pageId, $data)) {
        message('Coloring page updated successfully!' . ($thumbnailWarning ?? ''), isset($thumbnailWarning) ? 'info' : 'success');
    } else {
        message('No changes were saved.' . ($thumbnailWarning ?? ''), 'info');
    }

    redirect($admin_route . '/' . $plugin_route . '/pages/' . $bookId);
}
