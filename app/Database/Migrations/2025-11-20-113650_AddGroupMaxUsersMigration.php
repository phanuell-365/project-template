<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddGroupMaxUsersMigration extends Migration
{
    public function up()
    {
        $this->forge->addColumn('groups', [
            'max_users' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
                'default'    => 0,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('groups', 'max_users');
    }
}
