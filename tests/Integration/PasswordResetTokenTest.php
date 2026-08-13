<?php

use PHPUnit\Framework\TestCase;

require_once FCPATH . 'app/models/Siteusers.php';
require_once FCPATH . 'plugins/basic-auth/models/PasswordResets.php';

/**
 * Covers the same lifecycle (create/validate/single-use/expiry) that was
 * manually verified against a scratch DB when the password reset feature
 * was originally built.
 */
class PasswordResetTokenTest extends TestCase
{
    private \PasswordResets\PasswordResets $resets;
    private int $testUserId;

    protected function setUp(): void
    {
        $this->resets = new \PasswordResets\PasswordResets();

        $users = new \Siteusers\Siteusers();
        $users->query("DELETE FROM siteusers WHERE email = 'phpunit-reset-test@example.invalid'");
        $users->create([
            'first_name' => 'Phpunit',
            'last_name'  => 'Test',
            'email'      => 'phpunit-reset-test@example.invalid',
            'password'   => password_hash('Whatever!23', PASSWORD_DEFAULT),
            'image'      => '',
        ]);
        $this->testUserId = (int) $users->insert_id;

        $this->resets->query("DELETE FROM password_resets WHERE user_id = ?", [$this->testUserId]);
    }

    protected function tearDown(): void
    {
        $this->resets->query("DELETE FROM password_resets WHERE user_id = ?", [$this->testUserId]);
        $this->resets->query("DELETE FROM siteusers WHERE id = ?", [$this->testUserId]);
    }

    public function test_valid_token_validates_successfully(): void
    {
        $rawToken = bin2hex(random_bytes(32));
        $this->resets->createToken($this->testUserId, $rawToken, 60);

        $row = $this->resets->validateToken($rawToken);

        $this->assertIsObject($row);
        $this->assertEquals($this->testUserId, $row->user_id);
    }

    public function test_unknown_token_does_not_validate(): void
    {
        $this->assertFalse($this->resets->validateToken('not-a-real-token'));
    }

    public function test_used_token_no_longer_validates(): void
    {
        $rawToken = bin2hex(random_bytes(32));
        $this->resets->createToken($this->testUserId, $rawToken, 60);
        $row = $this->resets->validateToken($rawToken);
        $this->resets->markUsed($row->id);

        $this->assertFalse($this->resets->validateToken($rawToken));
    }

    public function test_invalidate_for_user_marks_previous_tokens_used(): void
    {
        $firstToken = bin2hex(random_bytes(32));
        $this->resets->createToken($this->testUserId, $firstToken, 60);

        $this->resets->invalidateForUser($this->testUserId);

        $this->assertFalse($this->resets->validateToken($firstToken));
    }

    public function test_expired_token_does_not_validate(): void
    {
        $rawToken = bin2hex(random_bytes(32));
        $this->resets->createToken($this->testUserId, $rawToken, -1); // already expired

        $this->assertFalse($this->resets->validateToken($rawToken));
    }
}
