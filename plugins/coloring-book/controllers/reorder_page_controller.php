<?php

if (user_can('manage_coloring_pages')) {
    $bookId = (int) URL(3);
    $direction = URL(4); // 'move-up' or 'move-down'
    $pageId = (int) URL(5);

    if (csrf_verify($req->post('_token')) && $pageId > 0) {
        $pages = new \ColoringBook\ColoringBookPages;
        $row = $pages->find($pageId);

        if ($row && (int) $row->coloring_book_id === $bookId) {
            $moved = $direction === 'move-up'
                ? $pages->moveUp($pageId, $bookId)
                : $pages->moveDown($pageId, $bookId);

            if (!$moved) {
                message('This page is already at that end of the list.', 'info');
            }
        } else {
            message('That page was not found in this coloring book.', 'fail');
        }
    } else {
        message('Invalid request.', 'fail');
    }

    redirect($admin_route . '/' . $plugin_route . '/pages/' . $bookId);
}
