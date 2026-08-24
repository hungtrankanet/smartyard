<?php

namespace App\Services\SmartYard;

use App\Models\SmartYard\SmartYardWarehouseModel;
use App\Models\SmartYard\SmartYardRegionModel;
use App\Models\SmartYard\SmartYardConfigModel;

/**
 * SmartYardWarehouseService
 * Handles warehouse business calculations, status color thresholds, and map formatting
 */
class SmartYardWarehouseService
{
    protected $warehouseModel;
    protected $regionModel;
    protected $configModel;

    public function __construct()
    {
        $this->warehouseModel = new SmartYardWarehouseModel();
        $this->regionModel = new SmartYardRegionModel();
        $this->configModel = new SmartYardConfigModel();
    }

    /**
     * Compute usage rate and status color for a warehouse
     */
    public function computeWarehouseState(object $warehouse): object
    {
        $wh = clone $warehouse;
        $allocated = (float)($wh->allocated_area ?? 0);
        $used = (float)($wh->used_area ?? 0);
        $total = (float)($wh->total_area ?? 0);

        $available = max(0, $allocated - $used);
        $wh->available_area = $available;

        // Compute usage percentage
        $usageRate = $allocated > 0 ? round(($used / $allocated) * 100, 2) : 0.00;
        $wh->usage_rate = $usageRate;

        // Dynamic thresholds from config or warehouse override
        $thLow = (float)($wh->threshold_low ?? $this->configModel->getValue('threshold_low', '30'));
        $thMed = (float)($wh->threshold_med ?? $this->configModel->getValue('threshold_med', '60'));
        $thHigh = (float)($wh->threshold_high ?? $this->configModel->getValue('threshold_high', '80'));

        if ($usageRate <= $thLow) {
            $wh->status_level = 'LOW';
            $wh->status_color = '#10B981'; // Green
            $wh->status_badge = 'success';
            $wh->status_label = 'Mức thấp (' . $usageRate . '%)';
        } elseif ($usageRate <= $thMed) {
            $wh->status_level = 'MEDIUM';
            $wh->status_color = '#F59E0B'; // Yellow / Amber
            $wh->status_badge = 'warning';
            $wh->status_label = 'Trung bình (' . $usageRate . '%)';
        } elseif ($usageRate <= $thHigh) {
            $wh->status_level = 'HIGH';
            $wh->status_color = '#F97316'; // Orange
            $wh->status_badge = 'warning';
            $wh->status_label = 'Mức cao (' . $usageRate . '%)';
        } else {
            $wh->status_level = 'FULL';
            $wh->status_color = '#EF4444'; // Red
            $wh->status_badge = 'danger';
            $wh->status_label = 'Gần đầy / Đầy (' . $usageRate . '%)';
        }

        return $wh;
    }

    /**
     * Get all active regions with enriched warehouses for 2D Map rendering
     */
    public function getRegionsMapData(int $userId, bool $isSuperAdmin = false): array
    {
        $regions = $this->regionModel->getActiveRegionsWithStats();
        $scopedWarehouses = $this->warehouseModel->getScopedWarehouses($userId, $isSuperAdmin);

        $warehousesByRegion = [];
        foreach ($scopedWarehouses as $wh) {
            $computedWh = $this->computeWarehouseState($wh);
            $warehousesByRegion[$wh->region_id][] = $computedWh;
        }

        $result = [];
        foreach ($regions as $region) {
            $regionData = (array)$region;
            $regionData['warehouses'] = $warehousesByRegion[$region->id] ?? [];
            $result[] = $regionData;
        }

        return $result;
    }
}
