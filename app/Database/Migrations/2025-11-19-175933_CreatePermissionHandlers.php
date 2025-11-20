<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePermissionHandlers extends Migration
{
    public function up()
    {

        // ------------------------------

        // Package Permissions Table

        $this->forge->addField([
            'id'            => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'package_id'    => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
                'unsigned'       => true,
            ],
            'permission_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
                'unsigned'       => true,
            ],
            'created_at'    => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at'    => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at'    => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

//        $this->forge->addKey(['package_id', 'permission_id'], true);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('package_id', 'packages', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('permission_id', 'permissions', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('package_permissions', true);

        // ------------------------------

        // Group Permissions Table

        $this->forge->addField([
            'id'                 => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'group_id'           => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
                'unsigned'       => true,
            ],
            'permission_id'      => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
                'unsigned'       => true,
            ],
            'granted_by_user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'unsigned'       => true,
            ],
            'created_at'         => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at'         => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at'         => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

//        $this->forge->addKey(['group_id', 'permission_id'], true);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('group_id', 'groups', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('permission_id', 'permissions', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('granted_by_user_id', 'users', 'id', 'SET NULL', 'CASCADE');

        $this->forge->createTable('group_permissions', true);

        // ------------------------------

        // Package Group Permissions Template Table
        // - This table will store default permissions assigned to packages

        $this->forge->addField([
            'id'              => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'package_id'      => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
                'unsigned'       => true,
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
            'permission_json' => [
                'type' => 'JSON',
                'null' => false,
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

        $this->forge->addForeignKey('package_id', 'packages', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('package_group_permissions_templates', true);
    }

    public function down()
    {
        $this->forge->dropTable('package_permissions');
        $this->forge->dropTable('group_permissions');
        $this->forge->dropTable('package_group_permissions_templates');
    }
}
