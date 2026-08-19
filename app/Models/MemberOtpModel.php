<?php

namespace App\Models;

use CodeIgniter\Model;

class MemberOtpModel extends Model
{
    protected $table            = 'member_otps';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $allowedFields    = ['email', 'otp_code', 'action', 'expires_at', 'is_used', 'created_at'];
    protected $useTimestamps    = false;

    public function ensureTableExists()
    {
        $db = \Config\Database::connect();
        $forge = \Config\Database::forge();
        if (!$db->tableExists($this->table)) {
            $forge->addField([
                'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'email'      => ['type' => 'VARCHAR', 'constraint' => 150],
                'otp_code'   => ['type' => 'VARCHAR', 'constraint' => 10],
                'action'     => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'register'],
                'expires_at' => ['type' => 'DATETIME'],
                'is_used'    => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $forge->addKey('id', true);
            $forge->addKey(['email', 'action']);
            $forge->addKey('otp_code');
            $forge->addKey('expires_at');
            $forge->createTable($this->table, true);
        }
    }

    public function generateOtp(string $email, string $action = 'register'): string
    {
        $this->ensureTableExists();
        $code = sprintf('%06d', mt_rand(100000, 999999));
        $now = date('Y-m-d H:i:s');
        $expires = date('Y-m-d H:i:s', strtotime('+5 minutes'));

        $db = \Config\Database::connect();
        $db->table($this->table)
            ->where('email', $email)
            ->where('action', $action)
            ->where('is_used', 0)
            ->update(['is_used' => 1]);

        $db->table($this->table)->insert([
            'email'      => $email,
            'otp_code'   => $code,
            'action'     => $action,
            'expires_at' => $expires,
            'is_used'    => 0,
            'created_at' => $now,
        ]);

        return $code;
    }

    public function verifyOtp(string $email, string $otp, string $action = 'register'): bool
    {
        $this->ensureTableExists();
        $now = date('Y-m-d H:i:s');
        $db = \Config\Database::connect();
        $row = $db->table($this->table)
                    ->where('email', $email)
                    ->where('otp_code', trim($otp))
                    ->where('action', $action)
                    ->where('is_used', 0)
                    ->where('expires_at >=', $now)
                    ->orderBy('id', 'DESC')
                    ->get()
                    ->getRow();

        if ($row) {
            $db->table($this->table)->where('id', $row->id)->update(['is_used' => 1]);
            return true;
        }
        return false;
    }

    public function canResendOtp(string $email, string $action = 'register'): bool
    {
        $this->ensureTableExists();
        $threshold = date('Y-m-d H:i:s', strtotime('-60 seconds'));
        $db = \Config\Database::connect();
        $lastOtp = $db->table($this->table)
                        ->where('email', $email)
                        ->where('action', $action)
                        ->where('created_at >=', $threshold)
                        ->orderBy('id', 'DESC')
                        ->get()
                        ->getRow();
        return empty($lastOtp);
    }
}
