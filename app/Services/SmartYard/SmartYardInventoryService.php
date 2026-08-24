<?php

namespace App\Services\SmartYard;

use App\Models\SmartYard\SmartYardWarehouseModel;
use App\Models\SmartYard\SmartYardLotModel;
use App\Models\SmartYard\SmartYardTransactionModel;
use App\Models\SmartYard\SmartYardAuditModel;
use App\Models\SmartYard\SmartYardConfigModel;
use Config\Database;
use Exception;

/**
 * SmartYardInventoryService
 * High-concurrency atomic engine for lot Import, Export and Area reconciliations
 */
class SmartYardInventoryService
{
    protected $warehouseModel;
    protected $lotModel;
    protected $transactionModel;
    protected $auditModel;
    protected $configModel;
    protected $rbacService;
    protected $db;

    public function __construct()
    {
        $this->warehouseModel = new SmartYardWarehouseModel();
        $this->lotModel = new SmartYardLotModel();
        $this->transactionModel = new SmartYardTransactionModel();
        $this->auditModel = new SmartYardAuditModel();
        $this->configModel = new SmartYardConfigModel();
        $this->rbacService = new SmartYardRbacService();
        $this->db = Database::connect();
    }

    /**
     * Import a new lot into a warehouse (Atomic transaction)
     */
    public function importLot(object $user, array $data): array
    {
        $warehouseId = (int)($data['warehouse_id'] ?? 0);
        $projectId = (int)($data['project_id'] ?? 0);
        $lotCode = trim($data['lot_code'] ?? '');
        $itemName = trim($data['item_name'] ?? '');
        $area = (float)($data['area'] ?? 0);
        $notes = trim($data['notes'] ?? '');

        // 1. Validate permissions
        if (!$this->rbacService->canAccessWarehouse($user, $warehouseId, 'import')) {
            return ['status' => false, 'message' => 'Bạn không có quyền nhập hàng vào kho này.'];
        }

        if (empty($lotCode) || empty($itemName) || $area <= 0) {
            return ['status' => false, 'message' => 'Vui lòng cung cấp đầy đủ thông tin mã lô, tên hàng và diện tích > 0.'];
        }

        // 2. Check duplicate lot code
        $existingLot = $this->lotModel->getByCode($lotCode);
        if ($existingLot) {
            return ['status' => false, 'message' => 'Mã lô hàng ' . $lotCode . ' đã tồn tại trong hệ thống.'];
        }

        // 3. Begin Atomic Database Transaction
        $this->db->transStart();
        try {
            // Lock warehouse row for update
            $warehouse = $this->db->table('smartyard_warehouses')
                ->where('id', $warehouseId)
                ->get()
                ->getRow();

            if (!$warehouse || $warehouse->status === 'inactive') {
                $this->db->transRollback();
                return ['status' => false, 'message' => 'Kho không tồn tại hoặc đang ngưng hoạt động.'];
            }

            $allocated = (float)$warehouse->allocated_area;
            $usedBefore = (float)$warehouse->used_area;
            $available = max(0, $allocated - $usedBefore);

            // Rule 02: Do not exceed allocated area
            $allowOver = (bool)(int)$this->configModel->getValue('allow_over_allocation', '0');
            if (!$allowOver && ($usedBefore + $area > $allocated)) {
                $this->db->transRollback();
                return [
                    'status' => false, 
                    'message' => 'Diện tích nhập (' . $area . 'm²) vượt quá diện tích khả dụng còn lại của kho (' . $available . 'm²).'
                ];
            }

            $usedAfter = $usedBefore + $area;

            // 4. Create Lot Record
            $lotId = $this->lotModel->insert([
                'warehouse_id' => $warehouseId,
                'project_id' => $projectId,
                'lot_code' => $lotCode,
                'item_name' => $itemName,
                'initial_area' => $area,
                'remaining_area' => $area,
                'status' => 'STORED',
                'imported_at' => date('Y-m-d H:i:s'),
                'notes' => $notes,
                'file_attachment' => $data['file_attachment'] ?? null
            ]);

            // 5. Update Warehouse used_area
            $this->warehouseModel->update($warehouseId, ['used_area' => $usedAfter]);

            // 6. Record Immutable Transaction Log
            $txId = $this->transactionModel->insert([
                'warehouse_id' => $warehouseId,
                'lot_id' => $lotId,
                'project_id' => $projectId,
                'user_id' => (int)$user->id,
                'transaction_type' => 'IMPORT',
                'area' => $area,
                'warehouse_used_before' => $usedBefore,
                'warehouse_used_after' => $usedAfter,
                'lot_remaining_before' => 0.00,
                'lot_remaining_after' => $area,
                'note' => $notes,
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
            ]);

            // 7. Audit Log
            $this->auditModel->insert([
                'user_id' => (int)$user->id,
                'action' => 'IMPORT_LOT',
                'object_type' => 'LOT',
                'object_id' => (string)$lotId,
                'after_data' => json_encode(['lot_code' => $lotCode, 'area' => $area, 'warehouse_id' => $warehouseId]),
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
            ]);

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return ['status' => false, 'message' => 'Lỗi hệ thống khi thực hiện giao dịch nhập kho.'];
            }

            return [
                'status' => true,
                'message' => 'Nhập kho thành công lô hàng ' . $lotCode . ' (' . $area . 'm²).',
                'lot_id' => $lotId,
                'transaction_id' => $txId,
                'used_area' => $usedAfter,
                'available_area' => max(0, $allocated - $usedAfter)
            ];
        } catch (Exception $e) {
            $this->db->transRollback();
            return ['status' => false, 'message' => 'Lỗi ngoại lệ: ' . $e->getMessage()];
        }
    }

    /**
     * Export a lot partially or fully from warehouse (Atomic transaction)
     */
    public function exportLot(object $user, array $data): array
    {
        $lotId = (int)($data['lot_id'] ?? 0);
        $exportArea = (float)($data['export_area'] ?? 0);
        $notes = trim($data['notes'] ?? '');

        if ($lotId <= 0 || $exportArea <= 0) {
            return ['status' => false, 'message' => 'Vui lòng chọn lô hàng và nhập diện tích xuất > 0.'];
        }

        $lot = $this->lotModel->find($lotId);
        if (!$lot) {
            return ['status' => false, 'message' => 'Không tìm thấy thông tin lô hàng.'];
        }

        // 1. Validate permissions
        if (!$this->rbacService->canAccessWarehouse($user, (int)$lot->warehouse_id, 'export')) {
            return ['status' => false, 'message' => 'Bạn không có quyền xuất hàng khỏi kho này.'];
        }

        // Rule 03: Do not export more than remaining area
        $lotRemainingBefore = (float)$lot->remaining_area;
        if ($exportArea > $lotRemainingBefore) {
            return [
                'status' => false,
                'message' => 'Diện tích xuất (' . $exportArea . 'm²) vượt quá diện tích còn lại của lô (' . $lotRemainingBefore . 'm²).'
            ];
        }

        $this->db->transStart();
        try {
            $warehouse = $this->db->table('smartyard_warehouses')
                ->where('id', $lot->warehouse_id)
                ->get()
                ->getRow();

            $usedBefore = (float)$warehouse->used_area;
            $usedAfter = max(0, $usedBefore - $exportArea);
            $lotRemainingAfter = max(0, $lotRemainingBefore - $exportArea);
            $newStatus = ($lotRemainingAfter <= 0) ? 'EXPORTED' : 'PARTIAL';

            // Update Lot
            $this->lotModel->update($lotId, [
                'remaining_area' => $lotRemainingAfter,
                'status' => $newStatus
            ]);

            // Update Warehouse used area
            $this->warehouseModel->update($lot->warehouse_id, ['used_area' => $usedAfter]);

            // Record Transaction
            $txId = $this->transactionModel->insert([
                'warehouse_id' => (int)$lot->warehouse_id,
                'lot_id' => $lotId,
                'project_id' => (int)$lot->project_id,
                'user_id' => (int)$user->id,
                'transaction_type' => 'EXPORT',
                'area' => $exportArea,
                'warehouse_used_before' => $usedBefore,
                'warehouse_used_after' => $usedAfter,
                'lot_remaining_before' => $lotRemainingBefore,
                'lot_remaining_after' => $lotRemainingAfter,
                'note' => $notes,
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
            ]);

            // Audit Log
            $this->auditModel->insert([
                'user_id' => (int)$user->id,
                'action' => 'EXPORT_LOT',
                'object_type' => 'LOT',
                'object_id' => (string)$lotId,
                'after_data' => json_encode(['lot_code' => $lot->lot_code, 'export_area' => $exportArea, 'remaining' => $lotRemainingAfter]),
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
            ]);

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return ['status' => false, 'message' => 'Lỗi hệ thống khi thực hiện giao dịch xuất kho.'];
            }

            return [
                'status' => true,
                'message' => 'Xuất kho thành công ' . $exportArea . 'm² từ lô ' . $lot->lot_code . '.',
                'lot_status' => $newStatus,
                'lot_remaining' => $lotRemainingAfter,
                'warehouse_used' => $usedAfter,
                'transaction_id' => $txId
            ];
        } catch (Exception $e) {
            $this->db->transRollback();
            return ['status' => false, 'message' => 'Lỗi ngoại lệ: ' . $e->getMessage()];
        }
    }
}
