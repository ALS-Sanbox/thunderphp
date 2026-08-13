<?php
namespace ContactForm;

use \Model\Model;

defined('ROOT') or die("Direct script access denied");

class ContactSubmissions extends Model {
    protected $table = 'contact_submissions';
    public $primary_key = 'id';
    public $order_column = 'date_created';

    protected $allowedColumns = [
        'name',
        'email',
        'subject',
        'message',
        'ip_address',
        'date_created',
    ];

    protected $allowedUpdateColumns = [
        'is_read',
    ];

    public function validate_insert(array $data): bool {
        if (empty($data['name']) || trim($data['name']) === '') {
            $this->errors['name'] = "Name is required.";
        }

        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $this->errors['email'] = "A valid email is required.";
        }

        if (empty($data['message']) || trim($data['message']) === '') {
            $this->errors['message'] = "Message is required.";
        }

        return empty($this->errors);
    }

    public function recentCountForIp(string $ip, int $minutes = 60): int {
        $row = $this->fetch(
            "SELECT COUNT(*) as count FROM {$this->table} WHERE ip_address = ? AND date_created > (NOW() - INTERVAL ? MINUTE)",
            [$ip, $minutes]
        );
        return $row ? (int) $row->count : 0;
    }
}
