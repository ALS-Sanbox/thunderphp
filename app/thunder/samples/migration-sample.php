<?php

use Migration\Migration;

defined('FCPATH') or die("Direct script access denied");

/**
 * {CLASS_NAME} class
 */
class {CLASS_NAME} extends Migration {
    public function up() {
        $this->addColumn('id int unsigned auto_increment');
        $this->addColumn('email varchar(255) not null unique');
        $this->addColumn('password varchar(255) not null');
        $this->addColumn('deleted tinyint(1) unsigned default 0');
        $this->addColumn('date_created datetime default null');
        $this->addColumn('date_updated datetime default null');
        $this->addColumn('date_deleted datetime default null');

        $this->addPrimaryKey('id');
        $this->addKey('email');
        $this->addKey('deleted');
        $this->addKey('date_created');
        $this->addKey('date_deleted');

        $this->createTable({TABLE_NAME});


        /*$this->addData([
        *'email'=>'email@email.com',
        *]);
        */
         
         $this->insert({TABLE_NAME});
         
    }

    public function down() {
        $this->dropTable({TABLE_NAME});
    }
}