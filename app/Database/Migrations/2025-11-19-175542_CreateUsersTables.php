<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsersTables extends Migration
{
    public function up()
    {

        // ------------------------------

        // Group Table

        $this->forge->addField([
            'id'              => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'name'            => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'description'     => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'organization_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'unsigned'       => true,
            ],
            'created_at'      => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at'      => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at'      => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('organization_id', 'organizations', 'id');

        $this->forge->createTable('groups', true);

        // ------------------------------

        // User Table

        $this->forge->addField([
            'id'              => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'identifier'      => [ // e.g., the user's full name or username
                                   'type'       => 'VARCHAR',
                                   'constraint' => 100,
                                   'null'       => false,
                                   'unique'     => true,
            ],
            'identifier2'     => [ // e.g., the user's email
                                   'type'       => 'VARCHAR',
                                   'constraint' => 150,
                                   'null'       => false,
                                   'unique'     => true,
            ],
            'identifier3'     => [ // e.g., the user's phone number
                                   'type'       => 'VARCHAR',
                                   'constraint' => 20,
                                   'null'       => true,
                                   'unique'     => true,
            ],
            'secret'          => [ // e.g., the user's password hash
                                   'type'       => 'VARCHAR',
                                   'constraint' => 255,
                                   'null'       => false,
            ],
            'last_login'      => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'status'          => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'null'       => false,
                'default'    => 1,
                // 1 = active, 0 = inactive
            ],
            'organization_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'unsigned'       => true,
            ],
            'created_at'      => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at'      => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at'      => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('organization_id', 'organizations', 'id');

        $this->forge->createTable('users', true);

        // ------------------------------

        // User Groups Table

        $this->forge->addField([
            'id'                   => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id'              => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
                'unsigned'       => true,
            ],
            'group_id'             => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
                'unsigned'       => true,
            ],
            'onboarded_by_user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'unsigned'       => true,
            ],
            'created_at'           => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at'           => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at'           => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

//        $this->forge->addKey(['user_id', 'group_id'], true);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('group_id', 'groups', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('onboarded_by_user_id', 'users', 'id', 'SET NULL', 'CASCADE');

        $this->forge->createTable('user_groups', true);
    }

    public function down()
    {
        $this->forge->dropTable('groups');
        $this->forge->dropTable('users');
        $this->forge->dropTable('user_groups');
    }
}
