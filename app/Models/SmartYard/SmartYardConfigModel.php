<?php

namespace App\Models\SmartYard;

use CodeIgniter\Model;

/**
 * SmartYardConfigModel
 * Manages dynamic runtime thresholds, alerts, and system settings
 */
class SmartYardConfigModel extends Model
{
    protected $table = 'smartyard_config';
    protected $primaryKey = 'id';
    protected $returnType = 'object';
    protected $allowedFields = [
        'config_key',
        'config_value',
        'description'
    ];
    protected $useTimestamps = true;
    protected $updatedField = 'updated_at';

    /**
     * Get config value with fallback
     */
    public function getValue(string $key, string $default = '')
    {
        $item = $this->where('config_key', $key)->first();
        return $item ? $item->config_value : $default;
    }

    /**
     * Set config value
     */
    public function setValue(string $key, string $value, string $description = '')
    {
        $existing = $this->where('config_key', $key)->first();
        if ($existing) {
            return $this->update($existing->id, ['config_value' => $value]);
        }
        return $this->insert([
            'config_key' => $key,
            'config_value' => $value,
            'description' => $description
        ]);
    }
}
