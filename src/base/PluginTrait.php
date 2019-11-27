<?php
namespace verbb\defaultdashboard\base;

use verbb\defaultdashboard\DefaultDashboard;
use verbb\defaultdashboard\services\Service;

use Craft;
use craft\log\FileTarget;

use yii\log\Logger;

trait PluginTrait
{
    // Static Properties
    // =========================================================================

    public static $plugin;


    // Public Methods
    // =========================================================================

    public function getService()
    {
        return $this->get('service');
    }

    private function _setPluginComponents()
    {
        $this->setComponents([
            'service' => Service::class,
        ]);
    }

    private function _setLogging()
    {
        Craft::getLogger()->dispatcher->targets[] = new FileTarget([
            'logFile' => Craft::getAlias('@storage/logs/default-dashboard.log'),
            'categories' => ['default-dashboard'],
        ]);
    }

    public static function log($message, $attributes = [])
    {
        $settings = DefaultDashboard::$plugin->getSettings();

        if ($attributes) {
            $message = Craft::t('default-dashboard', $message, $attributes);
        }

        // Check if we should log
        if (!$settings->logInfo) {
            return;
        }

        Craft::getLogger()->log($message, Logger::LEVEL_INFO, 'default-dashboard');
    }

    public static function error($message, $attributes = [])
    {
        $settings = DefaultDashboard::$plugin->getSettings();

        if ($attributes) {
            $message = Craft::t('default-dashboard', $message, $attributes);
        }

        // Check if we should log
        if (!$settings->logErrors) {
            return;
        }

        Craft::getLogger()->log($message, Logger::LEVEL_ERROR, 'default-dashboard');
    }

}