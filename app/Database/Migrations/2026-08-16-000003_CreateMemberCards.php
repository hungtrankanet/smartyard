<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration: Create member_cards table with FK to members
 */
class CreateMemberCards extends Migration
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
            'file_path' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'side' => [
                'type'       => 'ENUM',
                'constraint' => ['front', 'back', 'single'],
                'default'    => 'single',
            ],
            'ocr_raw' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'ocr_parsed' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'ocr_status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'done', 'failed'],
                'default'    => 'pending',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('member_id');
        $this->forge->addKey('ocr_status');
        $this->forge->addForeignKey('member_id', 'members', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('member_cards', true);
    }

    public function down()
    {
        $this->forge->dropTable('member_cards', true);
    }
}
