<?php

use PHPUnit\Framework\TestCase;

require_once FCPATH . 'app/models/Siteusers.php';

/**
 * Integration tests for the consolidated Siteusers model (app/models/Siteusers.php).
 * Needs a real DB connection because Model extends Database, whose
 * constructor connects eagerly - see tests/bootstrap.php for the TEST_DB_*
 * environment variables this relies on.
 */
class SiteusersValidationTest extends TestCase
{
    private \Siteusers\Siteusers $user;

    protected function setUp(): void
    {
        $this->user = new \Siteusers\Siteusers();
    }

    public function test_validate_insert_requires_first_name(): void
    {
        $result = $this->user->validate_insert([
            'last_name' => 'Doe',
            'email'     => 'test@example.com',
            'password'  => 'Passw0rd!',
        ]);

        $this->assertFalse($result);
        $this->assertArrayHasKey('first_name', $this->user->errors);
    }

    public function test_validate_insert_rejects_names_with_numbers(): void
    {
        $result = $this->user->validate_insert([
            'first_name' => 'John3',
            'last_name'  => 'Doe',
            'email'      => 'test@example.com',
            'password'   => 'Passw0rd!',
        ]);

        $this->assertFalse($result);
        $this->assertArrayHasKey('first_name', $this->user->errors);
    }

    public function test_validate_insert_rejects_invalid_email(): void
    {
        $result = $this->user->validate_insert([
            'first_name' => 'John',
            'last_name'  => 'Doe',
            'email'      => 'not-an-email',
            'password'   => 'Passw0rd!',
        ]);

        $this->assertFalse($result);
        $this->assertArrayHasKey('email', $this->user->errors);
    }

    public function test_validate_insert_rejects_short_password(): void
    {
        $result = $this->user->validate_insert([
            'first_name' => 'John',
            'last_name'  => 'Doe',
            'email'      => 'test@example.com',
            'password'   => 'Ab1!',
        ]);

        $this->assertFalse($result);
        $this->assertArrayHasKey('password', $this->user->errors);
    }

    public function test_validate_insert_rejects_password_without_special_character(): void
    {
        $result = $this->user->validate_insert([
            'first_name' => 'John',
            'last_name'  => 'Doe',
            'email'      => 'test@example.com',
            'password'   => 'Password1',
        ]);

        $this->assertFalse($result);
        $this->assertArrayHasKey('password', $this->user->errors);
    }

    public function test_validate_insert_passes_with_valid_data(): void
    {
        $result = $this->user->validate_insert([
            'first_name' => 'John',
            'last_name'  => 'Doe',
            'email'      => 'test@example.com',
            'password'   => 'Passw0rd!',
        ]);

        $this->assertTrue($result);
        $this->assertEmpty($this->user->errors);
    }
}
