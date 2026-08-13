<?php

use PHPUnit\Framework\TestCase;

/**
 * Tests for the genuinely pure (or near-pure, no-DB) helpers in
 * app/core/functions.php. See tests/Integration for anything that needs a
 * real database - Database's eager PDO connection in its own constructor
 * means Model/Migration subclasses can't be unit-tested in isolation.
 */
class FunctionsTest extends TestCase
{
    public function test_esc_escapes_html_special_characters(): void
    {
        $this->assertSame(
            '&lt;script&gt;alert(1)&lt;/script&gt;',
            esc('<script>alert(1)</script>')
        );
    }

    public function test_esc_escapes_single_and_double_quotes(): void
    {
        // ENT_QUOTES: both quote styles get escaped, not just double quotes.
        $this->assertSame('&#039;single&#039;', esc("'single'"));
        $this->assertSame('&quot;double&quot;', esc('"double"'));
    }

    public function test_esc_handles_null_without_error(): void
    {
        $this->assertSame('', esc(null));
    }

    public function test_esc_passes_through_plain_text(): void
    {
        $this->assertSame('Hello World', esc('Hello World'));
    }

    public function test_split_url_splits_on_slashes(): void
    {
        $this->assertSame(
            ['admin', 'site-menus', 'edit', '5'],
            split_url('admin/site-menus/edit/5')
        );
    }

    public function test_split_url_trims_leading_and_trailing_slashes(): void
    {
        $this->assertSame(['home'], split_url('/home/'));
    }

    public function test_split_url_strips_disallowed_characters_per_segment(): void
    {
        // Only a-z, A-Z, 0-9, and hyphen survive in each segment.
        $this->assertSame(['abc123', 'de-f'], split_url('ab<c>123/d"e-f'));
    }

    public function test_split_url_empty_string_yields_single_empty_segment(): void
    {
        $this->assertSame([''], split_url(''));
    }

    public function test_valid_route_true_when_on_is_all(): void
    {
        $GLOBALS['APP']['URL'] = ['anything'];
        $json = (object) ['routes' => (object) ['on' => ['all']]];

        $this->assertTrue(valid_route($json));
    }

    public function test_valid_route_true_when_current_page_matches_on_list(): void
    {
        $GLOBALS['APP']['URL'] = ['admin'];
        $json = (object) ['routes' => (object) ['on' => ['admin']]];

        $this->assertTrue(valid_route($json));
    }

    public function test_valid_route_false_when_current_page_not_in_on_list(): void
    {
        $GLOBALS['APP']['URL'] = ['home'];
        $json = (object) ['routes' => (object) ['on' => ['admin']]];

        $this->assertFalse(valid_route($json));
    }

    public function test_valid_route_off_takes_precedence_over_on(): void
    {
        // header-footer's real config: "on":["all"], "off":["admin"] - this
        // is the exact shape that caused a real bug earlier this project
        // (the whole plugin.php silently failed to load on admin pages).
        $GLOBALS['APP']['URL'] = ['admin'];
        $json = (object) ['routes' => (object) ['on' => ['all'], 'off' => ['admin']]];

        $this->assertFalse(valid_route($json));
    }

    public function test_valid_route_false_with_no_routes_configured(): void
    {
        $GLOBALS['APP']['URL'] = ['home'];
        $json = (object) ['routes' => (object) []];

        $this->assertFalse(valid_route($json));
    }

    public function test_get_image_returns_fallback_for_missing_male_image(): void
    {
        $this->assertSame(
            ROOT . '/assets/images/user_male.jpg',
            get_image('does/not/exist.jpg', 'male')
        );
    }

    public function test_get_image_returns_fallback_for_missing_female_image(): void
    {
        $this->assertSame(
            ROOT . '/assets/images/user_female.jpg',
            get_image('does/not/exist.jpg', 'female')
        );
    }

    public function test_get_image_returns_default_fallback_for_missing_post_image(): void
    {
        $this->assertSame(
            ROOT . '/assets/images/no_image.jpg',
            get_image('does/not/exist.jpg')
        );
    }

    public function test_get_image_returns_real_path_when_file_exists(): void
    {
        $tmpFile = FCPATH . 'uploads_test_tmp_file.txt';
        file_put_contents($tmpFile, 'test');

        try {
            $relative = 'uploads_test_tmp_file.txt';
            $this->assertSame(ROOT . '/' . $relative, get_image($relative));
        } finally {
            @unlink($tmpFile);
        }
    }
}
