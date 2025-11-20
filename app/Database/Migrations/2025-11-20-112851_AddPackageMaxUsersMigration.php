<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPackageMaxUsersMigration extends Migration
{
    public function up()
    {
        $this->forge->addColumn('packages', [
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
        $this->forge->dropColumn('packages', 'max_users');
    }
}
