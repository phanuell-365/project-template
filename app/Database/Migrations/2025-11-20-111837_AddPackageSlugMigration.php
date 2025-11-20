<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPackageSlugMigration extends Migration
{
    public function up()
    {
        $this->forge->addColumn('packages', [
            'slug' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('packages', 'slug');
    }
}
