<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DropTypeGeneralSettingsMigration extends Migration
{
    public function up()
    {
        $this->forge->dropColumn('general_settings', 'type');
    }

    public function down()
    {
        $this->forge->addColumn('general_settings', [
            'type' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'context', // position the new column after 'setting_key'
            ],
        ]);
    }
}
