<?php

if (user_can('manage_coloring_pages')) {
    $postdata = $req->post();
    $bookId = (int) URL(3);

    if (!csrf_verify($postdata['_token'] ?? null)) {
        message('Form expired! Please refresh the page.', 'fail');
        redirect($admin_route . '/' . $plugin_route . '/pages/' . $bookId . '/add');
    }

    $books = new \ColoringBook\ColoringBooks;
    $book = $books->find($bookId);

    if (!$book) {
        message('That coloring book was not found.', 'fail');
        redirect($admin_route . '/' . $plugin_route);
    }

    $pages = new \ColoringBook\ColoringBookPages;

    $title = trim((string) ($postdata['title'] ?? ''));
    $slugInput = trim((string) ($postdata['slug'] ?? ''));
    $slug = $slugInput !== '' ? $pages->makeSlugForBook($slugInput, $bookId) : $pages->makeSlugForBook($title, $bookId);

    $data = [
        'coloring_book_id' => $bookId,
        'title'            => $title,
        'slug'             => $slug,
        'status'           => in_array($postdata['status'] ?? '', ['draft', 'published'], true) ? $postdata['status'] : 'draft',
        'sort_order'       => $pages->nextSortOrder($bookId),
        'date_created'     => date('Y-m-d H:i:s'),
    ];

    if (!$pages->validate_insert($data)) {
        message(implode(' ', $pages->errors), 'fail');
        redirect($admin_route . '/' . $plugin_route . '/pages/' . $bookId . '/add');
    }

    // --- SVG upload: deliberately not $req->upload_files() (that method's
    // allowlist is raster-image-only and stays that way - see
    // wiki/Coloring-Book-Plugin.md for why SVG needs its own path). ---
    $svgFile = $_FILES['svg_file'] ?? null;
    if (empty($svgFile) || empty($svgFile['tmp_name']) || !is_uploaded_file($svgFile['tmp_name'])) {
        message('A coloring page SVG file is required.', 'fail');
        redirect($admin_route . '/' . $plugin_route . '/pages/' . $bookId . '/add');
    }

    $rawSvg = file_get_contents($svgFile['tmp_name']);
    if ($rawSvg === false) {
        message('The uploaded SVG file could not be read.', 'fail');
        redirect($admin_route . '/' . $plugin_route . '/pages/' . $bookId . '/add');
    }

    $sanitizer = new \ColoringBook\SvgSanitizer();
    $sanitizedSvg = $sanitizer->sanitize($rawSvg);

    if ($sanitizedSvg === null) {
        message('That SVG could not be used: ' . implode(' ', $sanitizer->errors), 'fail');
        redirect($admin_route . '/' . $plugin_route . '/pages/' . $bookId . '/add');
    }

    $bookDir = rtrim(get_value()['storage_dir'], '/') . '/' . $book->slug;
    if (!is_dir($bookDir) && !mkdir($bookDir, 0777, true) && !is_dir($bookDir)) {
        message('Could not create storage directory for this coloring book.', 'fail');
        redirect($admin_route . '/' . $plugin_route . '/pages/' . $bookId . '/add');
    }

    // The stored filename is built solely from the already-validated
    // (^[a-z0-9-]+$) page slug - never the client-supplied filename - so
    // there's no path-traversal surface here at all.
    $svgPath = $bookDir . '/' . $slug . '.svg';
    $counter = 1;
    while (file_exists($svgPath)) {
        $svgPath = $bookDir . '/' . $slug . '-' . $counter . '.svg';
        $counter++;
    }

    if (file_put_contents($svgPath, $sanitizedSvg) === false) {
        message('Failed to save the sanitized SVG file.', 'fail');
        redirect($admin_route . '/' . $plugin_route . '/pages/' . $bookId . '/add');
    }

    $data['svg_path'] = $svgPath;

    // --- Optional thumbnail: a plain raster image, so this reuses the
    // existing, already-hardened raster uploader (MIME-derived extension,
    // never the client filename) rather than duplicating that logic - just
    // pointed at this book's own storage directory instead of the shared
    // uploads/ root. A failed thumbnail upload isn't fatal for the page
    // itself (the admin can add one later via edit) - just folded into
    // whichever message ends up being sent below, since message()
    // overwrites its single session slot on every call. ---
    $thumbnailWarning = '';
    if (!empty($_FILES['thumbnail']['tmp_name'])) {
        $result = $req->upload_files('thumbnail', $bookDir);
        if (is_string($result)) {
            $data['thumbnail_path'] = $result;
        } else {
            $thumbnailWarning = ' (thumbnail failed: ' . implode(' ', $result) . ')';
        }
    }

    if ($pages->create($data)) {
        message('Coloring page added successfully!' . $thumbnailWarning, $thumbnailWarning ? 'info' : 'success');
    } else {
        // Roll back the file we just wrote - don't leave an orphaned SVG
        // with no database record pointing at it.
        $req->delete_file($svgPath);
        message('Failed to save the coloring page record.', 'fail');
    }

    redirect($admin_route . '/' . $plugin_route . '/pages/' . $bookId);
}
