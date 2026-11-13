<?php

namespace App\Models;

use CodeIgniter\Model;

class AdminAuditLogModel extends Model
{
    protected $table = 'audit_logs';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'user_id',
        'action',
        'entity_type',
        'entity_id',
        'details',
        'ip_address',
    ];
    protected $useTimestamps = true;

    public function record(
        ?int $actorId,
        string $action,
        string $entityType,
        int $entityId,
        string $details,
        ?string $ipAddress,
    ): bool {
        return $this->insert([
            'user_id'    => $actorId,
            'action'     => $action,
            'entity_type' => $entityType,
            'entity_id'  => $entityId,
            'details'    => $details,
            'ip_address' => $ipAddress,
        ]) !== false;
    }
}
