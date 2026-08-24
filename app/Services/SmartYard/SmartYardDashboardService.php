<?php

namespace App\Services\SmartYard;

use App\Models\SmartYard\SmartYardWarehouseModel;
use App\Models\SmartYard\SmartYardRegionModel;
use App\Models\SmartYard\SmartYardProjectModel;
use App\Models\SmartYard\SmartYardTransactionModel;
use App\Models\SmartYard\SmartYardLotModel;

/**
 * SmartYardDashboardService
 * Computes fast executive metrics, project allocations, and warehouse utilization status
 */
class SmartYardDashboardService
{
    protected $warehouseModel;
    protected $regionModel;
    protected $projectModel;
    protected $transactionModel;
    protected $lotModel;
    protected $warehouseService;

    public function __construct()
    {
        $this->warehouseModel = new SmartYardWarehouseModel();
        $this->regionModel = new SmartYardRegionModel();
        $this->projectModel = new SmartYardProjectModel();
        $this->transactionModel = new SmartYardTransactionModel();
        $this->lotModel = new SmartYardLotModel();
        $this->warehouseService = new SmartYardWarehouseService();
    }

    /**
     * Compute full dashboard summary
     */
    public function getExecutiveMetrics(int $userId, bool $isSuperAdmin = false): array
    {
        $scopedWarehouses = $this->warehouseModel->getScopedWarehouses($userId, $isSuperAdmin);

        $totalWarehouses = count($scopedWarehouses);
        $totalArea = 0.00;
        $allocatedArea = 0.00;
        $usedArea = 0.00;

        $statusCount = [
            'LOW' => 0,
            'MEDIUM' => 0,
            'HIGH' => 0,
            'FULL' => 0
        ];

        foreach ($scopedWarehouses as $wh) {
            $computed = $this->warehouseService->computeWarehouseState($wh);
            $totalArea += (float)$wh->total_area;
            $allocatedArea += (float)$wh->allocated_area;
            $usedArea += (float)$wh->used_area;

            if (isset($statusCount[$computed->status_level])) {
                $statusCount[$computed->status_level]++;
            }
        }

        $availableArea = max(0, $allocatedArea - $usedArea);
        $overallUsageRate = $allocatedArea > 0 ? round(($usedArea / $allocatedArea) * 100, 1) : 0.0;

        $regions = $this->regionModel->getActiveRegionsWithStats();
        $projects = $this->projectModel->getProjectsWithStats();
        $recentTransactions = $this->transactionModel->getTransactionsList([], 10, 0);

        return [
            'summary' => [
                'total_regions' => count($regions),
                'total_warehouses' => $totalWarehouses,
                'total_area' => $totalArea,
                'allocated_area' => $allocatedArea,
                'used_area' => $usedArea,
                'available_area' => $availableArea,
                'overall_usage_rate' => $overallUsageRate
            ],
            'status_distribution' => $statusCount,
            'regions' => $regions,
            'projects' => $projects,
            'recent_transactions' => $recentTransactions
        ];
    }
}
