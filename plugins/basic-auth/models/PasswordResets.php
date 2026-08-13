<?php

namespace PasswordResets;

use \Model\Model;

defined('ROOT') or die("Direct script access denied");

/**
 * PasswordResets model
 */
class PasswordResets extends Model {
    protected $table = 'password_resets';
    public $primary_key = 'id';

    protected $allowedColumns = [
        'user_id',
        'token_hash',
        'expires_at',
    ];

    protected $allowedUpdateColumns = [
        'used_at',
    ];

    public function createToken(int $userId, string $rawToken, int $ttlMinutes = 60): bool {
        return $this->create([
            'user_id'    => $userId,
            'token_hash' => hash('sha256', $rawToken),
            'expires_at' => date('Y-m-d H:i:s', time() + ($ttlMinutes * 60)),
        ]);
    }

    public function invalidateForUser(int $userId): bool {
        $this->query(
            "UPDATE {$this->table} SET used_at = NOW() WHERE user_id = :user_id AND used_at IS NULL",
            ['user_id' => $userId]
        );

        return !$this->has_error;
    }

    public function validateToken(string $rawToken): object|false {
        return $this->fetch(
            "SELECT * FROM {$this->table} WHERE token_hash = :token_hash AND used_at IS NULL AND expires_at > NOW() LIMIT 1",
            ['token_hash' => hash('sha256', $rawToken)]
        );
    }

    public function markUsed(int $id): bool {
        return $this->update($id, ['used_at' => date('Y-m-d H:i:s')]);
    }
}
