<?php

use PHPUnit\Framework\TestCase;

require_once FCPATH . 'plugins/site-menus/models/Menu.php';

/**
 * Menu::validate_insert() is not logically pure - it runs a real duplicate-
 * title SELECT as part of validation, so this can only be tested with a
 * live DB, unlike Siteusers' regex-only validators.
 */
class MenuValidationTest extends TestCase
{
    private \siteMenus\Menu $menu;

    protected function setUp(): void
    {
        $this->menu = new \siteMenus\Menu();
        $this->menu->query("DELETE FROM menus WHERE title LIKE 'PHPUnit%'");
    }

    protected function tearDown(): void
    {
        $this->menu->query("DELETE FROM menus WHERE title LIKE 'PHPUnit%'");
    }

    public function test_validate_insert_requires_title(): void
    {
        $result = $this->menu->validate_insert(['title' => '']);

        $this->assertFalse($result);
        $this->assertArrayHasKey('title', $this->menu->errors);
    }

    public function test_validate_insert_rejects_duplicate_title(): void
    {
        $this->menu->create([
            'title'      => 'PHPUnit Test Menu',
            'slug'       => 'phpunit-test-menu',
            'parent'     => 0,
            'disabled'   => 0,
            'list_order' => 10,
        ]);

        $result = $this->menu->validate_insert(['title' => 'PHPUnit Test Menu']);

        $this->assertFalse($result);
        $this->assertArrayHasKey('title', $this->menu->errors);
    }

    public function test_validate_insert_passes_with_unique_title(): void
    {
        $result = $this->menu->validate_insert(['title' => 'PHPUnit Unique Title']);

        $this->assertTrue($result);
    }
}
