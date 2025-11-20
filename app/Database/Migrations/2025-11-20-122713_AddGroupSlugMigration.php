<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddGroupSlugMigration extends Migration
{
    public function up()
    {
        $this->forge->addColumn('groups', [
            'slug' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'name', // position the new column after 'name'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('groups', 'slug');
    }
}
