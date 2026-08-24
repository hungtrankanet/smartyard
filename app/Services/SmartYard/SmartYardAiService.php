<?php

namespace App\Services\SmartYard;

use App\Models\SmartYard\SmartYardWarehouseModel;
use App\Models\SmartYard\SmartYardLotModel;
use App\Services\SmartYard\SmartYardWarehouseService;
use App\Services\SmartYard\SmartYardRbacService;
use Config\Database;

/**
 * SmartYardAiService
 * Intelligent query assistant with strict RBAC Context-Filtering & Suggestion engine
 */
class SmartYardAiService
{
    protected $warehouseModel;
    protected $lotModel;
    protected $warehouseService;
    protected $rbacService;
    protected $db;

    public function __construct()
    {
        $this->warehouseModel = new SmartYardWarehouseModel();
        $this->lotModel = new SmartYardLotModel();
        $this->warehouseService = new SmartYardWarehouseService();
        $this->rbacService = new SmartYardRbacService();
        $this->db = Database::connect();
    }

    /**
     * Process query from user strictly scoped to permitted warehouses
     */
    public function query(object $user, string $queryText): array
    {
        $cleanQuery = mb_strtolower(trim($queryText), 'UTF-8');
        $isSuperAdmin = $this->rbacService->isSuperAdmin($user);
        $allowedWhIds = $this->rbacService->getAllowedWarehouseIds($user, 'view');

        // Check if query targets a specific warehouse by code or name
        $allWarehouses = $this->warehouseModel->findAll();
        $targetedWh = null;
        foreach ($allWarehouses as $wh) {
            if (mb_strpos($cleanQuery, mb_strtolower($wh->code, 'UTF-8')) !== false ||
                mb_strpos($cleanQuery, mb_strtolower($wh->name, 'UTF-8')) !== false) {
                $targetedWh = $wh;
                break;
            }
        }

        // Rule 07 & Section 22: Enforce RBAC Scope Guardrail
        if ($targetedWh && !$isSuperAdmin && $allowedWhIds !== ['*'] && !in_array((int)$targetedWh->id, $allowedWhIds)) {
            $this->logAiConversation((int)$user->id, $queryText, 'Bạn không có quyền truy cập dữ liệu của kho này.', true);
            return [
                'status' => false,
                'violation' => true,
                'response' => 'Bạn không có quyền truy cập dữ liệu của kho ' . $targetedWh->code . ' (' . $targetedWh->name . '). Vui lòng liên hệ Quản trị viên để được cấp scope.',
                'suggestions' => []
            ];
        }

        // Load only permitted warehouses into context
        $scopedWarehouses = $this->warehouseModel->getScopedWarehouses((int)$user->id, $isSuperAdmin);
        $whStates = [];
        foreach ($scopedWarehouses as $wh) {
            $whStates[] = $this->warehouseService->computeWarehouseState($wh);
        }

        // Handle specific question intents
        $response = '';
        $suggestions = [];

        if (mb_strpos($cleanQuery, 'nhiều diện tích nhất') !== false || mb_strpos($cleanQuery, 'trống nhất') !== false) {
            usort($whStates, function($a, $b) {
                return $b->available_area <=> $a->available_area;
            });
            $top = $whStates[0] ?? null;
            if ($top) {
                $response = "Kho còn nhiều diện tích khả dụng nhất trong phạm vi của bạn là **{$top->name} ({$top->code})** với **" . number_format($top->available_area, 0) . "m²** còn trống (Mức sử dụng: {$top->usage_rate}%).";
                $suggestions[] = "Xem chi tiết kho {$top->code}";
            }
        } elseif (mb_strpos($cleanQuery, 'gần đầy') !== false || mb_strpos($cleanQuery, 'cao nhất') !== false || mb_strpos($cleanQuery, 'đầy') !== false) {
            $highWh = array_filter($whStates, function($w) {
                return in_array($w->status_level, ['HIGH', 'FULL']);
            });
            if (!empty($highWh)) {
                $list = array_map(function($w) {
                    return "- **{$w->name} ({$w->code})**: Đã dùng " . number_format($w->used_area, 0) . "/{$w->allocated_area}m² ({$w->usage_rate}% - {$w->status_level})";
                }, $highWh);
                $response = "Danh sách các kho đang ở mức sử dụng cao hoặc gần đầy:\n" . implode("\n", $list);
                $suggestions[] = "Đề xuất kế hoạch xuất hàng hoặc chuyển vùng";
            } else {
                $response = "Hiện tại không có kho nào trong phạm vi quản lý của bạn ở mức cảnh báo đầy (>80%).";
            }
        } elseif (preg_match('/(\d+)\s*(m2|m²)/ui', $cleanQuery, $matches)) {
            // Suggest warehouse for specific required area
            $neededArea = (float)$matches[1];
            $candidates = array_filter($whStates, function($w) use ($neededArea) {
                return $w->available_area >= $neededArea;
            });
            usort($candidates, function($a, $b) {
                return $a->available_area <=> $b->available_area; // pick best fit
            });

            if (!empty($candidates)) {
                $best = $candidates[0];
                $response = "Lô hàng diện tích **{$neededArea}m²** có thể phù hợp nhất với **{$best->name} ({$best->code})** (Còn trống " . number_format($best->available_area, 0) . "m² / Tổng cấp {$best->allocated_area}m²).";
                $suggestions[] = "Chuyển tới màn hình Nhập kho {$best->code}";
            } else {
                $response = "Không có kho nào trong phạm vi quyền hạn còn đủ diện tích trống **{$neededArea}m²**.";
            }
        } else {
            // General status overview
            $totalAvail = array_sum(array_column($whStates, 'available_area'));
            $totalUsed = array_sum(array_column($whStates, 'used_area'));
            $totalAlloc = array_sum(array_column($whStates, 'allocated_area'));
            $avgUsage = $totalAlloc > 0 ? round(($totalUsed / $totalAlloc) * 100, 1) : 0;

            $response = "Tổng quan kho bạn được phân quyền (" . count($whStates) . " kho):\n" .
                "- Tổng diện tích được cấp: **" . number_format($totalAlloc, 0) . "m²**\n" .
                "- Đã sử dụng: **" . number_format($totalUsed, 0) . "m²** ({$avgUsage}%)\n" .
                "- Còn khả dụng: **" . number_format($totalAvail, 0) . "m²**";
            $suggestions[] = "Kho nào còn nhiều diện tích nhất?";
            $suggestions[] = "Kho nào đang gần đầy?";
        }

        $this->logAiConversation((int)$user->id, $queryText, $response, false);

        return [
            'status' => true,
            'violation' => false,
            'response' => $response,
            'suggestions' => $suggestions
        ];
    }

    protected function logAiConversation(int $userId, string $query, string $response, bool $isViolation): void
    {
        $this->db->table('smartyard_ai_conversations')->insert([
            'user_id' => $userId,
            'query_text' => $query,
            'response_text' => $response,
            'is_scope_violation' => $isViolation ? 1 : 0,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
}
