<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration: Create TOP BEST GLOBAL Core Award Tables & High-Concurrency Indexes
 * Handles Seasons, Categories, Nomination Candidates, Voting OTPs, Audit Logs, and Jury Evaluations.
 */
class CreateTopBestGlobalAwardsSchema extends Migration
{
    public function up()
    {
        // 1. Table: tb_award_seasons
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'title' => ['type' => 'VARCHAR', 'constraint' => 255],
            'slug' => ['type' => 'VARCHAR', 'constraint' => 255],
            'theme_year' => ['type' => 'INT', 'constraint' => 4, 'default' => 2026],
            'description' => ['type' => 'TEXT', 'null' => true],
            'banner_image' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'nomination_start_at' => ['type' => 'DATETIME', 'null' => true],
            'nomination_end_at' => ['type' => 'DATETIME', 'null' => true],
            'voting_start_at' => ['type' => 'DATETIME', 'null' => true],
            'voting_end_at' => ['type' => 'DATETIME', 'null' => true],
            'gala_date' => ['type' => 'DATETIME', 'null' => true],
            'status' => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'draft'],
            'is_active' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('slug');
        $this->forge->addKey(['status', 'is_active']);
        $this->forge->addKey('theme_year');
        $this->forge->createTable('tb_award_seasons', true);

        // 2. Table: tb_award_categories
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'season_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'default' => 1],
            'parent_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'default' => 0],
            'name' => ['type' => 'VARCHAR', 'constraint' => 255],
            'slug' => ['type' => 'VARCHAR', 'constraint' => 255],
            'industry_sector' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'description' => ['type' => 'TEXT', 'null' => true],
            'icon' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'order_num' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'jury_weight' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => 70.00],
            'public_weight' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => 30.00],
            'status' => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'active'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['season_id', 'status', 'order_num']);
        $this->forge->addKey('slug');
        $this->forge->addKey('industry_sector');
        $this->forge->createTable('tb_award_categories', true);

        // 3. Table: tb_nomination_candidates
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'season_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'default' => 1],
            'category_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'candidate_code' => ['type' => 'VARCHAR', 'constraint' => 64],
            'name' => ['type' => 'VARCHAR', 'constraint' => 255],
            'organization_name' => ['type' => 'VARCHAR', 'constraint' => 255],
            'slug' => ['type' => 'VARCHAR', 'constraint' => 255],
            'candidate_type' => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'enterprise'],
            'avatar' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'cover_image' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'bio_summary' => ['type' => 'TEXT', 'null' => true],
            'dossier_content' => ['type' => 'LONGTEXT', 'null' => true],
            'tax_code' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'contact_person' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'contact_email' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'contact_phone' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'website' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'stage' => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'preliminary'],
            'public_votes_count' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'default' => 0],
            'jury_score_avg' => ['type' => 'DECIMAL', 'constraint' => '6,2', 'default' => 0.00],
            'composite_score' => ['type' => 'DECIMAL', 'constraint' => '6,2', 'default' => 0.00],
            'final_rank' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'award_title' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'certificate_serial' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'digital_badge_url' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'is_featured' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'status' => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'approved'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('candidate_code');
        $this->forge->addKey(['season_id', 'category_id', 'stage', 'status']);
        $this->forge->addKey(['category_id', 'composite_score']);
        $this->forge->addKey(['category_id', 'public_votes_count']);
        $this->forge->addKey(['stage', 'is_featured']);
        $this->forge->addKey('slug');
        $this->forge->addKey('certificate_serial');
        $this->forge->createTable('tb_nomination_candidates', true);

        // 4. Table: tb_voting_otps
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'auto_increment' => true],
            'email' => ['type' => 'VARCHAR', 'constraint' => 255],
            'otp_code' => ['type' => 'VARCHAR', 'constraint' => 10],
            'candidate_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'token' => ['type' => 'VARCHAR', 'constraint' => 100],
            'ip_address' => ['type' => 'VARCHAR', 'constraint' => 45],
            'user_agent' => ['type' => 'TEXT', 'null' => true],
            'device_fingerprint' => ['type' => 'VARCHAR', 'constraint' => 64, 'null' => true],
            'is_verified' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'verified_at' => ['type' => 'DATETIME', 'null' => true],
            'expires_at' => ['type' => 'DATETIME'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('token');
        $this->forge->addKey(['email', 'candidate_id', 'is_verified']);
        $this->forge->addKey(['ip_address', 'created_at']);
        $this->forge->addKey(['device_fingerprint', 'created_at']);
        $this->forge->addKey('expires_at');
        $this->forge->createTable('tb_voting_otps', true);

        // 5. Table: tb_voting_audit_logs
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'auto_increment' => true],
            'candidate_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'season_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'default' => 1],
            'category_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'voter_email' => ['type' => 'VARCHAR', 'constraint' => 255],
            'otp_id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => true],
            'ip_address' => ['type' => 'VARCHAR', 'constraint' => 45],
            'device_fingerprint' => ['type' => 'VARCHAR', 'constraint' => 64, 'null' => true],
            'user_agent' => ['type' => 'TEXT', 'null' => true],
            'risk_score' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'verification_status' => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'verified'],
            'integrity_hash' => ['type' => 'VARCHAR', 'constraint' => 64],
            'created_at' => ['type' => 'DATETIME'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['candidate_id', 'created_at']);
        $this->forge->addKey(['voter_email', 'candidate_id']);
        $this->forge->addKey(['season_id', 'category_id']);
        $this->forge->addKey(['ip_address', 'created_at']);
        $this->forge->addKey(['device_fingerprint', 'created_at']);
        $this->forge->addKey('integrity_hash');
        $this->forge->createTable('tb_voting_audit_logs', true);

        // 6. Table: tb_jury_evaluations
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'auto_increment' => true],
            'candidate_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'season_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'default' => 1],
            'category_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'jury_user_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'criteria_1_score' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => 0.00],
            'criteria_2_score' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => 0.00],
            'criteria_3_score' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => 0.00],
            'criteria_4_score' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => 0.00],
            'total_score' => ['type' => 'DECIMAL', 'constraint' => '6,2', 'default' => 0.00],
            'notes' => ['type' => 'TEXT', 'null' => true],
            'is_submitted' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'submitted_at' => ['type' => 'DATETIME', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['candidate_id', 'jury_user_id']);
        $this->forge->addKey(['season_id', 'category_id']);
        $this->forge->addKey(['jury_user_id', 'is_submitted']);
        $this->forge->createTable('tb_jury_evaluations', true);
    }

    public function down()
    {
        $this->forge->dropTable('tb_jury_evaluations', true);
        $this->forge->dropTable('tb_voting_audit_logs', true);
        $this->forge->dropTable('tb_voting_otps', true);
        $this->forge->dropTable('tb_nomination_candidates', true);
        $this->forge->dropTable('tb_award_categories', true);
        $this->forge->dropTable('tb_award_seasons', true);
    }
}
