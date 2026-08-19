<?php
namespace ColoringBook;

use \Model\Model;

defined('ROOT') or die("Direct script access denied");

class ColoringBookPages extends Model {
    protected $table = 'coloring_book_pages';
    public $primary_key = 'id';
    public $order_column = 'sort_order';
    public $order = 'asc';

    protected $allowedColumns = [
        'coloring_book_id',
        'title',
        'slug',
        'svg_path',
        'thumbnail_path',
        'sort_order',
        'status',
        'date_created',
    ];

    protected $allowedUpdateColumns = [
        'title',
        'slug',
        'svg_path',
        'thumbnail_path',
        'sort_order',
        'status',
        'date_updated',
    ];

    public function validate_insert(array $data): bool {
        if (empty($data['title']) || trim($data['title']) === '') {
            $this->errors['title'] = "Title is required.";
        }

        if (empty($data['coloring_book_id']) || (int) $data['coloring_book_id'] <= 0) {
            $this->errors['coloring_book_id'] = "A parent coloring book is required.";
        }

        if (empty($data['slug']) || !preg_match('/^[a-z0-9-]+$/', $data['slug'])) {
            $this->errors['slug'] = "Slug is required and can only contain lowercase letters, numbers, and hyphens.";
        } elseif (!empty($data['coloring_book_id']) && $this->slugExistsInBook($data['slug'], (int) $data['coloring_book_id'])) {
            $this->errors['slug'] = "That slug is already used by another page in this coloring book.";
        }

        if (!empty($data['status']) && !in_array($data['status'], ['draft', 'published'], true)) {
            $this->errors['status'] = "Invalid status.";
        }

        return empty($this->errors);
    }

    public function validate_update(array $data, int $bookId, int $excludeId): bool {
        if (empty($data['title']) || trim($data['title']) === '') {
            $this->errors['title'] = "Title is required.";
        }

        if (empty($data['slug']) || !preg_match('/^[a-z0-9-]+$/', $data['slug'])) {
            $this->errors['slug'] = "Slug is required and can only contain lowercase letters, numbers, and hyphens.";
        } else {
            $existing = $this->first(['slug' => $data['slug'], 'coloring_book_id' => $bookId]);
            if ($existing && (int) $existing->id !== $excludeId) {
                $this->errors['slug'] = "That slug is already used by another page in this coloring book.";
            }
        }

        if (!empty($data['status']) && !in_array($data['status'], ['draft', 'published'], true)) {
            $this->errors['status'] = "Invalid status.";
        }

        return empty($this->errors);
    }

    protected function slugExistsInBook(string $slug, int $bookId): bool {
        $result = $this->fetch(
            "SELECT COUNT(*) as count FROM {$this->table} WHERE slug = ? AND coloring_book_id = ?",
            [$slug, $bookId]
        );
        return is_object($result) && (int) $result->count > 0;
    }

    /** Unique-within-book slug generator - the base Model's makeSlug() checks
     * uniqueness across the whole table, which doesn't fit here since two
     * different books can each legitimately have a page called "cover". */
    public function makeSlugForBook(string $string, int $bookId, string $separator = '-'): string {
        $slug = strtolower(trim($string));
        $slug = preg_replace('/[^a-z0-9]+/i', $separator, $slug);
        $slug = preg_replace('/' . preg_quote($separator, '/') . '+/', $separator, $slug);
        $slug = trim($slug, $separator);
        if ($slug === '') {
            $slug = 'page';
        }

        $original = $slug;
        $i = 1;
        while ($this->slugExistsInBook($slug, $bookId)) {
            $slug = $original . $separator . $i;
            $i++;
        }

        return $slug;
    }

    public function findForBook(int $bookId, string $status = null): array {
        $where = ['coloring_book_id' => $bookId];
        if ($status !== null) {
            $where['status'] = $status;
        }
        $this->order_column = 'sort_order';
        $this->order = 'asc';
        $this->limit = 500;
        return $this->where($where);
    }

    public function nextSortOrder(int $bookId): int {
        $result = $this->fetch(
            "SELECT COALESCE(MAX(sort_order), -1) + 1 as next_order FROM {$this->table} WHERE coloring_book_id = ?",
            [$bookId]
        );
        return $result ? (int) $result->next_order : 0;
    }

    public function moveUp(int $id, int $bookId): bool {
        return $this->swapWithNeighbor($id, $bookId, 'up');
    }

    public function moveDown(int $id, int $bookId): bool {
        return $this->swapWithNeighbor($id, $bookId, 'down');
    }

    protected function swapWithNeighbor(int $id, int $bookId, string $direction): bool {
        $rows = $this->findForBook($bookId);
        $index = null;
        foreach ($rows as $i => $row) {
            if ((int) $row->id === $id) {
                $index = $i;
                break;
            }
        }
        if ($index === null) return false;

        $swapIndex = $direction === 'up' ? $index - 1 : $index + 1;
        if ($swapIndex < 0 || $swapIndex >= count($rows)) return false;

        $current = $rows[$index];
        $neighbor = $rows[$swapIndex];

        $this->query("UPDATE {$this->table} SET sort_order = ? WHERE id = ?", [$neighbor->sort_order, $current->id]);
        $this->query("UPDATE {$this->table} SET sort_order = ? WHERE id = ?", [$current->sort_order, $neighbor->id]);

        return true;
    }
}
