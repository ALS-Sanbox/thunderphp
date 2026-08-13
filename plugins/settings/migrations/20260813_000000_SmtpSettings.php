<?php

use Migration\Migration;

defined('FCPATH') or die("Direct script access denied");

/**
 * SmtpSettings class
 */
class SmtpSettings extends Migration {
    public function up() {
        $defaultSettings = [
            ['key' => 'smtp_host',       'value' => '',    'type' => 'string', 'environment' => 'production'],
            ['key' => 'smtp_port',       'value' => '587', 'type' => 'int',    'environment' => 'production'],
            ['key' => 'smtp_encryption', 'value' => 'tls', 'type' => 'string', 'environment' => 'production'],
            ['key' => 'smtp_username',   'value' => '',    'type' => 'string', 'environment' => 'production'],
            ['key' => 'smtp_password',   'value' => '',    'type' => 'string', 'environment' => 'production'],
            ['key' => 'smtp_from_email', 'value' => '',    'type' => 'string', 'environment' => 'production'],
            ['key' => 'smtp_from_name',  'value' => '',    'type' => 'string', 'environment' => 'production'],
        ];

        foreach ($defaultSettings as $row) {
            $this->addData($row);
        }

        $this->insert('settings');
    }

    public function down() {
        $this->query("DELETE FROM settings WHERE `key` IN ('smtp_host','smtp_port','smtp_encryption','smtp_username','smtp_password','smtp_from_email','smtp_from_name')");
    }
}
