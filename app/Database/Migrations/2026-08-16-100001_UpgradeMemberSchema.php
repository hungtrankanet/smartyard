<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration: Upgrade Member Schema to v2.0
 * 
 * Architecture Upgrade:
 * - Extends `members` with multilingual and flexible metadata attributes.
 * - Creates `member_contacts` for 1-to-N company representatives.
 * - Creates `member_branches` for 1-to-N corporate offices/hubs.
 * - Links `member_cards` to specific contacts (`contact_id`).
 * - Seamlessly migrates existing v1.0 flat data to hierarchical tables.
 * 
 * Strict Constraint: Under 500 lines of code.
 */
class UpgradeMemberSchema extends Migration
{
    public function up()
    {
        // 1. ALTER members table: Add multi-language and metadata columns
        $memberFields = [
            'company_name_en' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'company_name',
            ],
            'company_name_local' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'company_name_en',
            ],
            'detected_language' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'default'    => 'vi',
                'after'      => 'company_name_local',
            ],
            'metadata' => [
                'type'  => 'JSON',
                'null'  => true,
                'after' => 'note',
            ],
        ];

        if ($this->db->tableExists('members')) {
            $existingCols = $this->db->getFieldNames('members');
            $colsToAdd = [];
            foreach ($memberFields as $colName => $colDef) {
                if (!in_array($colName, $existingCols, true)) {
                    $colsToAdd[$colName] = $colDef;
                }
            }
            if (!empty($colsToAdd)) {
                $this->forge->addColumn('members', $colsToAdd);
            }

            // High concurrency lookup indexes on members table
            $this->addIndexSafely('members', 'company_name_en', 'idx_members_company_en');
            $this->addIndexSafely('members', 'company_name_local', 'idx_members_company_local');
            $this->addIndexSafely('members', 'detected_language', 'idx_members_detected_lang');
            $this->addIndexSafely('members', 'tax_code', 'idx_members_tax_code');
        }

        // 2. CREATE member_contacts table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'company_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'full_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'full_name_en' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'full_name_local' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'position' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'position_en' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'department' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'phone' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'phone_2' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'email_2' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'is_primary' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'metadata' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('company_id');
        $this->forge->addKey('full_name');
        $this->forge->addKey('full_name_en');
        $this->forge->addKey('is_primary');
        $this->forge->addKey('phone');
        $this->forge->addKey('email');
        $this->forge->addKey(['company_id', 'is_primary']);
        $this->forge->addForeignKey('company_id', 'members', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('member_contacts', true);

        // 3. CREATE member_branches table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'company_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'branch_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'country' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'city' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'address' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'phone' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'is_headquarters' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'metadata' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('company_id');
        $this->forge->addKey('country');
        $this->forge->addKey('city');
        $this->forge->addKey('is_headquarters');
        $this->forge->addKey(['company_id', 'is_headquarters']);
        $this->forge->addForeignKey('company_id', 'members', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('member_branches', true);

        // 4. ALTER member_cards: Add contact_id column and link
        if ($this->db->tableExists('member_cards')) {
            $cardCols = $this->db->getFieldNames('member_cards');
            if (!in_array('contact_id', $cardCols, true)) {
                $cardFields = [
                    'contact_id' => [
                        'type'       => 'INT',
                        'constraint' => 11,
                        'unsigned'   => true,
                        'null'       => true,
                        'after'      => 'member_id',
                    ],
                ];
                $this->forge->addColumn('member_cards', $cardFields);
                $this->addIndexSafely('member_cards', 'contact_id', 'idx_member_cards_contact');

                try {
                    $this->db->query("ALTER TABLE `member_cards` ADD CONSTRAINT `fk_member_cards_contact` FOREIGN KEY (`contact_id`) REFERENCES `member_contacts`(`id`) ON DELETE SET NULL");
                } catch (\Throwable $e) {
                    // Fallback for DB engines with strict DDL differences
                }
            }
        }

        // 5. DATA MIGRATION: Migrate legacy v1.0 contact and address records
        $this->migrateLegacyData();
    }

    public function down()
    {
        // 1. Drop member_branches table
        $this->forge->dropTable('member_branches', true);

        // 2. Drop contact_id column from member_cards
        if ($this->db->tableExists('member_cards')) {
            try {
                $this->db->query("ALTER TABLE `member_cards` DROP FOREIGN KEY `fk_member_cards_contact`");
            } catch (\Throwable $e) {
                // Ignore if not present
            }
            if (in_array('contact_id', $this->db->getFieldNames('member_cards'), true)) {
                $this->forge->dropColumn('member_cards', 'contact_id');
            }
        }

        // 3. Drop member_contacts table
        $this->forge->dropTable('member_contacts', true);

        // 4. Drop added columns from members table
        if ($this->db->tableExists('members')) {
            $columnsToDrop = [];
            $currentCols = $this->db->getFieldNames('members');
            foreach (['company_name_en', 'company_name_local', 'detected_language', 'metadata'] as $col) {
                if (in_array($col, $currentCols, true)) {
                    $columnsToDrop[] = $col;
                }
            }
            if (!empty($columnsToDrop)) {
                $this->forge->dropColumn('members', $columnsToDrop);
            }
        }
    }

    /**
     * Helper to safely execute index creation without throwing on duplicate/unsupported drivers
     */
    private function addIndexSafely(string $table, string $column, string $indexName): void
    {
        try {
            $this->db->query("CREATE INDEX `{$indexName}` ON `{$table}` (`{$column}`)");
        } catch (\Throwable $e) {
            // Index might already exist or driver syntax differs
        }
    }

    /**
     * Migrate existing members' representative & address info to member_contacts and member_branches
     */
    private function migrateLegacyData(): void
    {
        try {
            if (!$this->db->tableExists('members') || !$this->db->tableExists('member_contacts') || !$this->db->tableExists('member_branches')) {
                return;
            }

            $members = $this->db->table('members')->get()->getResult();
            if (empty($members)) {
                return;
            }

            $now = date('Y-m-d H:i:s');
            foreach ($members as $m) {
                // Check if contact already exists for this member
                $existingContactsCount = $this->db->table('member_contacts')
                    ->where('company_id', $m->id)
                    ->countAllResults();

                if ($existingContactsCount === 0) {
                    $hasContactInfo = !empty($m->representative_name) || !empty($m->phone) || !empty($m->email);
                    if ($hasContactInfo) {
                        $fullName = !empty($m->representative_name) ? trim((string)$m->representative_name) : (trim((string)$m->company_name) . ' Representative');
                        $this->db->table('member_contacts')->insert([
                            'company_id'   => $m->id,
                            'full_name'    => $fullName,
                            'position'     => $m->position ?? null,
                            'phone'        => $m->phone ?? null,
                            'email'        => $m->email ?? null,
                            'is_primary'   => 1,
                            'created_at'   => $m->created_at ?? $now,
                            'updated_at'   => $m->updated_at ?? $now,
                        ]);
                        $contactId = $this->db->insertID();

                        // Link existing cards of this member to this primary contact
                        if ($this->db->tableExists('member_cards') && in_array('contact_id', $this->db->getFieldNames('member_cards'), true)) {
                            $this->db->table('member_cards')
                                ->where('member_id', $m->id)
                                ->where('contact_id IS NULL')
                                ->update(['contact_id' => $contactId]);
                        }
                    }
                }

                // Check if branch already exists for this member
                $existingBranchesCount = $this->db->table('member_branches')
                    ->where('company_id', $m->id)
                    ->countAllResults();

                if ($existingBranchesCount === 0) {
                    // Create headquarters branch if address or city exists
                    if (!empty($m->address) || !empty($m->city)) {
                        $this->db->table('member_branches')->insert([
                            'company_id'      => $m->id,
                            'branch_name'     => 'Trụ sở chính',
                            'country'         => 'Vietnam',
                            'city'            => $m->city ?? null,
                            'address'         => $m->address ?? null,
                            'phone'           => $m->phone ?? null,
                            'email'           => $m->email ?? null,
                            'is_headquarters' => 1,
                            'created_at'      => $m->created_at ?? $now,
                            'updated_at'      => $m->updated_at ?? $now,
                        ]);
                    }
                }
            }
        } catch (\Throwable $e) {
            log_message('error', 'Legacy member migration warning: ' . $e->getMessage());
        }
    }
}
