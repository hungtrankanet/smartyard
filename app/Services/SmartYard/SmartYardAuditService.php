<?php

namespace App\Services\SmartYard;

use App\Models\SmartYard\SmartYardAuditModel;

/**
 * SmartYardAuditService
 * Centralized audit logging for security, state mutations, and regulatory traceability
 */
class SmartYardAuditService
{
    protected $auditModel;

    public function __construct()
    {
        $this->auditModel = new SmartYardAuditModel();
    }

    /**
     * Record an audit log entry
     */
    public function log(
        ?int $userId,
        string $action,
        string $objectType,
        string $objectId,
        $beforeData = null,
        $afterData = null
    ): int {
        return $this->auditModel->insert([
            'user_id' => $userId,
            'action' => strtoupper($action),
            'object_type' => strtoupper($objectType),
            'object_id' => $objectId,
            'before_data' => is_array($beforeData) || is_object($beforeData) ? json_encode($beforeData, JSON_UNESCAPED_UNICODE) : $beforeData,
            'after_data' => is_array($afterData) || is_object($afterData) ? json_encode($afterData, JSON_UNESCAPED_UNICODE) : $afterData,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'user_agent' => substr($_SERVER['HTTP_USER_AGENT'] ?? 'CLI/System', 0, 250)
        ]);
    }
}
