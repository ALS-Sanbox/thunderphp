<?php
namespace GoogleAuth;

use \Model\Model;

defined('ROOT') or die("Direct script access denied");

class GoogleAccounts extends Model {
    protected $table = 'google_accounts';
    public $primary_key = 'id';

    protected $allowedColumns = ['user_id', 'google_sub', 'google_email', 'date_created'];
    protected $allowedUpdateColumns = ['google_email'];

    public function findBySub(string $sub) {
        return $this->first(['google_sub' => $sub]);
    }

    public function link(int $userId, string $sub, string $email): bool {
        return $this->create([
            'user_id'      => $userId,
            'google_sub'   => $sub,
            'google_email' => $email,
            'date_created' => date('Y-m-d H:i:s'),
        ]);
    }
}
