<?php
namespace Redirects;

use \Model\Model;

defined('ROOT') or die("Direct script access denied");

class Redirects extends Model {
    protected $table = 'redirects';
    public $primary_key = 'id';

    protected $allowedColumns = [
        'from_path',
        'to_path',
        'redirect_type',
        'disabled',
        'date_created',
    ];

    protected $allowedUpdateColumns = [
        'from_path',
        'to_path',
        'redirect_type',
        'disabled',
        'hits',
    ];

    public function validate_insert(array $data): bool {
        if (empty($data['from_path']) || trim($data['from_path']) === '') {
            $this->errors['from_path'] = "From Path is required.";
        }

        if (empty($data['to_path']) || trim($data['to_path']) === '') {
            $this->errors['to_path'] = "To Path is required.";
        }

        if (!empty($data['redirect_type']) && !in_array($data['redirect_type'], ['301', '302'], true)) {
            $this->errors['redirect_type'] = "Invalid redirect type.";
        }

        return empty($this->errors);
    }

    public function findByPath(string $path) {
        return $this->first(['from_path' => ltrim($path, '/'), 'disabled' => 0]);
    }

    public function incrementHits(int $id): void {
        $this->query("UPDATE {$this->table} SET hits = hits + 1 WHERE id = ?", [$id]);
    }
}
