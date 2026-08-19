<?php

if (user_can('delete_coloring_books')) {
    $id = (int) (URL(3) ?? 0);

    if (csrf_verify($req->post('_token')) && $id > 0) {
        $books = new \ColoringBook\ColoringBooks;
        $pages = new \ColoringBook\ColoringBookPages;

        $book = $books->find($id);

        if ($book) {
            // No SQL foreign key / cascade on coloring_book_pages (see the
            // migration's own comment - this codebase manages relationships
            // at the application level), so the book's pages - and their
            // files - are removed explicitly here before the book itself.
            $childPages = $pages->findForBook($id);
            foreach ($childPages as $page) {
                if (!empty($page->svg_path)) $req->delete_file($page->svg_path);
                if (!empty($page->thumbnail_path)) $req->delete_file($page->thumbnail_path);
                $pages->delete($page->id);
            }
            if (!empty($book->cover_image)) {
                $req->delete_file($book->cover_image);
            }

            if ($books->delete($id)) {
                // Clean up the now-empty per-book directory too, if
                // everything in it (cover image included) is gone.
                $bookDir = rtrim(get_value()['storage_dir'], '/') . '/' . $book->slug;
                if (is_dir($bookDir) && count(scandir($bookDir)) === 2) {
                    rmdir($bookDir);
                }
                message('Coloring book and its pages were deleted.', 'success');
            } else {
                message('Failed to delete the coloring book.', 'fail');
            }
        } else {
            message('That coloring book was not found.', 'fail');
        }
    } else {
        message('Invalid request.', 'fail');
    }

    redirect($admin_route . '/' . $plugin_route);
}
