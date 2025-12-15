<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPackageGroupPermTemplateSlugMigration extends Migration
{
    public function up()
    {
        // Add 'slug' column to 'package_permission_templates' table
        $this->forge->addColumn('package_group_permissions_templates', [
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
        // Remove 'slug' column from 'package_permission_templates' table
        $this->forge->dropColumn('package_group_permissions_templates', 'slug');
    }
}
