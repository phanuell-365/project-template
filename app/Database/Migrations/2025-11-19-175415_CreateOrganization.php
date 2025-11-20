<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOrganization extends Migration
{
    public function up()
    {
        // Organization Table

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
            'address'       => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'contact_email' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'contact_phone' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],
            'slug'          => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'package_id'    => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
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

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('package_id', 'packages', 'id','CASCADE', 'CASCADE');

        $this->forge->createTable('organizations', true);
    }

    public function down()
    {
        $this->forge->dropTable('organizations');
    }
}
