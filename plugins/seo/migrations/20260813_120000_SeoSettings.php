<?php

use Migration\Migration;

defined('FCPATH') or die("Direct script access denied");

/**
 * SeoSettings class
 *
 * Seeds SEO-related keys into the shared `settings` table (same pattern as
 * SmtpSettings/SiteLogoSetting in the settings plugin) rather than creating
 * a dedicated table for a handful of key/value options.
 */
class SeoSettings extends Migration {
    public function up() {
        $defaultSettings = [
            ['key' => 'seo_default_description', 'value' => '', 'type' => 'string', 'environment' => 'production'],
            ['key' => 'seo_default_keywords',     'value' => '', 'type' => 'string', 'environment' => 'production'],
            ['key' => 'seo_default_og_image',     'value' => '', 'type' => 'string', 'environment' => 'production'],
            ['key' => 'seo_robots_txt',           'value' => '', 'type' => 'string', 'environment' => 'production'],
            ['key' => 'seo_include_pages',        'value' => '1', 'type' => 'bool',  'environment' => 'production'],
            ['key' => 'seo_include_posts',        'value' => '1', 'type' => 'bool',  'environment' => 'production'],
            ['key' => 'seo_sitemap_generated_at', 'value' => '', 'type' => 'string', 'environment' => 'production'],
        ];

        foreach ($defaultSettings as $row) {
            $this->addData($row);
        }

        $this->insert('settings');
    }

    public function down() {
        $this->query("DELETE FROM settings WHERE `key` IN ('seo_default_description','seo_default_keywords','seo_default_og_image','seo_robots_txt','seo_include_pages','seo_include_posts','seo_sitemap_generated_at')");
    }
}
