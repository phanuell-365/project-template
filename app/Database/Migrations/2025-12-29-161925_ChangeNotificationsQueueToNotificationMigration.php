<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ChangeNotificationsQueueToNotificationMigration extends Migration
{
    public function up()
    {
        // Rename table notification_queue to notifications
        $this->forge->renameTable('notification_queue', 'notifications');
    }

    public function down()
    {
        // Rename table notifications back to notification_queue
        $this->forge->renameTable('notifications', 'notification_queue');
    }
}
