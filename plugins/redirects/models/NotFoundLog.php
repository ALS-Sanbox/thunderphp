<?php
namespace Redirects;

use \Model\Model;

defined('ROOT') or die("Direct script access denied");

class NotFoundLog extends Model {
    protected $table = 'not_found_log';
    public $primary_key = 'id';
    public $order_column = 'last_seen';

    protected $allowedColumns = ['url', 'hits', 'first_seen', 'last_seen'];
    protected $allowedUpdateColumns = ['hits', 'last_seen'];

    public function logHit(string $url): void {
        $url = ltrim($url, '/');
        if ($url === '') return;

        $now = date('Y-m-d H:i:s');

        $this->query(
            "INSERT INTO {$this->table} (url, hits, first_seen, last_seen) VALUES (?, 1, ?, ?)
             ON DUPLICATE KEY UPDATE hits = hits + 1, last_seen = VALUES(last_seen)",
            [$url, $now, $now]
        );
    }
}
