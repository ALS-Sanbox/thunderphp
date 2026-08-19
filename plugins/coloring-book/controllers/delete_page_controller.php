<?php

if (user_can('manage_coloring_pages')) {
    $bookId = (int) URL(3);
    $pageId = (int) URL(5);

    if (csrf_verify($req->post('_token')) && $pageId > 0) {
        $pages = new \ColoringBook\ColoringBookPages;
        $row = $pages->find($pageId);

        if ($row && (int) $row->coloring_book_id === $bookId) {
            if ($pages->delete($pageId)) {
                if (!empty($row->svg_path)) $req->delete_file($row->svg_path);
                if (!empty($row->thumbnail_path)) $req->delete_file($row->thumbnail_path);
                message('Coloring page deleted.', 'success');
            } else {
                message('Failed to delete the coloring page.', 'fail');
            }
        } else {
            message('That page was not found in this coloring book.', 'fail');
        }
    } else {
        message('Invalid request.', 'fail');
    }

    redirect($admin_route . '/' . $plugin_route . '/pages/' . $bookId);
}
