<?php

// Public, read-only endpoint - GET /coloring-book/api/{slug}
// Deliberately no permission check: this is meant to be fetched directly
// by the public-facing widget on any visitor's browser, logged in or not.
// It only ever exposes published books/pages and public URLs, never a
// filesystem path or anything from a draft.

header('Content-Type: application/json');

$slug = URL(2) ?? '';

if ($slug === '' || !preg_match('/^[a-z0-9-]+$/', $slug)) {
    http_response_code(400);
    echo json_encode(['error' => 'A valid coloring book slug is required.']);
    return;
}

$books = new \ColoringBook\ColoringBooks;
$book = $books->findPublishedBySlug($slug);

if (!$book) {
    http_response_code(404);
    echo json_encode(['error' => 'Coloring book not found.']);
    return;
}

$pages = new \ColoringBook\ColoringBookPages;
$publishedPages = $pages->findForBook((int) $book->id, 'published');

$response = [
    'title'       => $book->title,
    'slug'        => $book->slug,
    'description' => $book->description,
    'cover'       => !empty($book->cover_image) ? get_image($book->cover_image) : null,
    'pages'       => array_map(function ($page) {
        return [
            'id'        => (int) $page->id,
            'title'     => $page->title,
            'slug'      => $page->slug,
            'svg'       => !empty($page->svg_path) && file_exists($page->svg_path) ? ROOT . '/' . $page->svg_path : null,
            'thumbnail' => !empty($page->thumbnail_path) ? get_image($page->thumbnail_path) : null,
        ];
    }, $publishedPages),
];

echo json_encode($response);
