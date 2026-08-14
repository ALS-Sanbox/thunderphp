<?php

use Migration\Migration;

defined('FCPATH') or die("Direct script access denied");

/**
 * GoogleAuthSettings class
 *
 * Seeds Google OAuth keys into the shared `settings` table, same pattern as
 * SmtpSettings/SeoSettings.
 */
class GoogleAuthSettings extends Migration {
    public function up() {
        $defaultSettings = [
            ['key' => 'google_oauth_client_id',     'value' => '', 'type' => 'string', 'environment' => 'production'],
            ['key' => 'google_oauth_client_secret', 'value' => '', 'type' => 'string', 'environment' => 'production'],
            ['key' => 'google_oauth_enabled',       'value' => '0', 'type' => 'bool',  'environment' => 'production'],
        ];

        foreach ($defaultSettings as $row) {
            $this->addData($row);
        }

        $this->insert('settings');
    }

    public function down() {
        $this->query("DELETE FROM settings WHERE `key` IN ('google_oauth_client_id','google_oauth_client_secret','google_oauth_enabled')");
    }
}
