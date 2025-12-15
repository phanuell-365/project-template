<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePermissionGroupTemplatesMigration extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'         => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'auto_increment' => true,
            ],
            'package_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'group_slug' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'permissions_json' => [
                'type' => 'JSON',
                'null' => false,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('package_id', 'packages', 'id');

        $this->forge->createTable('package_group_permission_templates', true);
    }

    public function down()
    {
        $this->forge->dropTable('package_group_permission_templates', true);
    }
}
