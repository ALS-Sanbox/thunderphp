<?php

use Migration\Migration;

defined('FCPATH') or die("Direct script access denied");

/**
 * Settings class
 */
class Settings extends Migration {
    public function up() {
        $this->addColumn('id int unsigned auto_increment');
        $this->addColumn('`key` varchar(255) not null');
        $this->addColumn('`value` text not null');
        $this->addColumn("`type` enum('string','int','bool','json','float') not null default 'string'");
        $this->addColumn("`environment` varchar(50) not null default 'production'");
        $this->addColumn("updated_at timestamp null default current_timestamp() on update current_timestamp()");

        $this->addPrimaryKey('id');
        $this->addKey('`key`');
        $this->addKey('`type`');
        $this->addKey('`environment`');

        $this->createTable('settings');

        $this->addData([
            ['key' => 'site_name',         'value' => 'Thunder PHP',        'type' => 'string', 'environment' => 'production'],
            ['key' => 'site_description',  'value' => 'A clone of wordpress','type' => 'string', 'environment' => 'production'],
            ['key' => 'debug_mode',        'value' => '1',                  'type' => 'bool',   'environment' => 'production'],
            ['key' => 'max_upload_size',   'value' => '10',                 'type' => 'int',    'environment' => 'production'],
            ['key' => 'pagination_limit',  'value' => '25',                 'type' => 'int',    'environment' => 'production'],
            ['key' => 'site_homepage',     'value' => 'home',               'type' => 'string', 'environment' => 'production'],
            ['key' => 'site_url',          'value' => 'https://your-site.com', 'type' => 'string', 'environment' => 'production'],
            ['key' => 'admin_email',       'value' => 'admin@your-site.com','type' => 'string', 'environment' => 'production'],
        ]);

        $this->insert('settings');
    }

    public function down() {
        $this->dropTable('settings');
    }
}
