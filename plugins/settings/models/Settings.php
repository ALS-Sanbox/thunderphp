<?php
// models/Settings.php

namespace Setting;

use \Model\Model;

defined('ROOT') or die("Direct script access denied");

class Settings extends Model {
    protected $table = 'settings';

    protected $allowedColumns = [
        'key', 'value', 'type', 'environment', 'updated_at',
    ];

    protected $allowedUpdateColumns = [
        'key', 'value', 'type', 'environment', 'updated_at',
    ];

    /**
     * Get all settings as key => ['type' => ..., 'id' => ...]
     */
    public function getAllSettings(): array {
        $rows = $this->query("SELECT id, `key`, `type` FROM {$this->table}");
        $settings = [];

        if ($rows) {
            foreach ($rows as $row) {
                $settings[$row->key] = [
                    'type' => $row->type,
                    'id'   => $row->id,
                ];
            }
        }

        return $settings;
    }
/**
 * Validate POST data against known setting keys and types
 */
public function validate_settings_data(array $post): bool {
    $this->errors = []; // Reset errors
    $settings = $this->getAllSettings();

    foreach ($post as $key => $value) {
        if ($key === '_token') continue;

        if (!isset($settings[$key])) {
            $this->errors[$key] = "Unknown setting key: $key";
            continue;
        }

        $type = $settings[$key]['type'];

        if (!$this->isValidValue($value, $type)) {
            $this->errors[$key] = "Invalid value for '$key' (expected $type)";
        }
    }

    return empty($this->errors);
}

/**
 * Check if a value matches the expected type
 */
protected function isValidValue(mixed $value, string $type): bool {
    if ($value === '' || $value === null) return false; // explicitly disallow empty

    return match ($type) {
        'int'   => filter_var($value, FILTER_VALIDATE_INT) !== false,
        'float' => filter_var($value, FILTER_VALIDATE_FLOAT) !== false,
        'bool'  => in_array($value, ['0', '1', 0, 1, true, false, 'true', 'false'], true),
        'json'  => is_array(json_decode($value, true)),
        'string' => is_scalar($value),
        default => false,
    };
}
    /**
     * Update settings based on POST data
     */
    public function update_settings(array $post): bool {
        $settings = $this->getAllSettings();

        foreach ($post as $key => $value) {
            if ($key === '_token') continue;
            if (!isset($settings[$key])) {
                $this->errors[$key] = "Unknown setting key: $key";
                continue;
            }

            $type = $settings[$key]['type'];
            $id   = $settings[$key]['id'];
            $casted = $this->castValue($value, $type);

            if ($casted === null && $value !== '0') {
                $this->errors[$key] = "Invalid value for $key (expected $type)";
                continue;
            }

            // Update using inherited `update` method
            $this->update($id, [
                'value'      => (string) $casted,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        return empty($this->errors);
    }

    /**
     * Validate and type-cast value based on its expected type
     */
    protected function castValue(mixed $value, string $type): mixed {
        return match ($type) {
            'int'   => filter_var($value, FILTER_VALIDATE_INT),
            'float' => filter_var($value, FILTER_VALIDATE_FLOAT),
            'bool'  => in_array($value, ['1', 1, 'true', true], true) ? 1 : 0,
            'json'  => json_encode(json_decode($value, true)) ?: null,
            'string' => (string) $value,
            default => null,
        };
    }
}
