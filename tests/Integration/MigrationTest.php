<?php

use PHPUnit\Framework\TestCase;

/**
 * Covers Migration's createTable()/addData()/insert() staging behavior,
 * including the exact ordering gotcha (createTable() wipes staged data via
 * its internal clearKeys() call) that has already caused one real bug in
 * this project.
 */
class MigrationTest extends TestCase
{
    private \Migration\Migration $migration;

    protected function setUp(): void
    {
        $this->migration = new \Migration\Migration();
        $this->migration->query('DROP TABLE IF EXISTS phpunit_migration_test');
    }

    protected function tearDown(): void
    {
        $this->migration->query('DROP TABLE IF EXISTS phpunit_migration_test');
    }

    public function test_create_table_then_add_data_then_insert_round_trip(): void
    {
        $this->migration->addColumn('id int unsigned auto_increment');
        $this->migration->addColumn('name varchar(100) not null');
        $this->migration->addPrimaryKey('id');
        $this->migration->createTable('phpunit_migration_test');

        // Correct order: createTable() first, addData()/insert() after -
        // matches the working pattern in e.g. Permissionsroles.php.
        $this->migration->addData(['name' => 'Test Row']);
        $this->migration->insert('phpunit_migration_test');

        $row = $this->migration->fetch(
            'SELECT * FROM phpunit_migration_test WHERE name = ?',
            ['Test Row']
        );

        $this->assertIsObject($row);
        $this->assertSame('Test Row', $row->name);
    }

    public function test_add_data_before_create_table_is_silently_lost(): void
    {
        // Documents the known gotcha rather than "fixing" createTable()'s
        // behavior, so a future change to that ordering is a deliberate
        // decision rather than an accidental regression. This exact bug
        // (wrong order) shipped once in plugins/user-roles' Userroles
        // migration and silently produced an empty seed table.
        $this->migration->addColumn('id int unsigned auto_increment');
        $this->migration->addColumn('name varchar(100) not null');
        $this->migration->addPrimaryKey('id');

        $this->migration->addData(['name' => 'Lost Row']);
        $this->migration->createTable('phpunit_migration_test'); // wipes staged data
        $this->migration->insert('phpunit_migration_test'); // inserts nothing

        $row = $this->migration->fetch(
            'SELECT * FROM phpunit_migration_test WHERE name = ?',
            ['Lost Row']
        );

        $this->assertFalse($row);
    }
}
