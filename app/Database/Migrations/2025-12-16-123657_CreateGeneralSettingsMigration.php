<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateGeneralSettingsMigration extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'         => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'setting_key'   => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'setting_value' => [
                'type'       => 'TEXT',
                'null'       => true,
            ],
            'organization_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'unsigned'   => true,
            ],
            'context'       => [ // e.g., 'security', 'appearance', etc.
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'type'          => [ // e.g., 'string', 'integer', 'boolean', etc.
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
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
        $this->forge->addUniqueKey('setting_key');

        $this->forge->addForeignKey('organization_id', 'organizations', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('general_settings', true);
    }

    public function down()
    {
        $this->forge->dropTable('general_settings', true);
    }
}
