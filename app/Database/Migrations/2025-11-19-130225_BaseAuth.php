<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class BaseAuth extends Migration
{
    public function up()
    {

        // Permissions Table

        $this->forge->addField([
            'id'          => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'name'        => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'uri'         => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'is_parent'   => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'null'       => false,
                'default'    => 0,
            ],
            'context'     => [ // e.g., 'admin', 'user', etc.
                               'type'       => 'VARCHAR',
                               'constraint' => 100,
                               'null'       => true,
            ],
            'parent_id'   => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'unsigned'       => true,
            ],
            'icon'        => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'order'       => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'slug'        => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'actions'     => [
                'type' => 'JSON',
                'null' => true,
            ],
            'created_at'  => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at'  => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at'  => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('parent_id', 'permissions', 'id');

        $this->forge->createTable('permissions', true);

        // ------------------------------

        // Package Table

        $this->forge->addField([
            'id'            => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'name'          => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'description'   => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'price'         => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => false,
                'default'    => 0.00,
            ],
            'duration_days' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
                'default'    => 30,
            ],
            'features'      => [
                'type' => 'JSON',
                'null' => true,
            ],
            'status'        => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'null'       => false,
                'default'    => 1,
                // 1 = active, 0 = inactive
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

        $this->forge->addKey('id', true);

        $this->forge->createTable('packages', true);

        // ------------------------------

    }

    public function down()
    {
        $this->forge->dropTable('permissions');
        $this->forge->dropTable('packages');
    }
}
