<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DropActionColumn extends Migration
{
    public function up()
    {
        $this->forge->dropColumn('permissions', 'actions');
    }

    public function down()
    {
        $this->forge->addColumn('permissions', [
            'actions' => [
                'type'       => 'JSON',
                'null'       => true,
            ],
        ]);
    }
}
