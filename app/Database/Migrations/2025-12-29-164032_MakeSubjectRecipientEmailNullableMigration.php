<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MakeSubjectRecipientEmailNullableMigration extends Migration
{
    public function up()
    {
        $fields = [
            'recipient_email' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'default'    => null,
            ],
            'subject' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'default'    => null,
            ],
        ];

        $this->forge->modifyColumn('notifications', $fields);
    }

    public function down()
    {
        $fields = [
            'recipient_email' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'subject' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
        ];

        $this->forge->modifyColumn('notifications', $fields);
    }
}
