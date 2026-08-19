<?php
namespace ColoringBook;

use \Model\Model;

defined('ROOT') or die("Direct script access denied");

class ColoringBooks extends Model {
    protected $table = 'coloring_books';
    public $primary_key = 'id';
    public $order_column = 'sort_order';
    public $order = 'asc';

    protected $allowedColumns = [
        'title',
        'slug',
        'description',
        'cover_image',
        'status',
        'sort_order',
        'date_created',
    ];

    protected $allowedUpdateColumns = [
        'title',
        'slug',
        'description',
        'cover_image',
        'status',
        'sort_order',
        'date_updated',
    ];

    public function validate_insert(array $data): bool {
        if (empty($data['title']) || trim($data['title']) === '') {
            $this->errors['title'] = "Title is required.";
        }

        if (empty($data['slug']) || !preg_match('/^[a-z0-9-]+$/', $data['slug'])) {
            $this->errors['slug'] = "Slug is required and can only contain lowercase letters, numbers, and hyphens.";
        } elseif ($this->first(['slug' => $data['slug']])) {
            $this->errors['slug'] = "That slug is already in use by another coloring book.";
        }

        if (!empty($data['status']) && !in_array($data['status'], ['draft', 'published'], true)) {
            $this->errors['status'] = "Invalid status.";
        }

        return empty($this->errors);
    }

    public function validate_update(array $data, int $excludeId): bool {
        if (empty($data['title']) || trim($data['title']) === '') {
            $this->errors['title'] = "Title is required.";
        }

        if (empty($data['slug']) || !preg_match('/^[a-z0-9-]+$/', $data['slug'])) {
            $this->errors['slug'] = "Slug is required and can only contain lowercase letters, numbers, and hyphens.";
        } else {
            $existing = $this->first(['slug' => $data['slug']]);
            if ($existing && (int) $existing->id !== $excludeId) {
                $this->errors['slug'] = "That slug is already in use by another coloring book.";
            }
        }

        if (!empty($data['status']) && !in_array($data['status'], ['draft', 'published'], true)) {
            $this->errors['status'] = "Invalid status.";
        }

        return empty($this->errors);
    }

    public function findBySlug(string $slug) {
        return $this->first(['slug' => $slug]);
    }

    public function findPublishedBySlug(string $slug) {
        return $this->first(['slug' => $slug, 'status' => 'published']);
    }

    public function publishedForDropdown(): array {
        $this->order_column = 'title';
        $this->order = 'asc';
        return $this->query(
            "SELECT id, title, slug FROM {$this->table} WHERE status = 'published' ORDER BY title ASC"
        );
    }

    public function pageCount(int $bookId): int {
        $result = $this->fetch("SELECT COUNT(*) as total FROM coloring_book_pages WHERE coloring_book_id = ?", [$bookId]);
        return $result ? (int) $result->total : 0;
    }
}
