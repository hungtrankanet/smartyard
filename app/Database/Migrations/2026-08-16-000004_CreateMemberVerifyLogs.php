<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration: Create member_verify_logs table with FK to members
 */
class CreateMemberVerifyLogs extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'member_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'check_type' => [
                'type'       => 'ENUM',
                'constraint' => ['google_maps', 'fanpage', 'manual'],
                'null'       => false,
            ],
            'result' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
            ],
            'detail' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'checked_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('member_id');
        $this->forge->addKey('check_type');
        $this->forge->addKey('checked_at');
        $this->forge->addForeignKey('member_id', 'members', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('member_verify_logs', true);
    }

    public function down()
    {
        $this->forge->dropTable('member_verify_logs', true);
    }
}
