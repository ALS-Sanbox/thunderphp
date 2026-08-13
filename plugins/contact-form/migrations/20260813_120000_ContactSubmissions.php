<?php

use Migration\Migration;

defined('FCPATH') or die("Direct script access denied");

/**
 * ContactSubmissions class
 */
class ContactSubmissions extends Migration {
    public function up() {
        $this->addColumn('id int unsigned auto_increment');
        $this->addColumn('name varchar(255) not null');
        $this->addColumn('email varchar(255) not null');
        $this->addColumn('subject varchar(255) default null');
        $this->addColumn('message text not null');
        $this->addColumn('ip_address varchar(45) default null');
        $this->addColumn('is_read tinyint(1) unsigned not null default 0');
        $this->addColumn('date_created datetime not null');

        $this->addPrimaryKey('id');
        $this->addKey('email');
        $this->addKey('ip_address');
        $this->addKey('date_created');

        $this->createTable('contact_submissions');

        $this->addData(['key' => 'contact_form_recipient_email', 'value' => '', 'type' => 'string', 'environment' => 'production']);
        $this->insert('settings');
    }

    public function down() {
        $this->dropTable('contact_submissions');
        $this->query("DELETE FROM settings WHERE `key` = 'contact_form_recipient_email'");
    }
}
