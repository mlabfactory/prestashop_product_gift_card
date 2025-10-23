<?php
/**
 * Module Configuration
 * 
 * @author mlabfactory <tech@mlabfactory.com>
 */

namespace Mlab\GiftCard\Config;

use Configuration;

class ModuleConfig
{
    const CONFIG_PREFIX = 'MLAB_GIFTCARD_';
    
    const KEY_ENABLED = 'ENABLED';
    const KEY_AMOUNTS = 'AMOUNTS';
    const KEY_VALIDITY_DAYS = 'VALIDITY_DAYS';
    const KEY_EMAIL_TEMPLATE = 'EMAIL_TEMPLATE';

    /**
     * Get configuration value
     */
    public static function get(string $key, $default = null)
    {
        $configKey = self::CONFIG_PREFIX . $key;
        $value = Configuration::get($configKey);
        
        return $value !== false ? $value : $default;
    }

    /**
     * Set configuration value
     */
    public static function set(string $key, $value): bool
    {
        $configKey = self::CONFIG_PREFIX . $key;
        return Configuration::updateValue($configKey, $value);
    }

    /**
     * Delete configuration value
     */
    public static function delete(string $key): bool
    {
        $configKey = self::CONFIG_PREFIX . $key;
        return Configuration::deleteByName($configKey);
    }

    /**
     * Check if module is enabled
     */
    public static function isEnabled(): bool
    {
        return (bool)self::get(self::KEY_ENABLED, true);
    }

    /**
     * Get default amounts
     */
    public static function getDefaultAmounts(): array
    {
        $amounts = self::get(self::KEY_AMOUNTS, '25,50,100,150,200');
        
        return array_map(
            'floatval',
            array_filter(
                array_map('trim', explode(',', $amounts)),
                function($v) { return $v !== ''; }
            )
        );
    }

    /**
     * Get validity period in days
     */
    public static function getValidityDays(): int
    {
        return (int)self::get(self::KEY_VALIDITY_DAYS, 365);
    }

    /**
     * Get all configuration keys
     */
    public static function getAllKeys(): array
    {
        return [
            self::CONFIG_PREFIX . self::KEY_ENABLED,
            self::CONFIG_PREFIX . self::KEY_AMOUNTS,
            self::CONFIG_PREFIX . self::KEY_VALIDITY_DAYS,
            self::CONFIG_PREFIX . self::KEY_EMAIL_TEMPLATE,
        ];
    }

    /**
     * Set default configuration values
     */
    public static function setDefaults(): bool
    {
        return self::set(self::KEY_ENABLED, 1)
            && self::set(self::KEY_AMOUNTS, '25,50,100,150,200')
            && self::set(self::KEY_VALIDITY_DAYS, 365);
    }

    /**
     * Delete all configuration values
     */
    public static function deleteAll(): bool
    {
        $success = true;
        
        foreach (self::getAllKeys() as $key) {
            $success = Configuration::deleteByName($key) && $success;
        }
        
        return $success;
    }
}
